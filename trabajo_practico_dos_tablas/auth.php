<?php
// ================================
// Archivo: auth.php
// Función: Protege el acceso a las páginas internas del panel.
// Si el usuario no inició sesión, lo redirige al login.
// ================================

// Verifica si la sesión NO está activa
if (session_status() !== PHP_SESSION_ACTIVE) {
    // Si no hay una sesión iniciada, la inicia
    session_start();
    // Esto permite acceder a variables como $_SESSION['usuario']
}

// Verifica si la variable de sesión 'usuario' está vacía o no existe
// Esa variable se crea en login.php cuando el usuario inicia sesión correctamente
if (empty($_SESSION['usuario'])) {

    // Si no hay usuario logueado, redirige al archivo login.php
    // Como está en la misma carpeta que este archivo, no necesita ruta completa
    header("Location: login.php");

    // Detiene por completo la ejecución del script
    // Esto asegura que no se siga mostrando contenido del panel
    exit();
}


/* Si NO está logueado, lo mando al login del proyecto final */
if (!isset($_SESSION['usuario'])) {
    header("Location: /trabajo-mi-proyecto-final-primer-cuatrimestre
/login.php");
    exit();
}

// Si el usuario está logueado, el script continúa normalmente
// (no hace nada más, simplemente deja pasar la ejecución)