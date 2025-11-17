<?php


include_once('../funciones/funciones_formulario.php');

// resepcion de datos //
$resepcion_ok = true;
$nombre = "";
if (isset($_POST["nombre"])) {
    $nombre = (string) validarValor($_POST["nombre"]);
} else {
    $resepcion_ok = false;
}


$apellido = "";
if (isset($_POST["apellido"])) {
    $apellido = (string) validarValor($_POST["apellido"]);
} else {
    $resepcion_ok = false;
}

$email = "";
if (isset($_POST["email"])) {
    $email = (string) validarValor($_POST["email"]);
} else {
    $resepcion_ok = false;
}
// la fecha no es obligatoria//

$fecha_de_nacimiento = $_POST["Fecha_de_nacimiento"] ?? "";
if ($fecha_de_nacimiento != "") {
    list($anio, $mes, $dia) = explode("-", $fecha_de_nacimiento);
    if (!checkdate($mes, $dia, $anio)) {
        $resepcion_ok = false;
    }
}


// Separar año, mes y día


$comentario = "";
if (isset($_POST["comentario"])) {
    $comentario = (string) validarValor($_POST["comentario"]);
} else {
    $resepcion_ok = false;
}

$Cod_producto = "";
if (isset($_POST["Cod_producto"])) {
    $Cod_producto = (int) validarValor($_POST["Cod_producto"]);
} else {
    $resepcion_ok = false;
}
$seleccionar_opcion = $_POST["seleccionar_opcion"] ?? '';

if (!$resepcion_ok) {
    echo "<p>no me llegaron los datos</p>";
    exit();
} else {
    echo "<p>okey me llegaron los datos</p>";
}

echo "<pre>";
var_dump($nombre, $apellido, $email, $comentario, $Cod_producto, $seleccionar_opcion);
echo "</pre>";
// Validacion del lado del servidor //


$no_hay_errores = true;

if ($nombre == ""  || strlen($nombre) < 3 || strlen($nombre) > 30) {
    $no_hay_errores = false;
}

if ($apellido == ""  || strlen($apellido) < 3 || strlen($apellido) > 30) {
    $no_hay_errores = false;
}

if ($email == ""  || strlen($email) < 10 || strlen($email) > 50 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $no_hay_errores = false;
}

if ($comentario == ""  || strlen($comentario) < 3 || strlen($comentario) > 100) {
    $no_hay_errores = false;
}

if ($Cod_producto == ""  || $Cod_producto  < 1 || $Cod_producto  > 7) {
    $no_hay_errores = false;
}

if ($seleccionar_opcion == "" || $seleccionar_opcion < 1 || $seleccionar_opcion < 2) {
    $no_hay_errores = false;
}

if ($no_hay_errores) {
    echo "<p>Recibimos correctamente</p>
    <ul>
        <li>Nombre: $nombre</li>
        <li>Apellido: $apellido</li>
        <li>Email: $email</li>
        <li>Fecha de nacimiento: $fecha_de_nacimiento</li>
        <li>Comentario: $comentario</li>
        <li>Cod.Producto: $Cod_producto</li>
        <li>Opcion seleccionada: $seleccionar_opcion</li>
    </ul>";

    include_once('../funciones/funciones_formulario.php');

    // Si quieres hacer algo más aquí, continúa

    // Por ejemplo, redirigir o mostrar mensaje de éxito
    $mensaje = "Ok, datos recibidos correctamente";
    header("Location: errores.php?mensaje=" . urlencode($mensaje));
    exit();
} else {
    // Guardar datos en sesión para mostrar después
    $_SESSION["hay_datos"] = true;
    $_SESSION["nombre"] = $nombre;
    $_SESSION["Apellido"] = $apellido;
    $_SESSION["Email"] = $email;
    $_SESSION["fecha_de_nacimiento"] = $fecha_de_nacimiento;
    $_SESSION["Comentario"] = $comentario;
    $_SESSION["Cod.Producto"] = $Cod_producto;
    $_SESSION["Opcion.seleccionada"] = $seleccionar_opcion;


    echo "<div><a href='pagina3.php'>Volver al formulario</a></div>";
}
