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

    // ✅ Traigo también id y rol (JOIN a roles)
    $sql = "SELECT u.id, u.usuario, u.contrasenia, u.role_id, r.nombre AS role_name
            FROM usuarios u
            LEFT JOIN roles r ON r.id = u.role_id
            WHERE u.usuario = ?";

  // Traigo usuario exacto, hash y su rol
$usuario = trim($_POST['usuario'] ?? '');
$pass    = $_POST['contrasenia'] ?? '';

if ($usuario === '' || $pass === '') {
  $error = "Usuario y contraseña son obligatorios.";
} else {
  // Traigo usuario exacto, hash y su rol (LEFT JOIN por seguridad)
  $stmt = $conn->prepare("
    SELECT u.id, u.usuario, u.contrasenia, u.role_id, r.nombre AS role_name
    FROM usuarios u
    LEFT JOIN roles r ON r.id = u.role_id
    WHERE u.usuario = ?
    LIMIT 1
  ");

  if (!$stmt) {
    // Si la query no preparó, mostramos error genérico
    error_log("[LOGIN] prepare() falló: " . $conn->error);
    $error = "Error al intentar ingresar. Intente nuevamente.";
  } else {
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res ? $res->fetch_assoc() : null;
    $stmt->close();

    $ok = false;
    if ($row) {
      $hash = (string)$row['contrasenia'];

      // ¿bcrypt/argon?
      if (preg_match('/^\$2y\$/', $hash) || preg_match('/^\$argon2/i', $hash)) {
        $ok = password_verify($pass, $hash);
      } else {
        // Compatibilidad con hashes viejos (SHA1) y migración transparente
        $ok = (sha1($pass) === $hash);
        if ($ok) {
          $nuevo = password_hash($pass, PASSWORD_DEFAULT);
          $upd = $conn->prepare("UPDATE usuarios SET contrasenia = ? WHERE usuario = ?");
          if ($upd) {
            $upd->bind_param("ss", $nuevo, $row['usuario']);
            $upd->execute();
            $upd->close();
          }
        }
      }
    }
     }
     }

    if ($ok && $row) {
      $_SESSION['login_intentos'] = 0;
      session_regenerate_id(true);

      // Seteamos SIEMPRE usuario + rol
      $_SESSION['user_id']   = (int)$row['id'];
      $_SESSION['usuario']   = $row['usuario'];
      $_SESSION['role_id']   = (int)($row['role_id'] ?? 3);               // 3 = lector
      $_SESSION['user_role'] = $row['role_name'] ?: 'lector';             // 'admin'|'editor'|'lector'

      header("Location: index.php?c=empleado&a=list");
      exit();
    } else {
      $_SESSION['login_intentos'] = ($_SESSION['login_intentos'] ?? 0) + 1;
      $error = "Usuario o contraseña incorrectos.";
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
      <?php if (isset($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>
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

        <?php
          // ✅ Link Volver 
        $volver = '/trabajo-mi-proyecto-final-primer-cuatrimestre/index.php';
        ?>
        <a href="<?= htmlspecialchars($volver) ?>" class="btn btn-outline-secondary w-100 mt-3">Volver</a>
      </form>
    </div>
  </div>
</body>
</html>