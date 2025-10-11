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
            <!-- Columnas visibles en el listado -->
            <th>ID</th>
            <th>Puesto</th> <!-- Mostramos el nombre del puesto -->
            <th>Tarea</th> <!-- Descripción/actividad asociada al puesto -->
            <th>Acciones</th> <!-- Editar / Eliminar -->
          </tr>
        </thead>

        <tbody>
          <?php if (!$puestos): ?>
            <!-- Estado vacío si no hay registros -->
            <tr>
              <td colspan="4">No hay puestos cargados.</td>
            </tr>

            <?php else: foreach ($puestos as $p): ?>
              <tr>
                <!-- ID (entero) -->
                <td><?= (int)$p['id'] ?></td>

                <!-- Nombre del puesto
                   Tu modelo devuelve SELECT id, nombre AS puesto, tarea ...
                   Por eso aquí usamos $p['puesto'].
                   Si en algún momento cambias a SELECT id, nombre, tarea ...
                   podés reemplazar por: htmlspecialchars($p['nombre'] ?? $p['puesto'] ?? '') -->
                <td><?= htmlspecialchars($p['puesto']) ?></td>

                <!-- Tarea: puede venir NULL → mostramos '(Sin tarea)' -->
                <td><?= htmlspecialchars($p['tarea'] ?? '(Sin tarea)') ?></td>

                <!-- Acciones:
                   - Editar: navega al form con ?c=puesto&a=edit&id=...
                   - Eliminar: confirma en el cliente y llama a ?c=puesto&a=delete&id=...
                   Sugerencia futura: usar POST + token CSRF para mayor seguridad. -->
                <td>
                  <a class="btn btn-sm btn-warning"
                    href="?c=puesto&a=edit&id=<?= (int)$p['id'] ?>">
                    Editar
                  </a>

                  <a class="btn btn-sm btn-danger"
                    href="?c=puesto&a=delete&id=<?= (int)$p['id'] ?>"
                    onclick="return confirm('¿Seguro que deseas eliminar este puesto?');">
                    Eliminar
                  </a>
                </td>
              </tr>
          <?php endforeach;
          endif; ?>
        </tbody>
      </table>
    </div>

  </div>
</div>