-- Crear BD 
CREATE DATABASE IF NOT EXISTS empresa_1
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE empresa_1;

-- Tabla de puestos (lado 1)
CREATE TABLE IF NOT EXISTS puestos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  UNIQUE KEY uq_puesto_nombre (nombre)
) ENGINE=InnoDB;

-- Tabla de empleados (lado N)
CREATE TABLE IF NOT EXISTS empleados (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nombre   VARCHAR(80)  NOT NULL,
  apellido VARCHAR(80)  NOT NULL,
  dni      VARCHAR(12)  NOT NULL,
  empresa  VARCHAR(120) NOT NULL,
  domicilio VARCHAR(120) DEFAULT NULL,
  ciudad   VARCHAR(80)  DEFAULT NULL,
  provincia VARCHAR(80) DEFAULT NULL,
  pais     VARCHAR(60)  DEFAULT 'Argentina',
  telefono VARCHAR(30)  DEFAULT NULL,
  email    VARCHAR(120) DEFAULT NULL,
  creado_en DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  puesto_id INT UNSIGNED NOT NULL,

  CONSTRAINT uq_empleado_dni UNIQUE (dni),
  CONSTRAINT fk_empleado_puesto
    FOREIGN KEY (puesto_id) REFERENCES puestos(id)
    ON UPDATE CASCADE
    ON DELETE RESTRICT
) ENGINE=InnoDB;

-- Datos de ejemplo
INSERT INTO puestos (nombre) VALUES ('Moldeador'), ('Desmoldador'), ('Empaquetador')
  ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

INSERT INTO empleados (nombre, apellido, dni, empresa, domicilio, ciudad, provincia, pais, telefono, email, puesto_id)
VALUES
('María','Gómez','30111222','La Quesera','San Martín 123','Salliqueló','Buenos Aires','Argentina','2392-536498','maria.gomez@empresa.com', 1),
('Juan','Pérez','28999444','La Quesera','Belgrano 456','Salliqueló','Buenos Aires','Argentina','2392-534000','juan.perez@empresa.com', 2);

ALTER TABLE puestos
  ADD UNIQUE KEY uq_puesto_nombre (nombre);


CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  contrasenia VARCHAR(255) NOT NULL
);  