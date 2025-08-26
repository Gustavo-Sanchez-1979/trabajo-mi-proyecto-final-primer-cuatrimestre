
<?php
include_once("../arreglos/arreglos.php");
     // esta funcion genera las opciones de cualquier seleccion la usamos para  nuestros productos //
     //arreglo 1 //
     
    
     function generar_opciones(){ 
                 global $arr_productos;
              
                 $opciones="";
                 foreach ( $arr_productos as $key => $value ) {
                      $opciones.= "<option value= '$key'>$value</option>";
                    
                      }
                      return  $opciones;
                }
    // fin de la funcion generar opciones//
    

    $seleccionar_opcion = isset($_POST['seleccionar_opcion']) ? (int) validarValor($_POST['seleccionar_opcion']) : 0; 


    // funcion para limpiar datos formulario //
    function validarValor($data) {
    // Quitamos los espacios, tanto en el principio como en el final
    $data = trim($data);
    // Quitamos las barras invertidas \
    $data = stripslashes($data);
    // Convierte caracteres especiales a HTML por ejemplo "<" a "&lt;", 
    // sirve para prevenir ataques de inyección de código
    $data = htmlspecialchars($data);
    return $data;
    }
// funcion para que quede el elegido en una funcion//
    function generar_opciones_elegido($indice){ 

                 global $arr_productos;

                 $opciones="";
                 foreach ( $arr_productos as $key => $value ) {
                           $elegido = "";
                           if( $indice == $key ){
                              $elegido = "selectd";
                           }
                            $opciones.= "<option value= '$key'>$value</option>";
                         }
                          return  $opciones;
               }

     function crear_tabla_productos(){ 
                 global $arr_codigo,$arr_descripcion,$arr_precio;
              
                 $opciones="";
                 foreach ( $arr_codigo as $key => $value ) {
                      $opciones.="<tr><td>$value</td><td>$arr_descripcion[$key]</td><td>$arr_precio[$key]</td></tr>";
                    
                      }
                      return  $opciones;
                }

?>





