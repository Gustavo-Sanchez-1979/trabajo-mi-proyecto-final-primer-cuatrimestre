<?php
// controller/empleado.php

// 🔐 Autenticación y helpers de roles
require_once __DIR__ . '/../auth.php';
require_once __DIR__ . '/../model/Empleado.php';
require_once __DIR__ . '/../model/Puesto.php';

/**
 * Controlador MVC de Empleados.
 * - Decide qué vista cargar (propiedad $view)
 * - Arma el título de página (propiedad $page_title)
 * - Llama al modelo Empleado para CRUD
 * - Usa el modelo Puesto para poblar combos <select>
 */
class EmpleadoController {
  // Título que usa el layout en <title> y <h1>
  public string $page_title = 'Empleados';

  // Nombre de la vista a renderizar dentro de /views/empleado/ (p.ej. listar.php o form.php)
  public string $view = 'listar';

  // Instancias de modelos (capa de datos)
  public Empleado $empleadoModel;
  public Puesto $puestoModel;

  // Alias para compatibilidad con código previo (si en algún lado usabas $tablaObj)
  public Empleado $tablaObj;

  // Se ejecuta al crear el controlador (una vez por request)
  public function __construct() {
    // Conectar capa de datos
    $this->empleadoModel = new Empleado(); // <-- clase del modelo Empleado (consulta BD)
    $this->puestoModel   = new Puesto();   // <-- clase del modelo Puesto (consulta BD)
    // Alias para no romper código previo que use $tablaObj
    $this->tablaObj      = $this->empleadoModel;
  }

  /* ===== LISTAR =====
   * Ruta: ?c=empleado&a=list
   * Obtiene todos los empleados y setea la vista 'listar'
   * Devuelve un array $data que el router le pasa a la vista.
   */
  public function list(): array {
    requireLogin(); // 🔐 cualquier usuario logueado puede ver
    $this->page_title = 'Listado de Empleados';
    $this->view = 'listar';
    return [
      'empleados' => $this->empleadoModel->all()
    ];
  }

  /* ===== FORM NUEVO =====
   * Ruta: ?c=empleado&a=form
   * Muestra el formulario para crear un empleado (carga el combo de puestos)
   */
  public function form(): array {
    requireRole(['admin','editor']); // 🔐 crear: editor o admin
    $this->page_title = 'Nuevo Empleado';
    $this->view = 'form';
    return [
      'puestos' => $this->puestoModel->all(), // llena el <select name="puesto_id">
      'errores' => []
    ];
  }

  /* ===== GUARDAR NUEVO =====
   * Ruta (POST): ?c=empleado&a=save
   * Valida datos requeridos, llama al modelo->create y redirige al listado.
   */
  public function save(): array {
    requireRole(['admin','editor']); // 🔐 guardar: editor o admin

    // Campos obligatorios mínimos
    $req = ['nombre','apellido','dni','empresa','puesto_id'];
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
        throw new Exception('No se pudo guardar (¿DNI duplicado o puesto inválido?).');
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
  } // 👈 CIERRE QUE FALTABA

  /* ===== EDITAR (cargar datos) =====
   * Ruta: ?c=empleado&a=edit&id=123
   * Busca el empleado por id y reusa la misma vista 'form' para editar.
   */
  public function edit(): array {
    requireRole(['admin','editor']); // 🔐 editar: editor o admin

    $id  = (int)($_GET['id'] ?? 0);
    $emp = $this->empleadoModel->find($id);
    if (!$emp) { die("Empleado no encontrado"); }

    $this->page_title = 'Editar Empleado';
    $this->view = 'form';
    return [
      'empleado' => $emp,
      'puestos'  => $this->puestoModel->all(),
      'errores'  => []
    ];
  }

  /* ===== ACTUALIZAR =====
   * Ruta (POST): ?c=empleado&a=update
   * Valida, hace UPDATE y redirige a la lista. Si hay errores, vuelve a 'form'.
   */
  public function update(): array {
    requireRole(['admin','editor']); // 🔐 actualizar: editor o admin

    $id = (int)($_POST['id'] ?? 0);

    $req = ['nombre','apellido','dni','empresa','puesto_id'];
    $errores = [];
    if (!$id) $errores[] = 'ID inválido.';
    foreach ($req as $r) {
      if (empty($_POST[$r])) $errores[] = "Falta el campo: {$r}";
    }

    if ($errores) {
      $this->page_title = 'Editar Empleado';
      $this->view = 'form';
      return [
        'empleado' => array_merge(['id'=>$id], $_POST),
        'puestos'  => $this->puestoModel->all(),
        'errores'  => $errores
      ];
    }

    try {
      $ok = $this->empleadoModel->update($id, $_POST);
      if (!$ok) {
        throw new Exception('No se pudo actualizar (¿DNI duplicado?).');
      }
    } catch (Throwable $e) {
      $this->page_title = 'Editar Empleado';
      $this->view = 'form';
      return [
        'empleado' => array_merge(['id'=>$id], $_POST),
        'puestos'  => $this->puestoModel->all(),
        'errores'  => ['Error al actualizar: ' . $e->getMessage()]
      ];
    }

    header("Location: ?c=empleado&a=list");
    exit;
  } // 👈 CIERRE QUE FALTABA

  /* ===== ELIMINAR =====
   * Ruta: ?c=empleado&a=delete&id=123
   * Elimina por id y redirige. Si falla, muestra un mensaje básico.
   */
  public function delete(): void {
  requireRole(['admin']); // 🔐 SOLO admin puede eliminar

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