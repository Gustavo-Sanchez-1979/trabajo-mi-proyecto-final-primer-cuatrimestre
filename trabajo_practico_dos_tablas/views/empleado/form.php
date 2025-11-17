<?php
// -------------------------------------------------------------------
// Datos que llegan del controlador (EmpleadoController)
// -------------------------------------------------------------------
$errores  = $data['errores']  ?? [];   // array de errores de validación (si los hubo)
$puestos  = $data['puestos']  ?? [];   // lista de puestos para llenar el <select>
$empleado = $data['empleado'] ?? null; // datos del empleado al editar (null si es alta)
$editando = !empty($empleado);         // true si estamos editando (hay datos), false si es alta nueva
?>

<?php if (!empty($errores)): ?>
  <!-- Muestra mensajes de validación del lado servidor -->
  <div class="alert alert-danger">
    <ul class="mb-0">
      <?php foreach ($errores as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<!--
  Formulario “2 en 1”:
  - Si $editando es true → action = update (POST a ?c=empleado&a=update)
  - Si $editando es false → action = save   (POST a ?c=empleado&a=save)
  La clase "row g-3" usa el grid de Bootstrap con separación (gutter) entre inputs.
-->
<form method="post" action="?c=empleado&a=save" class="row g-3">
  <div class="col-md-6">
    <label class="form-label">Nombre</label>
    <input name="nombre" class="form-control" required>
  </div>

  <div class="col-md-6">
    <label class="form-label">Apellido</label>
    <input name="apellido" class="form-control" required>
  </div>

  <div class="col-md-4">
    <label class="form-label">DNI</label>
    <input name="dni" class="form-control" required>
  </div>

  <div class="col-md-8">
    <label class="form-label">Empresa</label>
    <input name="empresa" class="form-control" required>
  </div>

  <div class="col-md-12">
    <label class="form-label">Domicilio</label>
    <input name="domicilio" class="form-control">
  </div>

  <div class="col-md-4">
    <label class="form-label">Ciudad</label>
    <input name="ciudad" class="form-control">
  </div>

  <div class="col-md-4">
    <label class="form-label">Provincia</label>
    <input name="provincia" class="form-control">
  </div>

  <div class="col-md-4">
    <label class="form-label">País</label>
    <input name="pais" class="form-control" value="Argentina">
  </div>

  <div class="col-md-6">
    <label class="form-label">Teléfono</label>
    <input name="telefono" class="form-control">
  </div>

  <div class="col-md-6">
    <label class="form-label">Email</label>
    <input name="email" type="email" class="form-control">
  </div>

  <div class="col-md-6">
    <label class="form-label">Puesto</label>
    <select name="puesto_id" class="form-select" required>
      <option value="">-- Seleccionar --</option>
      <?php foreach (($data['puestos'] ?? []) as $p): ?>
        <option value="<?= (int)$p['id'] ?>">
          <?= htmlspecialchars($p['puesto'] ?? $p['nombre']) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-12">
    <button type="submit" class="btn btn-success">Guardar</button>
    <a href="?c=empleado&a=list" class="btn btn-outline-secondary">Cancelar</a>
  </div>
</form>





