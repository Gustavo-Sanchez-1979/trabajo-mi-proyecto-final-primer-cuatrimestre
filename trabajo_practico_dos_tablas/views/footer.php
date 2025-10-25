<?php
// -------------------------------------------------------------------
// Vista parcial: views/_layout_footer.php
// Cierra el <div class="container"> abierto en _layout_header.php
// + Footer sencillo y carga de JS de Bootstrap.
// -------------------------------------------------------------------
?>

</div> <!-- /.container (abierto en _layout_header.php) -->

<footer class="text-center py-3 bg-dark text-white mt-auto">
  <!--
    Mostramos el año actual dinámicamente.
    Podés cambiar el texto por el nombre de tu app/empresa.
  -->
  <small>© <?= date('Y') ?> — Demo MVC (PHP 8 + MySQL)</small>
</footer>

<!--
  Bootstrap Bundle incluye Popper, suficiente para tooltips/dropdowns.
  Si necesitás otros scripts, agregalos debajo para que no bloqueen el render.
-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!--
  (Opcional) Lugar recomendado para tus scripts propios:
  <script src="/assets/js/app.js"></script>
-->
<script>
  // Activa todos los tooltips del documento
  document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function (el) {
      new bootstrap.Tooltip(el);
    });
  });
</script>
</body>
</html>
