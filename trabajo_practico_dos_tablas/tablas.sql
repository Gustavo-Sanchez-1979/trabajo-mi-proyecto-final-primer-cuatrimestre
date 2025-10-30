-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 30-10-2025 a las 23:26:46
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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `puestos`
--

CREATE TABLE `puestos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `tarea` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `puestos`
--

INSERT INTO `puestos` (`id`, `nombre`, `tarea`) VALUES
(7, 'Preprensa', 'moldeador'),
(8, 'Desmolde', 'Desmoldar'),
(9, 'Tinas queseras', 'Quesero'),
(10, 'Pasteurizador', 'operar los equipos'),
(12, 'Seguridad', 'Portero'),
(13, 'Saladero', 'sacador y echador de hormas'),
(15, 'Envasadora', 'envasar y paletizar'),
(16, 'Autoelevador', 'Conductor'),
(17, 'Sala de Elaboracion', 'Limpieza'),
(18, 'Camara expedición', 'Almacenamiento de quesos y carga'),
(19, 'sala de pintado', 'pintor'),
(20, 'Cremeria', 'Operario'),
(21, 'Dulcero', 'operario');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasenia` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `contrasenia`) VALUES
(1, 'gus', '$2y$10$PKE14oSU4/zzjILFqu8SrOuUgRShZ7t/IoM1svoTvNcKwmJKJ.bOu'),
(2, 'carlos', '$2y$10$uH26NqouVRPL4WE2A30tUu9/h3krKnMikLRyIfpR6fDgSOaEw9sdO'),
(3, 'admin', '$2y$10$SPRgp8NlJQQWUmBoN9NPuumxROuff4xSL5Q4rJU4nuxAyArpklqSe'),
(4, 'mariel', '$2y$10$yME2vYJRNEiH56/CcSrVbem1jMx1OVUh/1Wg.7Bq82KQHZNYp5D62'),
(5, 'juan', '$2y$10$eamMO5SaVJA0F8PByqi5QOCfIKKPLIpq/n/SS3bHViExYpDpaHQkm'),
(6, 'Marta', '$2y$10$PU8Mp0HBl3dNVbpAvmIGIe0mRKdQHDcEJbeU66AvvZ6oNk4QoV8Ce'),
(7, 'Juan Pablo', '$2y$10$xl7gP8NLQfOUc.4Ua4RMCevSQJ4VMSb6HWT6rqEBpnYlAYnC6rQpm');

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
-- Indices de la tabla `puestos`
--
ALTER TABLE `puestos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_puesto_nombre` (`nombre`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `empleados`
--
ALTER TABLE `empleados`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `puestos`
--
ALTER TABLE `puestos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

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
