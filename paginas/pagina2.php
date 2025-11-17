<?php
include_once('../funciones/funciones_formulario.php');
$arr_codigo = crear_tabla_productos();
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
      <?php include('../php/header.php'); ?>
      <?php include('../php/nav.php'); ?>
      <main>
            <section class="tablas_de_horario_y_productos">
                  <div>
                        <h3>Horario de Atención</h3>
                        <table class="tabla-estilo">
                              <tr>
                                    <th>Día</th>
                                    <th>Horario</th>
                              </tr>
                              <tr>
                                    <td>Lunes</td>
                                    <td>08:00 - 20:00</td>
                              </tr>
                              <tr>
                                    <td>Martes</td>
                                    <td>08:00 - 20:00</td>
                              </tr>
                              <tr>
                                    <td>Miercoles</td>
                                    <td>08:00 - 20:00</td>
                              </tr>
                              <tr>
                                    <td>jueves</td>
                                    <td>08:00 - 20:00</td>
                              </tr>
                              <tr>
                                    <td>viernes</td>
                                    <td>08:00 - 20:00</td>
                              </tr>
                              <tr>
                                    <td>Sabados</td>
                                    <td>05:00 - 12:00</td>
                              </tr>
                              <tr>
                                    <td>Domingo</td>
                                    <td>cerrados</td>
                              </tr>
                        </table>
                  </div>

                  <div>
                        <h2>Lista de Productos</h2>
                        <table class="tabla-estilo">
                              <thead>
                                    <tr>
                                          <th>Producto Cod.</th>
                                          <th>Descripción</th>
                                          <th>Precio</th>
                                    </tr>
                              </thead>
                              <tbody>

                                    <?php echo $arr_codigo; ?>

                              </tbody>
                        </table>
                  </div>
            </section>
      </main>

      <?php include('../php/section_aside.php'); ?>

      <?php include('../php/footer.php'); ?>

</body>

</html>