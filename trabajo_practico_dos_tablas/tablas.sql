-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-10-2025 a las 22:14:33
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `empresa_1`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empleados`
--

CREATE TABLE `empleados` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(80) NOT NULL,
  `apellido` varchar(80) NOT NULL,
  `dni` varchar(12) NOT NULL,
  `empresa` varchar(120) NOT NULL,
  `domicilio` varchar(120) DEFAULT NULL,
  `ciudad` varchar(80) DEFAULT NULL,
  `provincia` varchar(80) DEFAULT NULL,
  `pais` varchar(60) DEFAULT 'Argentina',
  `telefono` varchar(30) DEFAULT NULL,
  `email` varchar(120) DEFAULT NULL,
  `creado_en` datetime NOT NULL DEFAULT current_timestamp(),
  `puesto_id` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `empleados`
--

INSERT INTO `empleados` (`id`, `nombre`, `apellido`, `dni`, `empresa`, `domicilio`, `ciudad`, `provincia`, `pais`, `telefono`, `email`, `creado_en`, `puesto_id`) VALUES
(1, 'María', 'Gómez', '30111222', 'La Quesera', 'San Martín 123', 'Salliqueló', 'Buenos Aires', 'Argentina', '2392-536498', 'maria.gomez@empresa.com', '2025-10-10 19:58:42', 12),
(2, 'Juan', 'Pérez', '28999444', 'La Quesera', 'Belgrano 456', 'Salliqueló', 'Buenos Aires', 'Argentina', '2392-534000', 'juan.perez@empresa.com', '2025-10-10 19:58:42', 10),
(3, 'pedro', 'Castro', '57896541', 'Innovate Corp', 'Calle Falsa 456', 'salliquelo', 'cordoba', 'Argentina', '234569871', 'pedrosan@hotmail.com.ar', '2025-10-10 20:44:39', 13),
(4, 'Jorge', 'Lopez', '326554987', 'Innovate Corp', 'Calle francia 225', 'salliquelo', 'Buenos Aires', 'Argentina', '321654987410', 'jorge@hotmail.com.ar', '2025-10-10 21:34:43', 9),
(7, 'Rafael', 'Schel', '326598741', 'La Quesera', 'San Martín 123', 'Salliqueló', 'Santa Fe', 'Argentina', '3216549872', 'Rafael@hotmail.com.ar', '2025-10-10 21:48:05', 15),
(8, 'Fernando', 'Schel', '326549871', 'La Quesera', 'Belgrano 456', 'salliquelo', 'cordoba', 'Argentina', '32165-564987', 'fernando.@empresa.com', '2025-10-11 12:45:01', 16),
(9, 'Pedro', 'Lopez', '3265987410', 'La Quesera', 'San Martín 123', 'salliquelo', 'Buenos Aires', 'Argentina', '32165-56498745', 'pedro@hotmail.com.ar', '2025-10-11 13:06:52', 17),
(11, 'Juan pablo', 'Castro', '32156498', 'La Quesera', 'Calle Falsa 456', 'salliquelo', 'Buenos Aires', 'Argentina', '032659874512', 'juanpablo@hotmail.com.ar', '2025-10-16 19:53:46', 16),
(12, 'Rodrigo', 'Castro', '56897412', 'La Quesera', 'Calle Falsa 456', 'salliquelo', 'Buenos Aires', 'Argentina', '32654125412', 'rodrigo@hotmail.com.ar', '2025-10-19 10:27:10', 18),
(13, 'pablo', 'chester', '123654987', 'La Quesera', 'Calle francia 225', 'salliquelo', 'Buenos Aires', 'Argentina', '32654981235', 'pablo@hotmail.com.ar', '2025-10-23 20:32:19', 8),
(15, 'Marcos', 'apelans', '326549871365', 'La Quesera', 'Belgrano 265', 'salliquelo', 'Buenos Aires', 'Argentina', '32654198745', 'Marcos@hotmail.com.ar', '2025-10-24 20:49:46', 21),
(17, 'Gustavo', 'sanchez', '12365477777', 'La Quesera', 'Calle francia 555', 'salliquelo', 'cordoba', 'Argentina', '45632214444', 'gus@hotmail.com.ar', '2025-10-25 14:39:59', 18);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_empleado_dni` (`dni`),
  ADD KEY `fk_empleado_puesto` (`puesto_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `empleados`
--
ALTER TABLE `empleados`
  ADD CONSTRAINT `fk_empleado_puesto` FOREIGN KEY (`puesto_id`) REFERENCES `puestos` (`id`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
