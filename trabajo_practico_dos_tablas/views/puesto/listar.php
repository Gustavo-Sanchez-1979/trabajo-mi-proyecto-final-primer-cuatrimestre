<?php
// -------------------------------------------------------------------
// Vista: views/puesto/listar.php
// Recibe $data['puestos'] desde PuestoController::list()
// -------------------------------------------------------------------

// Aseguramos una variable de trabajo aún si viene vacío
$puestos = $data['puestos'] ?? [];
?>

<div class="card">
  <div class="card-body">

    <!-- Encabezado con botón para ir al formulario de alta -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Puestos registrados</h5>
      <a class="btn btn-primary" href="?c=puesto&a=form">+ Nuevo puesto</a>
    </div>

    <!-- Tabla responsiva (Bootstrap) -->
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle text-center"
        style="font-size: 0.9rem; table-layout: fixed; width: 100%;">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Puesto</th>
            <th>Tarea</th>
            <th>Total de empleados asignados por puesto</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody>
          <?php if (empty($puestos)): ?>
            <tr>
              <td colspan="5">No hay puestos cargados.</td>
            </tr>
            <?php else: foreach ($puestos as $p): ?>
              <tr>
                <td><?= (int)$p['id'] ?></td>
                <td><?= htmlspecialchars($p['puesto'] ?? $p['nombre'] ?? '') ?></td>
                <td><?= htmlspecialchars($p['tarea']  ?? '(Sin tarea)') ?></td>
                <td><small><?= (int)($p['can_empleados'] ?? 0) ?> empleado(s)</small></td>
                <td>
                  <a class="btn btn-sm btn-warning"
                    href="?c=puesto&a=edit&id=<?= (int)$p['id'] ?>">Editar</a>

                  <?php if ((int)($p['can_empleados'] ?? 0) > 0): ?>
                    <!-- Botón deshabilitado con tooltip -->
                    <span class="d-inline-block" tabindex="0"
                      data-bs-toggle="tooltip"
                      data-bs-placement="top"
                      data-bs-title="No se puede eliminar el puesto está asignado a empleados">
                      <button class="btn btn-sm btn-danger" type="button" disabled style="pointer-events: none;">
                        Eliminar
                      </button>
                    </span>
                  <?php else: ?>
                    <!-- Botón normal (se puede eliminar) -->
                    <a class="btn btn-sm btn-danger"
                      href="?c=puesto&a=delete&id=<?= (int)$p['id'] ?>"
                      onclick="return confirm('¿Seguro que deseas eliminar este puesto?, igual puedes eliminarlo por que no esta asignado a ningun empleado');">
                      Eliminar
                    </a>
                  <?php endif; ?>
                </td>
              </tr>
          <?php endforeach;
          endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>