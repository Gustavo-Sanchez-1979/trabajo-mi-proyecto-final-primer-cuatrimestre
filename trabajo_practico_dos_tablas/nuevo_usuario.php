<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/model/db.php';

/** 🔐 Solo ADMIN puede entrar a esta pantalla */
requireRole(['admin']);

$msg = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_usuario = trim($_POST['usuario'] ?? '');
    $nueva_clave   = $_POST['contrasenia'] ?? '';
    $role_id       = (int)($_POST['role_id'] ?? 3); // 1=admin, 2=editor, 3=lector (default)

    // Normalizo role_id a 1..3 por seguridad
    if (!in_array($role_id, [1,2,3], true)) {
        $role_id = 3;
    }

    if ($nuevo_usuario === '' || $nueva_clave === '') {
        $error = "Debes completar todos los campos.";
    } else {
        $db   = new Db();
        $conn = $db->con;

        // ¿Ya existe?
        $stmt = $conn->prepare("SELECT 1 FROM usuarios WHERE usuario = ?");
        if (!$stmt) {
            $error = "Error interno (prepare).";
        } else {
            $stmt->bind_param("s", $nuevo_usuario);
            $stmt->execute();
            $existe = $stmt->get_result()->num_rows > 0;
            $stmt->close();

            if ($existe) {
                $error = "El usuario ya existe.";
            } else {
                // Insert con rol ✅
                $hash = password_hash($nueva_clave, PASSWORD_DEFAULT);
                $ins  = $conn->prepare("INSERT INTO usuarios (usuario, contrasenia, role_id) VALUES (?, ?, ?)");
                if (!$ins) {
                    $error = "Error interno (prepare insert).";
                } else {
                    $ins->bind_param("ssi", $nuevo_usuario, $hash, $role_id);
                    if ($ins->execute()) {
                        $msg = "Usuario creado correctamente ✅";
                    } else {
                        $error = "No se pudo crear el usuario.";
                    }
                    $ins->close();
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
<title>Nuevo Usuario</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5 col-md-4">
  <div class="card shadow p-4">
    <h3 class="text-center mb-3">Crear nuevo usuario</h3>

    <?php if ($msg): ?>
      <div class="alert alert-success"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST">
      <div class="mb-3">
        <label>Usuario</label>
        <input type="text" name="usuario" class="form-control" required>
      </div>

      <div class="mb-3">
        <label>Contraseña</label>
        <input type="password" name="contrasenia" class="form-control" required>
      </div>

      <div class="mb-3">
        <label for="role_id">Rol</label>
        <select name="role_id" class="form-control" required>
          <option value="">Seleccionar rol</option>
          <option value="1">Administrador</option>
          <option value="2">Editor</option>
          <option value="3">Lector</option>
        </select>
      </div>

      <button class="btn btn-primary w-100" type="submit">Crear Usuario</button>

      <a href="index.php?c=empleado&a=list" class="btn btn-outline-secondary w-100 mt-3">
        Volver al listado de empleados
      </a>
    </form>
  </div>
</div>
</body>
</html>