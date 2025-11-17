<!doctype html>
<html lang="es">

<head>
  <meta charset="utf-8" />
  <!-- Responsive -->
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- Título dinámico -->
  <title><?= htmlspecialchars($page_title ?? 'App') ?></title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background: #f4f6f8;
    }

    .badge-puesto {
      background: #198754;
    }

    td,
    th {
      word-break: break-word;
    }

    td small {
      color: #6c757d;
    }
  </style>
</head>

<body>

  <?php
  // Sesión y rol para UI (si no usás roles, podés omitir esto)
  if (session_status() !== PHP_SESSION_ACTIVE) session_start();
  $role    = $_SESSION['user_role'] ?? 'lector';
  $isAdmin = ($role === 'admin');
  $canEdit = in_array($role, ['admin', 'editor'], true);
  ?>
  <!-- Navbar -->
  <nav class="navbar navbar-dark bg-dark">
    <div class="container d-flex align-items-center justify-content-between">

      <!-- Marca: vuelve al listado principal -->
      <a class="navbar-brand" href="?c=empleado&a=list">Puestos ↔ Empleados</a>

      <div class="d-flex align-items-center gap-2">
        <!-- Cerrar sesión / Cambiar credenciales -->
       <a
          href="logout.php?next=/trabajo-mi-proyecto-final-primer-cuatrimestre/trabajo_practico_dos_tablas/login.php"
          class="btn btn-outline-light m-2"
          onclick="return confirm('⚠️ Al Cerrar sesión  devera volver a logiarse para ingresar a administracion de empleados la sesión se cerrará automaticamente por seguridad. ¿Continuar?');">
          Cerrar sesión
        </a>
        <a href="cambiar_contrasenia.php" class="btn btn-warning m-2">Cambiar su nombre de usuario/o su contraseña</a>

        <!-- Nuevo usuario (solo admin; si no, mostrar deshabilitado con tooltip) -->
        <?php if ($isAdmin): ?>
          <a href="nuevo_usuario.php"
            class="btn btn-success m-2"
            data-bs-toggle="tooltip"
            data-bs-placement="bottom"
            data-bs-title="Solo para ingreso del administrador">
            Nuevo usuario
          </a>
        <?php else: ?>
          <span class="d-inline-block m-2" tabindex="0"
            data-bs-toggle="tooltip"
            data-bs-placement="bottom"
            data-bs-title="Solo Administrador">
            <button class="btn btn-success" type="button" disabled style="pointer-events:none;">
              Nuevo usuario
            </button>
          </span>
        <?php endif; ?>

        <!-- Volver al inicio (¡Al volver se cerrará la sesión de Administrador de Empleados por seguridad!) -->
        <a
          href="logout.php?next=/trabajo-mi-proyecto-final-primer-cuatrimestre/index.php&m=logout_ok"
          class="btn btn-outline-light m-2"
          onclick="return confirm('⚠️ Al volver a pagina pricipal se cerrará automaticamente la sesión de Administrador de Empleados por seguridad. ¿Continuar?');">
          Volver al Inicio
        </a>

        <!-- Accesos rápidos -->
        <a class="btn btn-warning m-2" href="?c=empleado&a=list">Empleado</a>
        <a class="btn btn-warning m-2" href="?c=puesto&a=list">Puestos</a>
      </div>
    </div>
  </nav>

  <!-- Contenido -->
  <div class="container-fluid my-4">
    <h1 class="mb-3"><?= htmlspecialchars($page_title ?? '') ?></h1>
  </div>

  <!-- Bootstrap JS (si no lo cargás en el footer) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Inicializa tooltips para que aparezcan los mensajes
    document.addEventListener('DOMContentLoaded', function() {
      var t = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
      t.forEach(function(el) {
        new bootstrap.Tooltip(el);
      });
    });
  </script>
</body>

</html>