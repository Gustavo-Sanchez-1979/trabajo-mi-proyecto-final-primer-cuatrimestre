<?php
session_start();
include_once('../funciones/funciones_formulario.php');
$datos_cargados = "";
      if (isset($_GET["hay_datos"])){
            $nombre_recibido = $_SESSION ["nombre"];
            $apellido_recibido = $_SESSION ["apellido"];
            $Email_recibido = $_SESSION["Email"];
            $fecha_de_nacimiento_recibida =$_SESSION["fecha_de_nacimiento"];
            $Comentario_recibido = $_SESSION["Comentario"];
            $Cod_Producto_recibido = $_SESSION["Cod_Producto"];
            $Opcion_seleccionada_recibido =  $_SESSION["Opcion_seleccionada"];
            $options_productos = generar_opciones_elegidos ();
            $Cod_Producto_recibido = $_SESSION['Cod_Producto'] ?? '';
            echo $Cod_Producto_recibido;
            $seleccionar_opcion = $_POST['seleccionar_opcion'] ?? '';
            $Cod_producto = $_POST['Cod_producto'] ?? '';
            }
       else{
            $options_productos = generar_opciones();
            $nombre_recibido = "";
            $apellido_recibido = "";
            $Email_recibido = "";
            $fecha_de_nacimiento_recibida ="";
            $Comentario_recibido = "";
            $Cod_Producto_recibido = "";
            $Opcion_seleccionada_recibido = "";
            }
?>

<!DOCTYPE html>
<html lang="es"> 
  
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../css/estilos.css">
      
</head>
    
   <body>
     <?php include_once('../php/header.php');?>
     <?php include_once('../php/nav.php');?>

        <h3 style="font-size: 32px; color: black;">
        Contactanos llenando este formulario para comprar nuestros productos o si queres saver de nosotros:
        </h3>

     <section class="formulario">

          <form method= "post" action="recibe_contacto.php">
               <div>Nombre: <input type="text" name="nombre" maxlength= "30" minlength="3" placeholder= "Escribe tu nombre aquí" required value="<?php echo $nombre_recibido?>"></div>
               <div>Apellido: <input type="text" name="apellido" maxlength= "30" minlength="3" placeholder= "Escribe tu apellido aquí" requiredvalue="<?php echo $apellido_recibido?>"></div>
               <div>Email: <input type="email" name="email" maxlength= "50" minlength="10"placeholder="Escribe tu email aquí" requiredvalue="<?php echo $Email_recibido?>"></div>
               <div>Fecha de nacimiento: <input type="date" name="Fecha_de_nacimiento" placeholder="Escribe aquí tu Fecha de nacimiento" norequiredvalue="<?php echo $nombre_recibido?>"></div>
               <div>Comentario <input type="text" name="comentario" maxlength= "100" minlength="3" placeholder="comentario Obligatorio" requiredvalue="<?php echo $Comentario_recibido_recibido?>"></div>
    
               <div> Seleccione Nuestro producto a comprar
                    <select name = "Cod_producto" required>
                         <option value=""> Seleccione Cod. Producto </option>
                         <?php echo $options_productos;?>
                    </select>
               </div>
               <div> Seleccione una opcion </div>
               <div name = "selecccionar_opcion">        
                    <input type="radio" name="selecccionar_opcion" class="radiochecks" value = 1  /><span>Comprar</span>
                    <input type="radio" name="selecccionar_opcion" class="radiochecks" value = 2 checked/><span>Contactarnos</span>
               </div>
              
                   <input type="submit" value="Enviar">
                   <input type="reset" value="Resetear"> 
               
          </form>

     </section>

     <?php include('../php/section_aside.php'); ?>

     <?php include('../php/footer.php'); ?>

   </body> 

</html>

