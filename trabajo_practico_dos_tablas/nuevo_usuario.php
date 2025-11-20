<?php

declare(strict_types=1);

if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/model/db.php';

// Solo ADMIN puede entrar acá
requireRole(['admin']);

$db   = new Db();
$conn = $db->con;

$errores = [];
$mensaje_ok = null;

// ============================================
// 1) ELIMINAR USUARIO (GET ?eliminar=ID)
// ============================================
if (isset($_GET['eliminar'])) {
  $id = (int)$_GET['eliminar'];

  // No borrar al usuario actual
  if (!empty($_SESSION['user_id']) && $id === (int)$_SESSION['user_id']) {
    $errores[] = "No podés eliminar tu propio usuario.";
  } else {
    // No borrar al ÚLTIMO admin
    $resAdmins = $conn->query("SELECT COUNT(*) AS c FROM usuarios WHERE role_id = 1");
    $rowAdmins = $resAdmins ? $resAdmins->fetch_assoc() : ['c' => 0];
    $cantAdmins = (int)($rowAdmins['c'] ?? 0);

    // Ver rol del usuario a eliminar
    $stmt = $conn->prepare("SELECT role_id FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resUser = $stmt->get_result();
    $userDel = $resUser->fetch_assoc();
    $stmt->close();

    if (!$userDel) {
      $errores[] = "Usuario no encontrado.";
    } else {
      $esAdminBorrar = ((int)$userDel['role_id'] === 1);

      if ($esAdminBorrar && $cantAdmins <= 1) {
        $errores[] = "No se puede eliminar al último administrador.";
      } else {
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
          $mensaje_ok = "Usuario eliminado correctamente.";
        } else {
          $errores[] = "No se pudo eliminar el usuario.";
        }
        $stmt->close();
      }
    }
  }
}

// ============================================
// 2) CARGAR DATOS PARA EDITAR (GET ?editar=ID)
// ============================================
$editUser = null;

