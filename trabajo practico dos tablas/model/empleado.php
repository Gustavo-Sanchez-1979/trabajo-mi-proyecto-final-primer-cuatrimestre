<?php
require_once __DIR__ . '/db.php'; // ⬅Carga la clase Db para obtener la conexión MySQLi

class Empleado {
  // Conexión MySQLi que usarán todos los métodos del modelo
  private mysqli $con;

  public function __construct() {
    // Instancia la conexión y la guarda en $this->con
    $db = new Db();
    $this->con = $db->con;
  }

  /**
   * all()
   * Devuelve el listado de empleados con datos de su puesto (si existe).
   * LEFT JOIN ⇒ muestra TODOS los empleados aunque puesto_id no tenga match.
   */
  public function all(): array {
    $sql = "SELECT 
              e.id,
              e.nombre,
              e.apellido,
              e.dni,
              e.empresa,
              e.domicilio,
              e.ciudad,
              e.provincia,
              e.pais,
              e.telefono,
              e.email,
              e.creado_en,
              COALESCE(p.nombre, '(Sin puesto)') AS puesto  -- si no hay match, muestra '(Sin puesto)'
            FROM empleados e
            LEFT JOIN puestos p ON p.id = e.puesto_id
            ORDER BY e.id ASC";
    $res = $this->con->query($sql);                       // ejecuta la consulta
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : [];     // devuelve array de arrays (o vacío si falla)
  }

  /**
   * create($d)
   * Inserta un empleado nuevo usando sentencia preparada (bind_param) para evitar SQL injection.
   * Espera en $d las claves: nombre, apellido, dni, empresa, domicilio, ciudad, provincia, pais, telefono, email, puesto_id
   */
  public function create(array $d): bool {
    $sql = "INSERT INTO empleados (nombre, apellido, dni, empresa, domicilio, ciudad, provincia, pais, telefono, email, puesto_id)
            VALUES (?,?,?,?,?,?,?,?,?,?,?)";
    $stmt = $this->con->prepare($sql);  // prepara la sentencia
    if (!$stmt) return false;           // si falla la preparación, corta

    // Normaliza/lee los campos del array de entrada
    $nombre    = $d['nombre']    ?? '';
    $apellido  = $d['apellido']  ?? '';
    $dni       = $d['dni']       ?? '';
    $empresa   = $d['empresa']   ?? '';
    $domicilio = $d['domicilio'] ?? null;      // opcionales como NULL
    $ciudad    = $d['ciudad']    ?? null;
    $provincia = $d['provincia'] ?? null;
    $pais      = $d['pais']      ?? 'Argentina';
    $telefono  = $d['telefono']  ?? null;
    $email     = $d['email']     ?? null;
    $puesto_id = (int)($d['puesto_id'] ?? 0);

    // bind_param: define tipos y valores en el mismo orden que la consulta
    // s=string, i=int. Acá: 10 strings + 1 int  → "ssssssssssi"
    $stmt->bind_param(
      "ssssssssssi",
      $nombre, $apellido, $dni, $empresa, $domicilio, $ciudad, $provincia, $pais, $telefono, $email, $puesto_id
    );
    return $stmt->execute(); // true si insertó OK, false si hubo error (UNIQUE, FK, etc.)
  }

  /**
   * find($id)
   * Trae un empleado por su ID junto con el nombre de su puesto (si existe).
   * Devuelve array asociativo o null si no lo encuentra.
   */
  public function find(int $id): ?array {
    $stmt = $this->con->prepare(
      "SELECT e.*,
              COALESCE(p.nombre, '(Sin puesto)') AS puesto
       FROM empleados e
       LEFT JOIN puestos p ON p.id = e.puesto_id
       WHERE e.id = ?"
    );
    $stmt->bind_param("i", $id);      // i = integer
    $stmt->execute();
    $res = $stmt->get_result();
    return $res->fetch_assoc() ?: null; // una fila o null si no hay
  }

  /**
   * update($id, $d)
   * Actualiza los campos del empleado por ID.
   * Usa sentencia preparada; devuelve true/false según éxito del UPDATE.
   */
  public function update(int $id, array $d): bool {
    $sql = "UPDATE empleados SET
              nombre=?, apellido=?, dni=?, empresa=?,
              domicilio=?, ciudad=?, provincia=?, pais=?,
              telefono=?, email=?, puesto_id=?
            WHERE id=?";
    $stmt = $this->con->prepare($sql);
    if (!$stmt) return false;

    // Normaliza/lee los campos
    $nombre    = $d['nombre']    ?? '';
    $apellido  = $d['apellido']  ?? '';
    $dni       = $d['dni']       ?? '';
    $empresa   = $d['empresa']   ?? '';
    $domicilio = $d['domicilio'] ?? null;
    $ciudad    = $d['ciudad']    ?? null;
    $provincia = $d['provincia'] ?? null;
    $pais      = $d['pais']      ?? 'Argentina';
    $telefono  = $d['telefono']  ?? null;
    $email     = $d['email']     ?? null;
    $puesto_id = (int)($d['puesto_id'] ?? 0);

    // Tipos: 10 strings + 2 ints (puesto_id, id) → "ssssssssssii"
    $stmt->bind_param(
      "ssssssssssii",
      $nombre, $apellido, $dni, $empresa,
      $domicilio, $ciudad, $provincia, $pais,
      $telefono, $email, $puesto_id, $id
    );
    return $stmt->execute(); // true si se actualizó; false si error (p.ej. UNIQUE(dni))
  }

  /**
   * delete($id)
   * Borra un empleado por ID. Devuelve true/false según éxito.
   * ⚠️ No pide confirmación: asegurate de usar confirm() en la vista y/o CSRF en el controlador.
   */
  public function delete(int $id): bool {
    $stmt = $this->con->prepare("DELETE FROM empleados WHERE id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute();
  }
}