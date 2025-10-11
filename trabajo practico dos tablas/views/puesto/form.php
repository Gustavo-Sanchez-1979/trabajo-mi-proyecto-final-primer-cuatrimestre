<?php
// -------------------------------------------------------------------
// Vista: views/puesto/form.php
// Recibe $data desde PuestoController::{form,edit,save,update}
// -------------------------------------------------------------------
$errores = $data['errores'] ?? []; // Mensajes de validación del servidor
$puesto  = $data['puesto']  ?? null; // Datos del puesto cuando editamos
$editando = !empty($puesto);         // true si estamos en modo edición
?>

<?php if (!empty($errores)): ?>
  <!-- Bloque de errores (se muestra si el controlador pasó mensajes) -->
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errores as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<div class="card">
  <div class="card-body">

    <!--
      Un mismo formulario para "crear" y "editar":
      - Si $editando es true → action = update (POST a ?c=puesto&a=update)
      - Si $editando es false → action = save   (POST a ?c=puesto&a=save)
      La clase "row g-3" (Bootstrap) da grid + spacing entre campos.
    -->
    <form method="post" action="?c=puesto&a=<?= $editando ? 'update' : 'save' ?>" class="row g-3">

      <?php if ($editando): ?>
        <!-- ID oculto solo cuando editamos -->
        <input type="hidden" name="id" value="<?= (int)$puesto['id'] ?>">
      <?php endif; ?>

      <!-- Campo: Nombre del puesto (requerido) -->
      <div class="col-md-8">
        <label class="form-label">Puesto *</label>
        <!--
          value se completa con el dato existente si estamos editando.
          htmlspecialchars evita XSS si viniera contenido con caracteres especiales.
        -->
        <input name="nombre" class="form-control" required
               value="<?= htmlspecialchars($puesto['nombre'] ?? $puesto['puesto'] ?? '') ?>">
      </div>

      <!-- Campo: Tarea descriptiva del puesto (opcional) -->
      <div class="col-md-8">
        <label class="form-label">Tarea (opcional)</label>
        <input name="tarea" class="form-control"
               value="<?= htmlspecialchars($puesto['tarea'] ?? '') ?>">
      </div>

      <!-- Botones -->
      <div class="col-12">
        <!-- El texto del botón cambia según el modo -->
        <button class="btn btn-primary"><?= $editando ? 'Actualizar' : 'Guardar' ?></button>
        <a class="btn btn-secondary" href="?c=puesto&a=list">Cancelar</a>
      </div>
    </form>

  </div>
</div>

