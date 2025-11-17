<?php
// model/db.php
// Clase encargada de abrir la conexión MySQL y dejarla disponible en $this->con
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
class Db
{
  // Handler/objeto de conexión (lo usan los modelos)
  public mysqli $con;

  public function __construct()
  {
    // ==============================
    // CONFIGURACIÓN DE LA CONEXIÓN
    // ==============================
    $host = "localhost";  // Servidor MySQL (en XAMPP es localhost)
    $user = "root";       // Usuario (por defecto en XAMPP)
    $pass = "";           // Contraseña (vacía por defecto en XAMPP)
    $db   = "empresa_1";  //  NOMBRE DE BASE (asegurate que exista y tenga tus tablas)

    // ==========================================
    //  (OPCIONAL) MODO DEBUG CON EXCEPCIONES:
    // - Activá estas dos líneas cuando quieras ver errores de MySQL con detalle y línea exacta.
    // - Útil en desarrollo; en producción podés comentarlas.
    // ==========================================
    // mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    // ==========================
    // CREAR LA CONEXIÓN MYSQLI
    // ==========================
    // El @ suprime warnings; si usás el modo debug de arriba, conviene sacarlo.
    $this->con = @new mysqli($host, $user, $pass, $db);

    // Si falló la conexión, cortamos con un mensaje claro
    if ($this->con->connect_errno) {
      // Mostramos el código y el texto del error de conexión
      die("Error de conexión ({$this->con->connect_errno}): {$this->con->connect_error}");
    }

    // ================================
    // CHARSET PARA TILDES Y EMOJIS
    // ================================
    // utf8mb4 es el recomendado (mejor que utf8) para caracteres especiales
    $this->con->set_charset("utf8mb4");
  }
}
