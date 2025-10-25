<?php
// controller/empleado.php

// Importamos los modelos que usa este controlador
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
    $this->page_title = 'Listado de Empleados';
    $this->view = 'listar';
    // Llama al modelo para traer la data (idealmente con LEFT JOIN a puestos)
    return [
      'empleados' => $this->empleadoModel->all()
    ];
  }

  /* ===== FORM NUEVO =====
   * Ruta: ?c=empleado&a=form
   * Muestra el formulario para crear un empleado (carga el combo de puestos)
   */
  public function form(): array {
    $this->page_title = 'Nuevo Empleado';
    $this->view = 'form';
    return [
      'puestos' => $this->puestoModel->all(), // llena el <select name="puesto_id">
      'errores' => []                          // array para mostrar validaciones
    ];
  }

  /* ===== GUARDAR NUEVO =====
   * Ruta (POST): ?c=empleado&a=save
   * Valida datos requeridos, llama al modelo->create y redirige al listado.
   */
  public function save(): array {
    // Campos obligatorios mínimos
    $req = ['nombre','apellido','dni','empresa','puesto_id'];
    $errores = [];
    foreach ($req as $r) {
      if (empty($_POST[$r])) $errores[] = "Falta el campo: {$r}";
    }

    // Si hay errores, volvemos al form con los mensajes y los puestos cargados
    if ($errores) {
      $this->page_title = 'Nuevo Empleado';
      $this->view = 'form';
      return ['puestos' => $this->puestoModel->all(), 'errores' => $errores];
    }

    // Insertar en BD
    $ok = $this->empleadoModel->create($_POST);

    // Si falla (DNI único, FK inválida, etc.) mostramos error y volvemos al form
    if (!$ok) {
      $errores[] = 'No se pudo guardar (¿DNI duplicado o puesto inválido?).';
      $this->page_title = 'Nuevo Empleado';
      $this->view = 'form';
      return ['puestos' => $this->puestoModel->all(), 'errores' => $errores];
    }

    // ÉXITO: redirigimos al listado (no hacer echo antes de header)
    header("Location: ?c=empleado&a=list");
    exit;
  }

  /* ===== EDITAR (cargar datos) =====
   * Ruta: ?c=empleado&a=edit&id=123
   * Busca el empleado por id y reusa la misma vista 'form' para editar.
   */
  public function edit(): array {
    $id  = (int)($_GET['id'] ?? 0);                 // id desde la URL
    $emp = $this->empleadoModel->find($id);        // busca registro en BD
    if (!$emp) { die("Empleado no encontrado"); }  // manejo simple (podés renderizar error lindo)

    $this->page_title = 'Editar Empleado';
    $this->view = 'form';
    return [
      'empleado' => $emp,                 // datos actuales para completar el form
      'puestos'  => $this->puestoModel->all(), // para el <select>
      'errores'  => []
    ];
  }

  /* ===== ACTUALIZAR =====
   * Ruta (POST): ?c=empleado&a=update
   * Valida, hace UPDATE y redirige a la lista. Si hay errores, vuelve a 'form'.
   */
  public function update(): array {
    $id = (int)($_POST['id'] ?? 0);      // id oculto del formulario

    // Validación mínima
    $req = ['nombre','apellido','dni','empresa','puesto_id'];
    $errores = [];
    if (!$id) $errores[] = 'ID inválido.';
    foreach ($req as $r) {
      if (empty($_POST[$r])) $errores[] = "Falta el campo: {$r}";
    }

    // Si falla validación, volvemos al form con lo que el usuario cargó
    if ($errores) {
      $this->page_title = 'Editar Empleado';
      $this->view = 'form';
      return [
        // Mezclamos el id con POST para que se rellene el formulario
        'empleado' => array_merge(['id'=>$id], $_POST),
        'puestos'  => $this->puestoModel->all(),
        'errores'  => $errores
      ];
    }

    // Ejecutar UPDATE
    $ok = $this->empleadoModel->update($id, $_POST);

    // Si falla (p.ej. UNIQUE(dni)), mostramos error y mantenemos lo cargado
    if (!$ok) {
      $this->page_title = 'Editar Empleado';
      $this->view = 'form';
      return [
        'empleado' => array_merge(['id'=>$id], $_POST),
        'puestos'  => $this->puestoModel->all(),
        'errores'  => ['No se pudo actualizar (¿DNI duplicado?)']
      ];
    }

    // ÉXITO: volvemos al listado
    header("Location: ?c=empleado&a=list");
    exit;
  }

  /* ===== ELIMINAR =====
   * Ruta: ?c=empleado&a=delete&id=123
   * Elimina por id y redirige. Si falla, muestra un mensaje básico.
   */
  public function delete(): void {
    $id = (int)($_GET['id'] ?? 0);
    if ($id && $this->empleadoModel->delete($id)) {
      header("Location: ?c=empleado&a=list");
      exit;
    } else {
      // Podés reemplazar por una vista de error más prolija
      echo "<div class='alert alert-danger text-center'>No se pudo eliminar el empleado.</div>";
      echo "<div class='text-center mt-3'><a class='btn btn-secondary' href='?c=empleado&a=list'>Volver</a></div>";
    }
  }
}