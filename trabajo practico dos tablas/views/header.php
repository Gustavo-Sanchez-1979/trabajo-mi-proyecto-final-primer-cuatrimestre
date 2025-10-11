<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <!--
    viewport para responsive en móviles
    width=device-width: usa el ancho del dispositivo
    initial-scale=1: zoom inicial
  -->
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!--
    Título dinámico: viene del controlador ($this->page_title)
    htmlspecialchars: evita XSS si el título trae caracteres especiales
  -->
  <title><?= htmlspecialchars($page_title ?? 'App') ?></title>

  <!-- Bootstrap CSS (CDN) para estilos y componentes -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    /* Estilo base del fondo */
    body {
      background: #f4f6f8
    }

    /* Badge verde para resaltar puestos (si lo usás en tablas) */
    .badge-puesto {
      background: #198754;
    }

    /* Evita que textos largos rompan el layout en celdas de tabla */
    td,
    th {
      word-break: break-word;
    }

    /* Color más suave para textos pequeños (fechas, etc.) en tablas */
    td small {
      color: #6c757d;
    }
  </style>
</head>

<!--
  Sugerencia (opcional): para sticky footer
  <body class="d-flex flex-column min-vh-100">
-->

<body>
  <!-- Navbar superior oscura -->
  <nav class="navbar navbar-dark bg-dark">
    <div class="container">
      <!-- “Logo” que vuelve al listado principal de empleados -->
      <a class="navbar-brand" href="?c=empleado&a=list">Puestos ↔ Empleados</a>
      <!-- 🔹 Botón para volver al inicio -->      
      <a href="/MIPROYECTOFINAL/index.php" class="btn btn-secondary m-2">⬅ Volver al Inicio</a>
      <!-- Acciones rápidas a la derecha -->
      <div class="d-flex gap-2">
        <a class="btn btn-outline-light" href="?c=empleado&a=form">+ Nuevo empleado</a>
        <a class="btn btn-warning" href="?c=puesto&a=list">Puestos</a>
        <a class="btn btn-outline-warning" href="?c=puesto&a=form">+ Nuevo puesto</a>
      </div>
    </div>
  </nav>

  <!-- Contenedor principal donde se inyectan las vistas -->
  <div class="container my-4">
    <!-- Título de la página (set por cada acción del controlador) -->
    <h1 class="mb-3"><?= htmlspecialchars($page_title ?? '') ?></h1>