<?php
// Inicia/retoma la sesión para poder leer/escribir en $_SESSION
session_start();

// Carga la clase Db desde /model/db.php, usando ruta absoluta desde este archivo
require_once __DIR__ . '/model/db.php';

// Solo procesamos el login si el formulario vino por método POST
if ($_SERVER["REQUEST_METHOD"] === "POST") {
  // Lee los campos del formulario; si no vienen, quedan en string vacío
  $usuario = $_POST['usuario'] ?? '';
  $pass    = $_POST['contrasenia'] ?? '';

  // Crea el objeto de conexión y obtiene el handler mysqli
  $db   = new Db();
  $conn = $db->con;  // en tu clase Db, la propiedad pública se llama "con"

  // Prepara la consulta para validar usuario/contraseña con SHA1 (básico)
  // Nota: en producción se recomienda password_hash/password_verify
  $stmt = $conn->prepare("SELECT 1 FROM usuarios WHERE usuario = ? AND contrasenia = SHA1(?)");
  // Enlaza los parámetros (ambos strings)
  $stmt->bind_param("ss", $usuario, $pass);
  // Ejecuta la consulta
  $stmt->execute();
  // Obtiene el resultado (si hay fila, es válido)
  $res  = $stmt->get_result();

  if ($res && $res->num_rows === 1) {
    // Seguridad: regeneramos el id de sesión al autenticarnos
    session_regenerate_id(true);
    // Guardamos el usuario en la sesión para que auth.php nos deje pasar
    $_SESSION['usuario'] = $usuario;
    // Redirigimos al panel (controlador de empleados → acción listar)
    header("Location: index.php?c=empleado&a=list");
    exit();
  } else {
    // Si no coincide usuario/contraseña, mostramos un error en la vista
    $error = "Usuario o contraseña incorrectos";
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Ingreso al sistema</title>
  <!-- Cargamos Bootstrap CSS desde CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
  <div class="container mt-5 col-md-4">
    <div class="card shadow p-4">
      <h3 class="text-center mb-3">Iniciar sesión</h3>

      <!-- Si $error está seteado, mostramos una alerta roja -->
      <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

      <!-- Formulario de login (POST). autocomplete off es opcional -->
      <form method="POST" autocomplete="off">
        <div class="mb-3">
          <label class="form-label">Usuario</label>
          <input type="text" name="usuario" class="form-control" required>
        </div>

        <div class="mb-3">
          <label class="form-label">Contraseña</label>
          <input type="password" name="contrasenia" class="form-control" required>
        </div>

        <!-- Botón que envía el formulario -->
        <button class="btn btn-primary w-100" type="submit">Ingresar</button>

        <!-- Enlace (no botón) para ir al inicio público; separado con mt-3 -->
        <a href="/MIPROYECTOFINAL/index.php" class="btn btn-outline-primary w-100 mt-3">
          Si no estás autorizado → Volver al inicio de la página principal
        </a>
      </form>
    </div>
  </div>
</body>
</html>