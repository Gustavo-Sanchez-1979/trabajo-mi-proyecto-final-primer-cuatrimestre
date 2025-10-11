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
            <!-- Encabezados de columnas (deben coincidir con lo que seleccionás en el modelo) -->
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>DNI</th>
            <th>Empresa</th>
            <th>Domicilio</th>
            <th>Ciudad</th>
            <th>Email</th>
            <th>Puesto</th>
            <th>Creado</th>
            <th>Acciones</th> <!-- ⬅️ MISMO LUGAR QUE EN PUESTOS: AL FINAL -->
          </tr>
        </thead>

        <tbody>
          <?php if (empty($lista)): ?>
            <!-- Estado vacío: no hay registros -->
            <tr>
              <td colspan="11">No hay empleados cargados.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($lista as $e): ?>
              <tr>
                <!-- celdas: salidas sanitizadas con htmlspecialchars para evitar XSS -->
                <td><?= (int)$e['id'] ?></td>
                <td><?= htmlspecialchars($e['nombre']) ?></td>
                <td><?= htmlspecialchars($e['apellido']) ?></td>
                <td><?= htmlspecialchars($e['dni']) ?></td>
                <td><?= htmlspecialchars($e['empresa']) ?></td>
                <td><?= htmlspecialchars($e['domicilio'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['ciudad'] ?? '') ?></td>
                <td><?= htmlspecialchars($e['email'] ?? '') ?></td>

                <!-- badge para resaltar el puesto; viene de COALESCE(...) AS puesto en el modelo -->
                <td><span class="badge bg-secondary"><?= htmlspecialchars($e['puesto']) ?></span></td>

                <!-- fecha/hora de creación si tu tabla la tiene (creado_en) -->
                <td><small><?= htmlspecialchars($e['creado_en'] ?? '') ?></small></td>

                <!-- Acciones: Editar / Eliminar -->
                <td>
                  <!-- Editar: navega al form de edición con el ID -->
                  <a class="btn btn-sm btn-warning"
                    href="?c=empleado&a=edit&id=<?= (int)$e['id'] ?>">
                    Editar
                  </a>

                  <!-- Eliminar: GET + confirmación en cliente.
                       Sugerencia de mejora: hacerlo por POST con token CSRF. -->
                  <a class="btn btn-sm btn-danger"
                    href="?c=empleado&a=delete&id=<?= (int)$e['id'] ?>"
                    onclick="return confirm('¿Eliminar empleado?');">
                    Eliminar
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>