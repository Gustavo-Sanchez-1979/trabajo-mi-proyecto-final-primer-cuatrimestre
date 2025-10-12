<?php
// Inicia o retoma la sesión actual. Es necesario para poder destruirla después.
session_start();

// Destruye por completo la sesión del usuario (borra los datos de $_SESSION y el id de sesión).
session_destroy();

// Redirige al usuario al inicio público del sitio (cambiá la ruta si tu carpeta tiene otro nombre).
header("Location: /miproyectofinal/index.php");

// Asegura que el script termine aquí y no siga ejecutando nada más después de la redirección.
exit();
