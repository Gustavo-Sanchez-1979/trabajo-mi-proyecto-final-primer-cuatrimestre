<?php
require_once __DIR__ . '/db.php'; // ⬅️ Conexión a MySQL

class Puesto {
  // Handler de conexión que usan todos los métodos
  private mysqli $con;

  public function __construct() {
    // Instancia la conexión y la guarda en $this->con
    $db = new Db();
    $this->con = $db->con;
  }

  /**
   * all()
   * Devuelve todos los puestos ordenados por nombre.
   * Nota: devolvemos 'nombre AS puesto' para que en la vista puedas usar $p['puesto'].
   *       Si preferís $p['nombre'], quitá el alias.
   */
  public function all(): array {
    $sql = "SELECT id, nombre AS puesto, tarea FROM puestos ORDER BY nombre";
    $res = $this->con->query($sql);
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];
  }

  /**
   * create($nombre, $tarea)
   * Inserta un nuevo puesto usando sentencia preparada (evita SQL injection).
   * Devuelve true/false según éxito del INSERT (p.ej. puede fallar por UNIQUE(nombre)).
   */
  public function create(string $nombre, string $tarea): bool {
    $stmt = $this->con->prepare(
      "INSERT INTO puestos (nombre, tarea) VALUES (?, ?)"
    );
    if (!$stmt) return false;
    $stmt->bind_param("ss", $nombre, $tarea); // s=string, s=string
    return $stmt->execute();
  }

  /**
   * update($id, $nombre, $tarea)
   * Actualiza el nombre y la tarea del puesto por ID.
   * Devuelve true/false según éxito del UPDATE (puede fallar por UNIQUE(nombre)).
   */
  public function update(int $id, string $nombre, string $tarea): bool {
    $stmt = $this->con->prepare(
      "UPDATE puestos SET nombre = ?, tarea = ? WHERE id = ?"
    );
    if (!$stmt) return false;
    $stmt->bind_param("ssi", $nombre, $tarea, $id); // s,s,i
    return $stmt->execute();
  }

  /**
   * delete($id)
   * Versión SENCILLA: borra directo. Si hay FK en empleados, podría fallar.
   * Recomendación: usar la versión SEGURA de abajo (descomentá y comentá esta).
   */
  public function delete(int $id): bool {
    $stmt = $this->con->prepare("DELETE FROM puestos WHERE id = ?");
    if (!$stmt) return false;
    $stmt->bind_param("i", $id); // i=int
    return $stmt->execute();
  }

  /**
   * find($id)
   * Trae un puesto por ID. Devuelve array asociativo o null si no existe.
   * Se usa para precargar el formulario de edición.
   */
  public function find(int $id): ?array {
    $stmt = $this->con->prepare("SELECT * FROM puestos WHERE id = ?");
    if (!$stmt) return null;
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc() ?: null;
  }
}