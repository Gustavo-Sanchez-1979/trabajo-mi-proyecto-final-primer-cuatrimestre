<?php
// -------------------------------------------------------------------
// Vista: views/empleado/listar.php
// Llega $data desde el controlador EmpleadoController::list()
// -------------------------------------------------------------------
$lista = $data['empleados'] ?? []; // Array de empleados (cada item es un row asociativo)
?>

<div class="card">
  <div class="card-body">

    <!-- Encabezado + botón de alta -->
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h5 class="mb-0">Empleados</h5>
      <a class="btn btn-success" href="?c=empleado&a=form">+ Nuevo empleado</a>
    </div>

    <!-- Tabla responsiva (bootstrap) -->
    <div class="table-responsive">
      <table class="table table-bordered table-striped align-middle text-center"
        style="font-size: 0.9rem; table-layout: fixed; width: 100%;">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>DNI</th>
            <th>Empresa</th>
            <th>Domicilio</th>
            <th>Ciudad</th>
            <th>Email</th>
            <th>Puesto</th>
            <th>Tarea</th> <!-- NUEVA -->
            <th>Creado</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($lista)): ?>
            <tr>
              <td colspan="12">No hay empleados cargados.</td>
            </tr>
            <?php else: foreach ($lista as $e): ?>
              <tr>
                <td><?= (int)$e['id'] ?></td>
                <td><?= htmlspecialchars($e['nombre']) ?></td>
                <td><?= htmlspecialchars($e['apellido']) ?></td>
                <td><?= htmlspecialchars($e['dni']) ?></td>
                <td><?= htmlspecialchars($e['empresa']) ?></td>
                <td><?= htmlspecialchars($e['domicilio'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['ciudad'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['email'] ?? '') ?></td>
                <td><span class="badge bg-secondary"><?= htmlspecialchars($e['puesto']) ?></span></td>
                <td><?= htmlspecialchars($e['tarea_puesto']) ?></td> <!-- NUEVA -->
                <td><small><?= htmlspecialchars($e['creado_en'] ?? '') ?></small></td>
                <td>
                  <a class="btn btn-sm btn-warning" href="?c=empleado&a=edit&id=<?= (int)$e['id'] ?>">Editar</a>
                  <a class="btn btn-sm btn-danger"
                    href="?c=empleado&a=delete&id=<?= (int)$e['id'] ?>"
                    onclick="return confirm('¿Eliminar empleado?');">Eliminar</a>
                </td>
              </tr>
          <?php endforeach;
          endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>