<?php
require_once __DIR__ . '/db.php'; // Conexión a MySQL

class Puesto
{
  /** Handler de conexión MySQLi usado por todos los métodos */
  private mysqli $con;

  public function __construct()
  {
    // Instanciamos la conexión y la guardamos en $this->con
    $db = new Db();
    $this->con = $db->con;
  }


  // LISTADO

  /**
   * all()
   * Devuelve TODOS los puestos con la cantidad de empleados asignados.
   * - Usamos alias "puesto" para el nombre, así la vista puede imprimir $p['puesto'].
   * - Contamos con COUNT(b.id) (y no COUNT(*)) para que los puestos sin empleados cuenten 0.
   */
  public function all(): array
  {
    $sql = '
      SELECT
        a.id,
        a.nombre AS puesto,   -- alias amigable para la vista
        a.tarea,
        COUNT(b.id) AS can_empleados
      FROM puestos a
      LEFT JOIN empleados b ON b.puesto_id = a.id
      GROUP BY a.id, a.nombre, a.tarea
      ORDER BY a.nombre
    ';
    $res = $this->con->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
  }


  // REGLA DE NEGOCIO: NO BORRAR SI ESTÁ ASIGNADO

  /**
   * canDelete($id)
   * Devuelve TRUE si el puesto NO tiene empleados asignados (se puede borrar).
  
   * Nota: con LEFT JOIN + COUNT(*), si no hay empleados, sigue habiendo 1 fila (la del puesto).
   * Por eso, si b.puesto_id es NULL, forzamos can_empleados = 0 en PHP.
   */
  public function canDelete(int $id): bool
  {
    $sql = "
      SELECT a.*, b.puesto_id, COUNT(*) AS can_empleados
      FROM puestos a
      LEFT JOIN empleados b ON a.id = b.puesto_id
      WHERE a.id = ?
      GROUP BY 1,2,3,4
    ";
    $stmt = $this->con->prepare($sql);
    if (!$stmt) return false;

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $row = $res->fetch_assoc();

    if (!$row) {
      // El puesto no existe → por seguridad, consideramos que no se puede borrar
      return false;
    }

    // Ajuste: si no hay empleados asignados, b.puesto_id vendrá NULL.
    // En ese caso, aunque COUNT(*) sea 1 por la fila del puesto, forzamos a 0.
    $count = (int)$row['can_empleados'];
    if (is_null($row['puesto_id'])) {
      $count = 0;
    }

    // Se puede borrar solo si NO hay empleados asignados
    return $count === 0;
  }

  /**
   * delete($id)
   * Borra el puesto SOLO si canDelete($id) dice que no hay empleados asignados.
   * Devuelve true si se eliminó, false si no se pudo (asignado o error de prepare/execute).
   */
  public function delete(int $id): bool
  {
    // Bloqueo por regla de negocio: si está asignado, no lo borro
    if (!$this->canDelete($id)) {
      return false;
    }
    $stmt = $this->con->prepare('DELETE FROM puestos WHERE id = ?');
    if (!$stmt) return false;

    $stmt->bind_param("i", $id);
    return $stmt->execute();
  }
  // ABM (crear / actualizar / buscar)

  /**
   * create($nombre, $tarea)
   * Inserta un nuevo puesto usando sentencia preparada (evita SQLi).
   * Devuelve true/false según éxito del INSERT (puede fallar por UNIQUE(nombre)).
   */
  public function create(string $nombre, string $tarea): bool
  {
    $stmt = $this->con->prepare('INSERT INTO puestos (nombre, tarea) VALUES (?, ?)');
    if (!$stmt) return false;

    $stmt->bind_param("ss", $nombre, $tarea);
    return $stmt->execute();
  }

  /**
   * update($id, $nombre, $tarea)
   * Actualiza el nombre y la tarea del puesto por ID.
   * Devuelve true/false según éxito del UPDATE (puede fallar por UNIQUE(nombre)).
   */
  public function update(int $id, string $nombre, string $tarea): bool
  {
    $stmt = $this->con->prepare('UPDATE puestos SET nombre = ?, tarea = ? WHERE id = ?');
    if (!$stmt) return false;

    $stmt->bind_param("ssi", $nombre, $tarea, $id);
    return $stmt->execute();
  }

  /**
   * find($id)
   * Trae un puesto por ID. Devuelve array asociativo o null si no existe.
   * Útil para precargar el formulario de edición.
   */
  public function find(int $id): ?array
  {
    $stmt = $this->con->prepare('SELECT * FROM puestos WHERE id = ?');
    if (!$stmt) return null;

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc() ?: null;
  }
}
