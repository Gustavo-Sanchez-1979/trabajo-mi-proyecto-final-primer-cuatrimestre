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
<form method="post" action="?c=empleado&a=<?= $editando ? 'update' : 'save' ?>" class="row g-3">

  <?php if ($editando): ?>
    <!-- Campo oculto con el ID del empleado para la actualización -->
    <input type="hidden" name="id" value="<?= (int)$empleado['id'] ?>">
  <?php endif; ?>

  <!-- Nombre (requerido) -->
  <div class="col-md-4">
    <label class="form-label">Nombre *</label>
    <!--
      value usa el operador null-coalescing para precargar en edición
      htmlspecialchars evita XSS si el valor viene con caracteres especiales
    -->
    <input name="nombre" class="form-control" required
           value="<?= htmlspecialchars($empleado['nombre'] ?? '') ?>">
  </div>

  <!-- Apellido (requerido) -->
  <div class="col-md-4">
    <label class="form-label">Apellido *</label>
    <input name="apellido" class="form-control" required
           value="<?= htmlspecialchars($empleado['apellido'] ?? '') ?>">
  </div>

  <!-- DNI (requerido). Podés agregar pattern="\d{7,11}" para validar solo números -->
  <div class="col-md-4">
    <label class="form-label">DNI *</label>
    <input name="dni" class="form-control" required
           value="<?= htmlspecialchars($empleado['dni'] ?? '') ?>">
  </div>

  <!-- Empresa (requerido) -->
  <div class="col-md-6">
    <label class="form-label">Empresa *</label>
    <input name="empresa" class="form-control" required
           value="<?= htmlspecialchars($empleado['empresa'] ?? '') ?>">
  </div>

  <!-- Puesto (FK) (requerido)
       - Llena el <select> con $puestos
       - Marca selected si el empleado ya tiene puesto_id (en edición)
       - OJO: según tu modelo Puesto::all():
         * Si usás SELECT id, nombre AS puesto, tarea ... → mostrar $p['puesto']
         * Si usás SELECT id, nombre, tarea ...           → mostrar $p['nombre']
       Abajo usamos un fallback: primero intenta $p['nombre'], si no existe usa $p['puesto'].
  -->
  <div class="col-md-6">
    <label class="form-label">Puesto (FK) *</label>
    <select name="puesto_id" class="form-select" required>
      <option value="">Seleccionar…</option>
      <?php foreach ($puestos as $p): ?>
        <option value="<?= (int)$p['id'] ?>"
          <?= (isset($empleado['puesto_id']) && (int)$empleado['puesto_id'] === (int)$p['id']) ? 'selected' : '' ?>>
          <?= htmlspecialchars($p['nombre'] ?? $p['puesto'] ?? '') ?>
        </option>
      <?php endforeach; ?>
    </select>
  </div>

  <!-- Domicilio (opcional) -->
  <div class="col-md-6">
    <label class="form-label">Domicilio</label>
    <input name="domicilio" class="form-control"
           value="<?= htmlspecialchars($empleado['domicilio'] ?? '') ?>">
  </div>

  <!-- Ciudad (opcional) -->
  <div class="col-md-6">
    <label class="form-label">Ciudad</label>
    <input name="ciudad" class="form-control"
           value="<?= htmlspecialchars($empleado['ciudad'] ?? '') ?>">
  </div>

  <!-- Provincia (opcional) -->
  <div class="col-md-6">
    <label class="form-label">Provincia</label>
    <input name="provincia" class="form-control"
           value="<?= htmlspecialchars($empleado['provincia'] ?? '') ?>">
  </div>

  <!-- País (opcional, por defecto Argentina) -->
  <div class="col-md-6">
    <label class="form-label">País</label>
    <input name="pais" class="form-control"
           value="<?= htmlspecialchars($empleado['pais'] ?? 'Argentina') ?>">
  </div>

  <!-- Teléfono (opcional) -->
  <div class="col-md-6">
    <label class="form-label">Teléfono</label>
    <input name="telefono" class="form-control"
           value="<?= htmlspecialchars($empleado['telefono'] ?? '') ?>">
  </div>

  <!-- Email (opcional, type=email hace validación básica del lado del navegador) -->
  <div class="col-md-6">
    <label class="form-label">Email</label>
    <input name="email" type="email" class="form-control"
           value="<?= htmlspecialchars($empleado['email'] ?? '') ?>">
  </div>

  <!-- Botones de acción -->
  <div class="col-12">
    <!-- El texto del botón cambia según el modo -->
    <button class="btn btn-primary"><?= $editando ? 'Actualizar' : 'Guardar' ?></button>
    <a class="btn btn-secondary" href="?c=empleado&a=list">Cancelar</a>
  </div>
</form>






