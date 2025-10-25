<?php
// controller/puesto.php

require_once __DIR__ . '/../model/Puesto.php';

class PuestoController
{
  // Título que usará el layout en <title> y <h1>
  public string $page_title = 'Puestos';
  // Nombre de la vista a renderizar dentro de /views/puesto/ (listar.php o form.php)
  public string $view = 'listar';
  // Instancia del modelo (capa de datos)
  private Puesto $puestoModel;

  public function __construct()
  {
    // Creamos el modelo (conexión y métodos SQL listos para usar)
    $this->puestoModel = new Puesto();
  }

  /** LISTAR - ?c=puesto&a=list */
  public function list(): array
  {
    $this->page_title = 'Listado de Puestos';
    $this->view = 'listar';
    return ['puestos' => $this->puestoModel->all()];
  }

  /** FORM NUEVO - ?c=puesto&a=form */
  public function form(): array
  {
    $this->page_title = 'Nuevo Puesto';
    $this->view = 'form';
    return ['errores' => []];
  }

  /** GUARDAR NUEVO (POST) - ?c=puesto&a=save */
  public function save(): array
  {
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

  /** EDITAR (cargar datos) - ?c=puesto&a=edit&id=123 */
  public function edit(): array
  {
    $id = (int)($_GET['id'] ?? 0);
    $puesto = $this->puestoModel->find($id);
    if (!$puesto) {
      die("Puesto no encontrado");
    }
    $this->page_title = 'Editar Puesto';
    $this->view = 'form';
    return ['puesto' => $puesto, 'errores' => []];
  }

  /** ACTUALIZAR (POST) - ?c=puesto&a=update */
  public function update(): array
  {
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

  /** ELIMINAR - ?c=puesto&a=delete&id=123 */
  public function delete(): void
  {
    $id = (int)($_GET['id'] ?? 0);

    if (!$id) {
      echo "<div class='alert alert-danger text-center'>ID inválido.</div>";
      echo "<div class='text-center mt-3'><a class='btn btn-secondary' href='?c=puesto&a=list'>Volver</a></div>";
      return;
    }

    // El modelo ya bloquea si hay empleados asignados (canDelete dentro de delete)
    if (!$this->puestoModel->delete($id)) {
      echo "<div class='alert alert-danger text-center'>
              No se puede eliminar el puesto: está asignado a uno o más empleados.
            </div>";
      echo "<div class='text-center mt-3'>
              <a class='btn btn-secondary' href='?c=puesto&a=list'>Volver</a>
            </div>";
      return;
    }

    header("Location: ?c=puesto&a=list");
    exit;
  }
}
