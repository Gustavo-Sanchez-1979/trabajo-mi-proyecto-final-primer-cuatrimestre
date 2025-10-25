<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once __DIR__ . '/model/db.php';

if (!isset($_SESSION['login_intentos'])) $_SESSION['login_intentos'] = 0;

$db   = new Db();
$conn = $db->con;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
  if ($_SESSION['login_intentos'] >= 10) {
    $error = "Demasiados intentos. Probá en unos minutos.";
  } else {
    $usuario = trim($_POST['usuario'] ?? '');
    $pass    = $_POST['contrasenia'] ?? '';

    // Traigo usuario exacto y hash
    $stmt = $conn->prepare("SELECT usuario, contrasenia FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    $ok = false;
    if ($row) {
      $hash = $row['contrasenia'];

      if (preg_match('/^\$2y\$/', $hash) || preg_match('/^\$argon2/', $hash)) {
        // bcrypt/argon
        $ok = password_verify($pass, $hash);
      } else {
        // compat SHA1 heredado
        $ok = (sha1($pass) === $hash);
        if ($ok) {
          // migro a bcrypt
          $nuevo = password_hash($pass, PASSWORD_DEFAULT);
          $upd = $conn->prepare("UPDATE usuarios SET contrasenia = ? WHERE usuario = ?");
          $upd->bind_param("ss", $nuevo, $row['usuario']);
          $upd->execute();
        }
      }
    }

    if ($ok) {
      $_SESSION['login_intentos'] = 0;
      session_regenerate_id(true);
      $_SESSION['usuario'] = $row['usuario']; // exacto como está en DB
      header("Location: index.php?c=empleado&a=list");
      exit();
    } else {
      $_SESSION['login_intentos']++;
      $error = "Usuario o contraseña incorrectos";
    }
  }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ingreso al sistema</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 col-md-4">
  <div class="card shadow p-4">
    <h3 class="text-center mb-3">Iniciar sesión</h3>
    <?php if(isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="POST">
      <div class="mb-3">
        <label>Usuario</label>
        <input type="text" name="usuario" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Contraseña</label>
        <input type="password" name="contrasenia" class="form-control" required>
      </div>
      <button class="btn btn-primary w-100" type="submit">Ingresar</button>
    </form>
  </div>
</div>
</body>
</html>