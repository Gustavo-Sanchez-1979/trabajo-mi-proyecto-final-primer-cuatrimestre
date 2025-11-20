<?php
// -------------------------------------------------------------------
// Datos que llegan del controlador (EmpleadoController)
// -------------------------------------------------------------------
$errores  = $data['errores']  ?? [];   // array de errores de validación (si los hubo)
$puestos  = $data['puestos']  ?? [];   // lista de puestos para llenar el <select>
$empleado = $data['empleado'] ?? null; // datos del empleado al editar (null si es alta)
$editando = !empty($empleado);         // true si estamos editando, false si es alta

// Normalizamos campos para usarlos tanto en alta como en edición
$id         = $empleado['id']        ?? null;
$nombre     = $empleado['nombre']    ?? '';
$apellido   = $empleado['apellido']  ?? '';
$dni        = $empleado['dni']       ?? '';
$empresa    = $empleado['empresa']   ?? '';
$domicilio  = $empleado['domicilio'] ?? '';
$ciudad     = $empleado['ciudad']    ?? '';
$provincia  = $empleado['provincia'] ?? '';
$pais       = $empleado['pais']      ?? 'Argentina';
$telefono   = $empleado['telefono']  ?? '';
$email      = $empleado['email']     ?? '';
$puesto_id  = $empleado['puesto_id'] ?? '';
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
-->
<form method="post"
  action="?c=empleado&a=<?= $editando ? 'update' : 'save'; ?>"
  class="row g-3">

  <?php if ($editando): ?>
    <!-- ID oculto solo en edición -->
    <input type="hidden" name="id" value="<?= (int)$id ?>">
  <?php endif; ?>

  <div class="col-12">
    <h4 class="mb-3">
      <?= $editando ? "Editar empleado #{$id}" : "Nuevo empleado"; ?>
    </h4>
  </div>

  <div class="col-md-6">
    <label class="form-label">Nombre</label>
    <input name="nombre"
      class="form-control"
      required
      value="<?= htmlspecialchars($nombre) ?>">
  </div>

  <div class="col-md-6">
    <label class="form-label">Apellido</label>
    <input name="apellido"
      class="form-control"
      required
      value="<?= htmlspecialchars($apellido) ?>">
  </div>

  <div class="col-md-4">
    <label class="form-label">DNI</label>
    <input name="dni"
      class="form-control"
      required
      value="<?= htmlspecialchars($dni) ?>">
  </div>

  <div class="col-md-8">
    <label class="form-label">Empresa</label>
    <input name="empresa"
      class="form-control"
      required
      value="<?= htmlspecialchars($empresa) ?>">
  </div>

  <div class="col-md-12">
    <label class="form-label">Domicilio</label>
    <input name="domicilio"
      class="form-control"
      value="<?= htmlspecialchars($domicilio) ?>">
  </div>

  <div class="col-md-4">
    <label class="form-label">Ciudad</label>
    <input name="ciudad"
      class="form-control"
      value="<?= htmlspecialchars($ciudad) ?>">
  </div>

  <div class="col-md-4">
    <label class="form-label">Provincia</label>
    <input name="provincia"
      class="form-control"
      value="<?= htmlspecialchars($provincia) ?>">
  </div>

  <div class="col-md-4">
    <label class="form-label">País</label>
    <input name="pais"
      class="form-control"
      value="<?= htmlspecialchars($pais) ?>">
  </div>

  <div class="col-md-6">
    <label class="form-label">Teléfono</label>
    <input name="telefono"
      class="form-control"
      value="<?= htmlspecialchars($telefono) ?>">
  </div>

  <div class="col-md-6">
    <label class="form-label">Email</label>
    <input name="email"
      type="email"
      class="form-control"
      value="<?= htmlspecialchars($email) ?>">
  </div>

  <div class="col-md-6">
    <label class="form-label">Puesto</label>
    <select name="puesto_id" class="form-select" required>
      <option value="">-- Seleccionar --</option>
      <?php foreach ($puestos as $p): ?>
        <?php
        $pid      = (int)$p['id'];
        $etiqueta = $p['puesto'] ?? ($p['nombre'] ?? '');
        ?>
        <option value="<?= $pid ?>"
          <?= ($puesto_id == $pid) ? 'selected' : '' ?>>
          <?= htmlspecialchars($etiqueta) ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="col-12">
    <button type="submit" class="btn btn-success">
      <?= $editando ? 'Actualizar' : 'Guardar'; ?>
    </button>
    <a href="?c=empleado&a=list" class="btn btn-outline-secondary">Cancelar</a>
  </div>
</form>

