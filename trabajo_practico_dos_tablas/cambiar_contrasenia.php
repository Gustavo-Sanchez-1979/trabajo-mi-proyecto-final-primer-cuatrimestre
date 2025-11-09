<?php
require_once __DIR__ . '/auth.php';     // protege la página (solo logueados)
require_once __DIR__ . '/model/db.php'; // conexión a la base

$mensaje = '';
$error   = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $usuario_sesion = $_SESSION['usuario'] ?? '';
    $pass_actual    = $_POST['actual'] ?? '';
    $nuevo_usuario  = trim($_POST['nuevo_usuario'] ?? ''); // opcional
    $pass_nueva     = $_POST['nueva'] ?? '';               // opcional

    if ($usuario_sesion === '' || $pass_actual === '') {
        $error = "Debes ingresar tu contraseña actual.";
    } else {
        $db   = new Db();
        $conn = $db->con;

        // Traigo hash y usuario actual real (por si hay mayúsculas/minúsculas)
        $stmt = $conn->prepare("SELECT usuario, contrasenia FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $usuario_sesion);
        $stmt->execute();
        $res = $stmt->get_result();
        $row = $res->fetch_assoc();

        if (!$row) {
            $error = "Usuario de sesión no encontrado.";
        } else {
            $usuario_actual = $row['usuario'];
            $hash_guardado  = $row['contrasenia'];

            // Verifico contraseña actual (bcrypt o SHA1)
            $coincide = preg_match('/^\$2y\$/', $hash_guardado) || preg_match('/^\$argon2/', $hash_guardado)
                ? password_verify($pass_actual, $hash_guardado)
                : (sha1($pass_actual) === $hash_guardado);

            if (!$coincide) {
                $error = "La contraseña actual es incorrecta.";
            } else {
                // Armamos UPDATE dinámico según qué quiera cambiar
                $campos = [];
                $params = [];
                $types  = '';

                // Cambiar usuario (opcional, validando que no exista)
                if ($nuevo_usuario !== '' && $nuevo_usuario !== $usuario_actual) {
                    $chk = $conn->prepare("SELECT 1 FROM usuarios WHERE usuario = ?");
                    $chk->bind_param("s", $nuevo_usuario);
                    $chk->execute();
                    if ($chk->get_result()->num_rows > 0) {
                        $error = "El nombre de usuario ya existe. Elegí otro.";
                    } else {
                        $campos[] = "usuario = ?";
                        $params[] = $nuevo_usuario;
                        $types   .= 's';
                    }
                }

                // Cambiar contraseña (opcional; siempre guardamos bcrypt)
                if (!$error && $pass_nueva !== '') {
                    $nuevo_hash = password_hash($pass_nueva, PASSWORD_DEFAULT);
                    $campos[] = "contrasenia = ?";
                    $params[] = $nuevo_hash;
                    $types   .= 's';
                }

                if (!$error) {
                    if (count($campos) > 0) {
                        $sql = "UPDATE usuarios SET " . implode(", ", $campos) . " WHERE usuario = ?";
                        $params[] = $usuario_actual;
                        $types   .= 's';

                        $upd = $conn->prepare($sql);
                        $upd->bind_param($types, ...$params);
                        $upd->execute();

                        // Si cambió el usuario, actualizamos la sesión
                        if ($nuevo_usuario !== '' && $nuevo_usuario !== $usuario_actual) {
                            $_SESSION['usuario'] = $nuevo_usuario;
                        }

                        $mensaje = "Datos actualizados correctamente ✅";
                    } else {
                        $mensaje = "No hay cambios para guardar.";
                    }
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Cambiar usuario/contraseña</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 col-md-5">
  <div class="card shadow p-4">
    <h3 class="text-center mb-3">Cambiar su nombre de usuario/o su contraseña</h3>

    <?php if ($mensaje): ?>
      <div class="alert alert-success"><?= htmlspecialchars($mensaje) ?></div>
    <?php elseif ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" autocomplete="off">
      <div class="mb-3">
        <label class="form-label">Contraseña actual *</label>
        <input type="password" name="actual" class="form-control" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Nuevo usuario (opcional)</label>
        <input type="text" name="nuevo_usuario" class="form-control" placeholder="Dejar en blanco si no cambiás">
      </div>

      <div class="mb-3">
        <label class="form-label">Nueva contraseña (opcional)</label>
        <input type="password" name="nueva" class="form-control" placeholder="Dejar en blanco si no cambiás">
      </div>

      <button type="submit" class="btn btn-primary w-100">Guardar cambios</button>
      <a href="index.php?c=empleado&a=list" class="btn btn-outline-secondary w-100 mt-3">Volver</a>
    </form>
  </div>
</div>
</body>
</html>