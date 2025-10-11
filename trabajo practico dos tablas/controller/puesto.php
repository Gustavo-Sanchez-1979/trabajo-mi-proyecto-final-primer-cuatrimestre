<?php
// controller/puesto.php

// Importa el MODELO que habla con la BD (tabla `puestos`)
require_once __DIR__ . '/../model/Puesto.php';

class PuestoController {
  // Título que usará el layout en <title> y <h1>
  public string $page_title = 'Puestos';
  // Nombre de la vista a renderizar dentro de /views/puesto/ (listar.php o form.php)
  public string $view = 'listar';
  // Instancia del modelo (capa de datos)
  private Puesto $puestoModel;

  public function __construct() {
    // Creamos el modelo (conexión y métodos SQL listos para usar)
    $this->puestoModel = new Puesto();
  }

  /** LISTAR
   * Ruta: ?c=puesto&a=list
   * - Setea título y vista
   * - Pide al modelo el listado de puestos
   * - Devuelve los datos para que el router los pase a la vista como $data['puestos']
   */
  public function list(): array {
    $this->page_title = 'Listado de Puestos'; // para el header/h1
    $this->view = 'listar';                   // usará views/puesto/listar.php
    return ['puestos' => $this->puestoModel->all()]; // SELECT ...
  }

  /** FORM NUEVO
   * Ruta: ?c=puesto&a=form
   * - Prepara la vista de formulario para crear un puesto
   * - Pasa un array de errores (vacío por defecto)
   */
  public function form(): array {
    $this->page_title = 'Nuevo Puesto';
    $this->view = 'form';           // usará views/puesto/form.php
    return ['errores' => []];       // la vista puede listar aquí validaciones
  }

  /** GUARDAR NUEVO
   * Ruta (POST): ?c=puesto&a=save
   * - Lee datos del formulario ($_POST)
   * - Valida mínimos
   * - Llama al modelo->create()
   * - Si todo ok, redirige al listado
   */
  public function save(): array {
    // Tomamos inputs (trim saca espacios al inicio/fin)
    $nombre = trim($_POST['nombre'] ?? '');
    $tarea  = trim($_POST['tarea']  ?? ''); // opcional
    $errores = [];

    // Validación mínima (nombre requerido)
    if ($nombre === '') {
      $errores[] = 'El nombre del puesto es obligatorio.';
    }

    // Si falla validación, volvemos a la vista de form con los errores
    if ($errores) {
      $this->page_title = 'Nuevo Puesto';
      $this->view = 'form';
      return ['errores' => $errores];
    }

    // Intentamos insertar en la BD
    $ok = $this->puestoModel->create($nombre, $tarea);

    // Si falla el INSERT (p.ej. UNIQUE(nombre)), informamos y volvemos al form
    if (!$ok) {
      $errores[] = 'No se pudo crear el puesto. ¿Ya existe uno con ese nombre?';
      $this->page_title = 'Nuevo Puesto';
      $this->view = 'form';
      return ['errores' => $errores];
    }

    // ÉXITO: redirigimos al listado (no hacer echo antes de header)
    header("Location: ?c=puesto&a=list");
    exit; // importante cortar la ejecución
  }

  /** EDITAR (carga de datos)
   * Ruta: ?c=puesto&a=edit&id=123
   * - Busca el puesto por ID
   * - Reutiliza la misma vista de formulario para editar
   * - Pasa $data['puesto'] con los valores actuales
   */
  public function edit(): array {
    $id = (int)($_GET['id'] ?? 0);        // id viene por GET
    $puesto = $this->puestoModel->find($id); // SELECT * FROM puestos WHERE id=?
    if (!$puesto) {
      // Manejo simple de error (podrías renderizar una vista de error “linda”)
      die("Puesto no encontrado");
    }
    $this->page_title = 'Editar Puesto';
    $this->view = 'form'; // reutilizamos views/puesto/form.php
    return ['puesto' => $puesto, 'errores' => []];
  }

  /** ACTUALIZAR
   * Ruta (POST): ?c=puesto&a=update
   * - Valida ID y nombre
   * - Llama al modelo->update()
   * - Redirige al listado si todo ok
   */
  public function update(): array {
    $id     = (int)($_POST['id'] ?? 0);          // hidden en el form de edición
    $nombre = trim($_POST['nombre'] ?? '');
    $tarea  = trim($_POST['tarea']  ?? '');
    $errores = [];

    // Validaciones básicas
    if (!$id)            $errores[] = 'ID inválido.';
    if ($nombre === '')  $errores[] = 'El nombre es obligatorio.';

    // Si hay errores, volvemos al form manteniendo los valores ya escritos
    if ($errores) {
      $this->page_title = 'Editar Puesto';
      $this->view = 'form';
      // Devolvemos un "puesto" armado con lo que llegó por POST para re-llenar inputs
      return ['errores' => $errores, 'puesto' => ['id'=>$id, 'nombre'=>$nombre, 'tarea'=>$tarea]];
    }

    // Intentamos actualizar
    $ok = $this->puestoModel->update($id, $nombre, $tarea);

    // Si falla (p.ej. UNIQUE(nombre)), informamos y mantenemos lo cargado
    if (!$ok) {
      $errores[] = 'No se pudo actualizar (posible nombre duplicado).';
      $this->page_title = 'Editar Puesto';
      $this->view = 'form';
      return ['errores' => $errores, 'puesto' => ['id'=>$id, 'nombre'=>$nombre, 'tarea'=>$tarea]];
    }

    // ÉXITO: redirigimos al listado
    header("Location: ?c=puesto&a=list");
    exit;
  }

  /** ELIMINAR
   * Ruta: ?c=puesto&a=delete&id=123
   * - Intenta borrar el registro
   * - Si hay empleados referenciando ese puesto, tu modelo debería impedirlo
   */
  public function delete(): void {
    $id = (int)($_GET['id'] ?? 0); // id por GET
    if ($id && $this->puestoModel->delete($id)) {
      // Ok → volver al listado
      header("Location: ?c=puesto&a=list");
      exit;
    } else {
      // Falla → aviso (FK en uso, id inexistente, etc.)
      echo "<div class='alert alert-danger text-center'> No se puede eliminar el puesto (posiblemente esté asignado a empleados).</div>";
      echo "<div class='text-center mt-3'><a class='btn btn-secondary' href='?c=puesto&a=list'>Volver</a></div>";
    }
  }
}