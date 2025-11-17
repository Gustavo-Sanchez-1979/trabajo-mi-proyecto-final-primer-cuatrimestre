<?php
// controller/empleado.php

// 🔐 Autenticación y helpers de roles
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../model/Empleado.php';
require_once __DIR__ . '/../model/Puesto.php';

/**
 * Controlador MVC de Empleados.
 */
class EmpleadoController
{
  // Título que usa el layout en <title> y <h1>
  public string $page_title = 'Empleados';

  // Vista por defecto en /views/empleado/
  public string $view = 'listar';

  // Modelos
  public Empleado $empleadoModel;
  public Puesto $puestoModel;

  // Alias por compatibilidad con código previo
  public Empleado $tablaObj;

  public function __construct()
  {
    $this->empleadoModel = new Empleado();
    $this->puestoModel   = new Puesto();
    $this->tablaObj      = $this->empleadoModel;
  }

  /* ===== LISTAR =====
   * GET: ?c=empleado&a=list
   */
  public function list(): array
  {
    requireLogin(); // cualquier usuario logueado puede ver
    $this->page_title = 'Listado de Empleados';
    $this->view = 'listar';
    return [
      'empleados' => $this->empleadoModel->all()
    ];
  }

  /* ===== FORM NUEVO =====
   * GET: ?c=empleado&a=form
   */
  public function form(): array
  {
    requireRole(['admin', 'editor']); // crear: editor o admin
    $this->page_title = 'Nuevo Empleado';
    $this->view = 'form';
    return [
      'puestos' => $this->puestoModel->all(),
      'errores' => []
    ];
  }

  /* ===== GUARDAR NUEVO =====
   * POST: ?c=empleado&a=save
   * Inserta un EMPLEADO (no usuarios).
   */
  public function save(): array
  {
    requireRole(['admin', 'editor']); // guardar: editor o admin

    // Validación mínima de campos requeridos del empleado
    $req = ['nombre', 'apellido', 'dni', 'empresa', 'puesto_id'];
    $errores = [];
    foreach ($req as $r) {
      if (empty($_POST[$r])) $errores[] = "Falta el campo: {$r}";
    }
    if ($errores) {
      $this->page_title = 'Nuevo Empleado';
      $this->view = 'form';
      return ['puestos' => $this->puestoModel->all(), 'errores' => $errores];
    }

    try {
      $ok = $this->empleadoModel->create($_POST);
      if (!$ok) {
        // si tu modelo setea una propiedad con el último error, la mostramos
        $detalle = $this->empleadoModel->lastError ?? '';
        $msg = 'No se pudo guardar (¿DNI duplicado o puesto inválido?)';
        if ($detalle) $msg .= ' — Detalle: ' . $detalle;
        throw new Exception($msg);
      }
    } catch (Throwable $e) {
      $this->page_title = 'Nuevo Empleado';
      $this->view = 'form';
      return [
        'puestos' => $this->puestoModel->all(),
        'errores' => ['Error al guardar: ' . $e->getMessage()]
      ];
    }

    header("Location: ?c=empleado&a=list");
    exit;
  }

  /* ===== EDITAR (cargar datos) =====
   * GET: ?c=empleado&a=edit&id=123
   */
  public function edit(): array
  {
    requireRole(['admin', 'editor']); // editar: editor o admin

    $id  = (int)($_GET['id'] ?? 0);
    $emp = $this->empleadoModel->find($id);
    if (!$emp) {
      die("Empleado no encontrado");
    }

    $this->page_title = 'Editar Empleado';
    $this->view = 'form';
    return [
      'empleado' => $emp,
      'puestos'  => $this->puestoModel->all(),
      'errores'  => []
    ];
  }

  /* ===== ACTUALIZAR =====
   * POST: ?c=empleado&a=update
   */
  public function update(): array
  {
    requireRole(['admin', 'editor']); // actualizar: editor o admin

    $id = (int)($_POST['id'] ?? 0);

    $req = ['nombre', 'apellido', 'dni', 'empresa', 'puesto_id'];
    $errores = [];
    if (!$id) $errores[] = 'ID inválido.';
    foreach ($req as $r) {
      if (empty($_POST[$r])) $errores[] = "Falta el campo: {$r}";
    }

    if ($errores) {
      $this->page_title = 'Editar Empleado';
      $this->view = 'form';
      return [
        'empleado' => array_merge(['id' => $id], $_POST),
        'puestos'  => $this->puestoModel->all(),
        'errores'  => $errores
      ];
    }

    try {
      $ok = $this->empleadoModel->update($id, $_POST);
      if (!$ok) {
        $detalle = $this->empleadoModel->lastError ?? '';
        $msg = 'No se pudo actualizar (¿DNI duplicado?)';
        if ($detalle) $msg .= ' — Detalle: ' . $detalle;
        throw new Exception($msg);
      }
    } catch (Throwable $e) {
      $this->page_title = 'Editar Empleado';
      $this->view = 'form';
      return [
        'empleado' => array_merge(['id' => $id], $_POST),
        'puestos'  => $this->puestoModel->all(),
        'errores'  => ['Error al actualizar: ' . $e->getMessage()]
      ];
    }

    header("Location: ?c=empleado&a=list");
    exit;
  }

  /* ===== ELIMINAR =====
   * GET: ?c=empleado&a=delete&id=123
   */
  public function delete(): void
  {
    requireRole(['admin']); // eliminar: solo admin

    $id = (int)($_GET['id'] ?? 0);
    if ($id && $this->empleadoModel->delete($id)) {
      header("Location: ?c=empleado&a=list");
      exit;
    } else {
      echo "<div class='alert alert-danger text-center'>No se pudo eliminar el empleado.</div>";
      echo "<div class='text-center mt-3'><a class='btn btn-secondary' href='?c=empleado&a=list'>Volver</a></div>";
    }
  }
}
