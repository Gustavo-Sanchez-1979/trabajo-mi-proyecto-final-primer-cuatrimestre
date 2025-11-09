<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

/* ¿Está logueado? */
function isLoggedIn(): bool {
  return !empty($_SESSION['user_id']);
}

/* Rol actual ('admin' | 'editor' | 'lector' | 'anon') */
function currentRole(): string {
  return $_SESSION['user_role'] ?? 'anon';
}

/* Exigir login (para ver listados, etc.) */
function requireLogin(): void {
  if (!isLoggedIn()) {
    http_response_code(401);
    header("Location: login.php");
    exit();
  }
}

/* Exigir rol (para editar/eliminar) */
function requireRole(array $allowed): void {
  requireLogin();
  $role = currentRole();

  if (!in_array($role, $allowed, true)) {
    http_response_code(403);

    // Cartel vistoso y centrado (usa Bootstrap 5)
    die('
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <title>Acceso restringido</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body{background:#0d1117;color:#e6edf3;}
    .card{
      max-width: 720px;
      margin: 8vh auto;
      border: none;
      border-radius: 1rem;
      box-shadow: 0 20px 45px rgba(0,0,0,.35);
      background: linear-gradient(135deg,#1f2937,#0f172a);
      color: #e6edf3;
    }
    .display-icon{
      font-size: 64px;
      line-height: 1;
    }
    .role-badge{
      background:#dc3545; 
      margin: 0 .25rem;
      font-weight: 600;
    }
    .need-badge{
      background:#ffc107;
      color:#111827;
      font-weight:700;
    }
  </style>
</head>
<body>
  <div class="container px-3">
    <div class="card p-4 p-md-5 text-center">
      <div class="display-icon mb-3">🚫</div>
      <h1 class="display-6 fw-bold mb-2">Acceso restringido</h1>
      <p class="lead mb-4">
        Tu rol actual no tiene permiso para ver esta sección.
      </p>

      <div class="mb-4">
        <span class="badge rounded-pill need-badge">Se requiere:</span>
        ' . implode(" / ", array_map(fn($r)=>"<span class=\"badge rounded-pill role-badge\">".htmlspecialchars($r)."</span>", $allowed)) . '
      </div>

      <div class="d-flex flex-wrap justify-content-center gap-2">
      
        <button class="btn btn-light btn-lg" onclick="history.back()">Volver atrás</button>
      </div>

      <hr class="my-4 opacity-25">
      <small class="text-secondary">Código de error: 403 • Rol actual: <strong>'.htmlspecialchars($role).'</strong></small>
    </div>
  </div>
</body>
</html>');
  }
}
