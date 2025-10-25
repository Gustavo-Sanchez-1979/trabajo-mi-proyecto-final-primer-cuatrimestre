<?php
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/model/db.php';
// Solo el admin puede entrar
if ($_SESSION['usuario'] !== 'admin') {
    header("Location: index.php?c=empleado&a=list");
    exit();
}

$msg = $error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nuevo_usuario = trim($_POST['usuario'] ?? '');
    $nueva_clave   = $_POST['contrasenia'] ?? '';

    if ($nuevo_usuario === '' || $nueva_clave === '') {
        $error = "Debes completar todos los campos.";
    } else {
        $db   = new Db();
        $conn = $db->con;

        // Verifica si ya existe
        $stmt = $conn->prepare("SELECT 1 FROM usuarios WHERE usuario = ?");
        $stmt->bind_param("s", $nuevo_usuario);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = "El usuario ya existe.";
        } else {
            $hash = password_hash($nueva_clave, PASSWORD_DEFAULT);
            $ins = $conn->prepare("INSERT INTO usuarios (usuario, contrasenia) VALUES (?, ?)");
            $ins->bind_param("ss", $nuevo_usuario, $hash);
            $ins->execute();
            $msg = "Usuario creado correctamente ✅";
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
    <?php if ($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
    <?php if ($error): ?><div class="alert alert-danger"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="POST">
      <div class="mb-3">
        <label>Nombre de usuario</label>
        <input type="text" name="usuario" class="form-control" required>
      </div>
      <div class="mb-3">
        <label>Contraseña</label>
        <input type="password" name="contrasenia" class="form-control" required>
      </div>
      <button class="btn btn-primary w-100" type="submit">Crear usuario</button>
      <a href="index.php?c=empleado&a=list" class="btn btn-outline-secondary w-100 mt-3">Volver</a>
    </form>
  </div>
</div>
</body>
</html>