if (isset($_GET['editar'])) {
  $id = (int)$_GET['editar'];

  $stmt = $conn->prepare("
        SELECT u.id, u.usuario, u.role_id, r.nombre AS role_name
        FROM usuarios u
        LEFT JOIN roles r ON r.id = u.role_id
        WHERE u.id = ?
        LIMIT 1
    ");
  $stmt->bind_param("i", $id);
  $stmt->execute();
  $res = $stmt->get_result();
  $editUser = $res ? $res->fetch_assoc() : null;
  $stmt->close();

  if (!$editUser) {
    $errores[] = "Usuario a editar no encontrado.";
  }
}

// ============================================
// 3) GUARDAR (ALTA / EDICIÓN) - POST
// ============================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id        = isset($_POST['id']) ? (int)$_POST['id'] : 0;
  $usuario   = trim($_POST['usuario'] ?? '');
  $contrasenia = $_POST['contrasenia'] ?? '';
  $role_id   = (int)($_POST['role_id'] ?? 3); // por defecto lector (3)

  if ($usuario === '') {
    $errores[] = "El nombre de usuario es obligatorio.";
  }

  if ($id === 0 && $contrasenia === '') {
    // En alta nueva, la contraseña es obligatoria
    $errores[] = "La contraseña es obligatoria para un usuario nuevo.";
  }

  // Si no hay errores, procesar
  if (empty($errores)) {
    if ($id === 0) {
      // ===========================
      // ALTA NUEVA
      // ===========================
      $hash = password_hash($contrasenia, PASSWORD_DEFAULT);

      $stmt = $conn->prepare("
                INSERT INTO usuarios (usuario, contrasenia, role_id)
                VALUES (?, ?, ?)
            ");
      $stmt->bind_param("ssi", $usuario, $hash, $role_id);
      if ($stmt->execute()) {
        $mensaje_ok = "Usuario creado correctamente.";
      } else {
        $errores[] = "No se pudo crear el usuario (¿usuario duplicado?).";
      }
      $stmt->close();
    } else {
      // ===========================
      // EDICIÓN
      // ===========================
      if ($contrasenia !== '') {
        // Actualizar también contraseña
        $hash = password_hash($contrasenia, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("
                    UPDATE usuarios 
                    SET usuario = ?, contrasenia = ?, role_id = ?
                    WHERE id = ?
                ");
        $stmt->bind_param("ssii", $usuario, $hash, $role_id, $id);
      } else {
        // Solo usuario + rol
        $stmt = $conn->prepare("
                    UPDATE usuarios 
                    SET usuario = ?, role_id = ?
                    WHERE id = ?
                ");
        $stmt->bind_param("sii", $usuario, $role_id, $id);
      }

      if ($stmt->execute()) {
        $mensaje_ok = "Usuario actualizado correctamente.";
      } else {
        $errores[] = "No se pudo actualizar el usuario.";
      }
      $stmt->close();
    }

    // Recargar datos de edición si estamos modificando un usuario
    if ($id !== 0 && empty($errores)) {
      header("Location: nuevo_usuario.php"); // para limpiar ?editar=
      exit;
    }
  }
}

// ============================================
// 4) OBTENER LISTA DE USUARIOS PARA LA TABLA
// ============================================
$sql = "
    SELECT u.id, u.usuario, u.role_id, r.nombre AS role_name
    FROM usuarios u
    LEFT JOIN roles r ON r.id = u.role_id
    ORDER BY u.id ASC
";
$resUsuarios = $conn->query($sql);
$usuarios = $resUsuarios ? $resUsuarios->fetch_all(MYSQLI_ASSOC) : [];

// ============================================
// 5) OBTENER ROLES (para el <select>)
// ============================================
$resRoles = $conn->query("SELECT id, nombre FROM roles ORDER BY id ASC");
$roles = $resRoles ? $resRoles->fetch_all(MYSQLI_ASSOC) : [];

?>
<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <title>Administrador de Usuarios</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

  <div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2>Administrador de Usuarios</h2>
      <a href="index.php" class="btn btn-outline-secondary btn-sm">Volver al panel listado de empleados</a>
    </div>

    <?php if (!empty($mensaje_ok)): ?>
      <div class="alert alert-success"><?= htmlspecialchars($mensaje_ok) ?></div>
    <?php endif; ?>

    <?php if (!empty($errores)): ?>
      <div class="alert alert-danger">
        <ul class="mb-0">
          <?php foreach ($errores as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <!-- ==========================
         FORMULARIO ALTA / EDICIÓN
         ========================== -->
    <?php
    $editando = !empty($editUser);
    ?>

    <div class="card mb-4">
      <div class="card-header">
        <?= $editando ? 'Editar usuario' : 'Nuevo usuario' ?>
      </div>
      <div class="card-body">
        <form method="post" class="row g-3">
          <?php if ($editando): ?>
            <input type="hidden" name="id" value="<?= (int)$editUser['id'] ?>">
          <?php endif; ?>

          <div class="col-md-4">
            <label class="form-label">Usuario</label>
            <input type="text"
              name="usuario"
              class="form-control"
              required
              value="<?= $editando ? htmlspecialchars($editUser['usuario']) : '' ?>">
          </div>

          <div class="col-md-4">
            <label class="form-label">
              Contraseña <?= $editando ? '(dejar vacío para no cambiar)' : '' ?>
            </label>
            <input type="password"
              name="contrasenia"
              class="form-control"
              <?= $editando ? '' : 'required' ?>>
          </div>

          <div class="col-md-4">
            <label class="form-label">Rol</label>
            <select name="role_id" class="form-select">
              <?php foreach ($roles as $r): ?>
                <option value="<?= (int)$r['id'] ?>"
                  <?= $editando && (int)$editUser['role_id'] === (int)$r['id'] ? 'selected' : '' ?>>
                  <?= htmlspecialchars($r['nombre']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="col-12">
            <button type="submit" class="btn btn-success">
              <?= $editando ? 'Guardar cambios' : 'Crear usuario' ?>
            </button>
            <?php if ($editando): ?>
              <a href="nuevo_usuario.php" class="btn btn-outline-secondary">Cancelar edición</a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>

    <!-- ==========================
         TABLA DE USUARIOS + BOTONES
         ========================== -->
    <div class="card">
      <div class="card-header">
        Usuarios existentes
      </div>
      <div class="card-body p-0">
        <table class="table table-striped table-bordered table-sm mb-0 align-middle">
          <thead class="table-dark">
            <tr>
              <th>#</th>
              <th>Usuario</th>
              <th>Rol</th>
              <th class="text-center">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($usuarios as $u): ?>
              <tr>
                <td><?= (int)$u['id'] ?></td>
                <td><?= htmlspecialchars($u['usuario']) ?></td>
                <td><?= htmlspecialchars($u['role_name'] ?? ('Rol ID ' . $u['role_id'])) ?></td>
                <td class="text-center">

                  <!-- Botón EDITAR (recarga el form con datos) -->
                  <a href="nuevo_usuario.php?editar=<?= (int)$u['id'] ?>"
                    class="btn btn-sm btn-primary">
                    Editar
                  </a>

                  <?php
                  // No permitir borrar tu propio usuario
                  $esActual = !empty($_SESSION['user_id']) && (int)$u['id'] === (int)$_SESSION['user_id'];
                  ?>
                  <?php if (!$esActual): ?>
                    <!-- Botón ELIMINAR -->
                    <a href="nuevo_usuario.php?eliminar=<?= (int)$u['id'] ?>"
                      class="btn btn-sm btn-danger"
                      onclick="return confirm('¿Seguro que querés eliminar este usuario?');">
                      Eliminar
                    </a>
                  <?php else: ?>
                    <span class="badge bg-secondary">Tu usuario</span>
                  <?php endif; ?>

                </td>
              </tr>
            <?php endforeach; ?>
            <?php if (empty($usuarios)): ?>
              <tr>
                <td colspan="4" class="text-center text-muted py-3">
                  No hay usuarios registrados.
                </td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </div>

</body>

</html>

</html>