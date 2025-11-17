<nav>
  <ul class="menu">

    <a href="../index.php">Inicio</a>
    <a href="pagina1.php">Historia y estado del proyecto</a>
    <a href="pagina2.php">Horarios de atencion y productos</a>
    <a href="pagina3.php">formulario de compra o contacto</a>
    <a href="pagina4.php">Galería de Imágenes</a>
    <a href="../trabajo_practico_dos_tablas/index.php" class="btn btn-success">Administrador de Empleados</a>
    <?php if (!empty($_GET['msg']) && $_GET['msg'] === 'logout_ok'): ?>

      <div class="alert alert-info py-2 px-3 small text-center">
        <strong>Sesión cerrada por seguridad.</strong>
      </div>
    <?php endif; ?>
  </ul>
</nav>