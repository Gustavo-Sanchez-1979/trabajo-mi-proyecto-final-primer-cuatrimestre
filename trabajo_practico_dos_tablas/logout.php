<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

// Vaciar variables de sesión
$_SESSION = [];

// Borrar cookie de sesión (si aplica)
if (ini_get('session.use_cookies')) {
  $p = session_get_cookie_params();
  setcookie(session_name(), '', time() - 42000, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
}

// Destruir la sesión
session_destroy();

// Sanitizar y decidir destino
$next = $_GET['next'] ?? '/trabajo-mi-proyecto-final-primer-cuatrimestre/index.php';
$m    = $_GET['m']    ?? '';

// Evitar open-redirect: solo permitir rutas internas absolutas
if (!preg_match('#^/[a-zA-Z0-9/_\-.]+(\.php)?(\?.*)?$#', $next)) {
  $next = '/trabajo-mi-proyecto-final-primer-cuatrimestre/index.php';
}

// Reagregar query de mensaje si viene
if ($m !== '') {
  $sep  = (strpos($next, '?') === false) ? '?' : '&';
  $next = $next . $sep . 'msg=' . urlencode($m);
}

header("Location: {$next}");
exit;
