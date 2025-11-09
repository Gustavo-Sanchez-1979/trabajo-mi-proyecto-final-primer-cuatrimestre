<?php
// logout.php

// Asegurar sesión activa
if (session_status() !== PHP_SESSION_ACTIVE) {
  session_start();
}

// Vaciar variables de sesión
$_SESSION = [];

// Borrar cookie de sesión (si aplica)
if (ini_get('session.use_cookies')) {
  $params = session_get_cookie_params();
  setcookie(
    session_name(),
    '',
    time() - 42000,
    $params['path'],
    $params['domain'],
    $params['secure'],
    $params['httponly']
  );
}

// Destruir sesión
session_destroy();

// Redirigir al index público del proyecto
header("Location: /trabajo-mi-proyecto-final-primer-cuatrimestre/index.php");
exit;
