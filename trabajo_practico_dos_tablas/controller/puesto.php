<?php
// controller/puesto.php

// 🔐 primero traemos las funciones de sesión/roles
require_once __DIR__ . '/../auth.php';

// luego el modelo
require_once __DIR__ . '/../model/Puesto.php';

class PuestoController
{
  public string $page_title = 'Puestos';
  public string $view = 'listar';
  private Puesto $puestoModel;

  public function __construct()
  {
    $this->puestoModel = new Puesto();
  }

  /** LISTAR - ?c=puesto&a=list
   * cualquiera logueado puede ver
   */
  public function list(): array
  {
    requireLogin(); // 🔐 tiene que estar logueado
    $this->page_title = 'Listado de Puestos';
    $this->view = 'listar';
    return ['puestos' => $this->puestoModel->all()];
  }

  /** FORM NUEVO - ?c=puesto&a=form
   * solo admin o editor
   */
  public function form(): array
  {
    requireRole(['admin','editor']); // 🔐
    $this->page_title = 'Nuevo Puesto';
    $this->view = 'form';
    return ['errores' => []];
  }

  /** GUARDAR NUEVO (POST) - ?c=puesto&a=save
   * solo admin o editor
   */
  public function save(): array
  {
    requireRole(['admin','editor']); // 🔐

    $nombre = trim($_POST['nombre'] ?? '');
    $tarea  = trim($_POST['tarea']  ?? '');
    $errores = [];

    if ($nombre === '') {
      $errores[] = 'El nombre del puesto es obligatorio.';
    }

    if ($errores) {
      $this->page_title = 'Nuevo Puesto';
      $this->view = 'form';
      return ['errores' => $errores];
    }

    $ok = $this->puestoModel->create($nombre, $tarea);
    if (!$ok) {
      $errores[] = 'No se pudo crear el puesto. ¿Ya existe uno con ese nombre?';
      $this->page_title = 'Nuevo Puesto';
      $this->view = 'form';
      return ['errores' => $errores];
    }

    header("Location: ?c=puesto&a=list");
    exit;
  }

  /** EDITAR (cargar datos) - ?c=puesto&a=edit&id=123
   * solo admin o editor
   */
  public function edit(): array
  {
    requireRole(['admin','editor']); // 🔐

    $id = (int)($_GET['id'] ?? 0);
    $puesto = $this->puestoModel->find($id);
    if (!$puesto) {
      die("Puesto no encontrado");
    }
    $this->page_title = 'Editar Puesto';
    $this->view = 'form';
    return ['puesto' => $puesto, 'errores' => []];
  }

  /** ACTUALIZAR (POST) - ?c=puesto&a=update
   * solo admin o editor
   */
  public function update(): array
  {
    requireRole(['admin','editor']); // 🔐

    $id     = (int)($_POST['id'] ?? 0);
    $nombre = trim($_POST['nombre'] ?? '');
    $tarea  = trim($_POST['tarea']  ?? '');
    $errores = [];

    if (!$id)           $errores[] = 'ID inválido.';
    if ($nombre === '') $errores[] = 'El nombre es obligatorio.';

    if ($errores) {
      $this->page_title = 'Editar Puesto';
      $this->view = 'form';
      return ['errores' => $errores, 'puesto' => ['id' => $id, 'nombre' => $nombre, 'tarea' => $tarea]];
    }

    $ok = $this->puestoModel->update($id, $nombre, $tarea);
    if (!$ok) {
      $errores[] = 'No se pudo actualizar (posible nombre duplicado).';
      $this->page_title = 'Editar Puesto';
      $this->view = 'form';
      return ['errores' => $errores, 'puesto' => ['id' => $id, 'nombre' => $nombre, 'tarea' => $tarea]];
    }

    header("Location: ?c=puesto&a=list");
    exit;
  }

  /** ELIMINAR - ?c=puesto&a=delete&id=123
   * solo admin
   */
public function delete(): void {
  requireRole(['admin']); // 🔐 SOLO admin puede eliminar

  $id = (int)($_GET['id'] ?? 0);

  if (!$id) {
    echo "<div class='alert alert-danger text-center'>ID inválido.</div>";
    echo "<div class='text-center mt-3'><a class='btn btn-secondary' href='?c=puesto&a=list'>Volver</a></div>";
    return;
  }

  if (!$this->puestoModel->delete($id)) {
    echo "<div class='alert alert-danger text-center'>
            No se puede eliminar el puesto: está asignado a uno o más empleados.
          </div>";
    echo "<div class='text-center mt-3'><a class='btn btn-secondary' href='?c=puesto&a=list'>Volver</a></div>";
    return;
  }

  header("Location: ?c=puesto&a=list");
  exit;
}
}
