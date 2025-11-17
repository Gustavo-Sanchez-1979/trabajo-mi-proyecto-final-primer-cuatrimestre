<?php

require_once __DIR__ . '/auth.php'; // obliga login para TODO el panel
// auth.php


if (!isset($_SESSION['usuario'])) {
  header("Location: login.php");
  exit();
}
?>

<?php
// ======================================================
// FRONT CONTROLLER - Enrutador simple tipo MVC
// Este archivo recibe la URL, resuelve qué controlador y
// qué acción ejecutar, obtiene $data y renderiza la vista.
// ======================================================

// ------------------------------------------------------
// 1) Leer parámetros de la URL
//    Ejemplo: index.php?c=empleado&a=list
//    - c = nombre del controlador (sin "Controller" ni ruta)
//    - a = nombre del método/acción pública dentro del controlador
// ------------------------------------------------------
$controller = $_GET['c'] ?? 'empleado';  // controlador por defecto → empleado
$action     = $_GET['a'] ?? 'list';      // acción por defecto     → list

// ------------------------------------------------------
// 2) Construir la ruta física del archivo del controlador
//    Espera archivos como: controller/empleado.php, controller/puesto.php
// ------------------------------------------------------
$controllerFile = __DIR__ . "/controller/{$controller}.php";

// Verificar que el archivo exista
if (!file_exists($controllerFile)) {
  http_response_code(404);
  die("Error: Controlador <b>{$controller}</b> no encontrado en <b>controller/</b>.");
}

// Incluir el archivo del controlador
require_once $controllerFile;

// ------------------------------------------------------
// 3) Resolver el nombre de la clase del controlador
//    Convención: ucfirst($controller) . 'Controller'
//    - 'empleado' → 'EmpleadoController'
//    - 'puesto'   → 'PuestoController'
// ------------------------------------------------------
$controllerClass = ucfirst($controller) . "Controller";

// Verificar que la clase exista en el archivo incluido
if (!class_exists($controllerClass)) {
  http_response_code(500);
  die("Error: La clase <b>{$controllerClass}</b> no está definida en {$controllerFile}");
}

// Instanciar el controlador
$controllerObj = new $controllerClass();

// ------------------------------------------------------
// 4) Verificar que la acción (método) exista en el controlador
//    - Debe ser un método público; normalmente retorna un array $data
//      que viajará a la vista.
// ------------------------------------------------------
if (!method_exists($controllerObj, $action)) {
  http_response_code(404);
  die("Error: La acción <b>{$action}</b> no existe en el controlador <b>{$controllerClass}</b>.");
}

// ======================================================
// 5) Ejecutar la acción y recibir datos del controlador
//    - Convención: la acción retorna un array asociativo con datos
//      que la vista usará como $data.
//    - El propio controlador setea:
//        $this->view       → nombre de la vista (sin .php)
//        $this->page_title → título de página
// ======================================================
$data = $controllerObj->$action();

// ======================================================
// 6) Construir la ruta a la vista
//    Convención de carpetas de vistas:
//      views/<controller>/<view>.php
//    Ejemplos:
//      views/empleado/listar.php
//      views/empleado/form.php
//      views/puesto/listar.php
// ------------------------------------------------------
//  IMPORTANTE: $controllerObj->view debe ser asignado por la acción,
//     p.ej. en EmpleadoController::list() → $this->view = 'listar';
// ======================================================
$viewPath = __DIR__ . "/views/{$controller}/{$controllerObj->view}.php";

// Verificar que la vista exista
if (!file_exists($viewPath)) {
  http_response_code(500);
  die("Error: No se encontró la vista <b>{$viewPath}</b>.");
}

// Título de la página (lo utiliza el layout/header)
$page_title = $controllerObj->page_title ?? ucfirst($controller);

// ------------------------------------------------------
// 7) Incluir el layout: header → vista → footer
//    - header.php debería abrir <html> y <body>, navbar, container...
//    - footer.php debería cerrar el container, imprimir el footer y
//      cerrar </body></html>

include __DIR__ . "/views/header.php";
include $viewPath;
include __DIR__ . "/views/footer.php";
