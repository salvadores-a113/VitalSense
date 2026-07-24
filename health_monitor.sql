-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-03-2025 a las 07:29:24
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `health_monitor`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `anomalies`
--

CREATE TABLE `anomalies` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `description` text NOT NULL,
  `severity` enum('low','medium','high') NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `anomalies`
--

INSERT INTO `anomalies` (`id`, `patient_id`, `description`, `severity`, `timestamp`) VALUES
(3, 1, 'Posible evento de desmayo', 'low', '2025-03-29 06:04:40'),
(4, 1, 'Movimiento corporal inusual detectado', 'medium', '2025-03-29 06:04:43'),
(5, 1, 'Disminución inusual en el ritmo cardíaco', 'low', '2025-03-29 06:05:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `heart_rate`
--

CREATE TABLE `heart_rate` (
  `id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `rate` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `heart_rate`
--

INSERT INTO `heart_rate` (`id`, `patient_id`, `rate`, `timestamp`) VALUES
(2, 1, 90, '2025-03-29 06:04:30'),
(3, 1, 60, '2025-03-29 06:04:33'),
(4, 1, 68, '2025-03-29 06:04:36'),
(5, 1, 73, '2025-03-29 06:04:38'),
(6, 1, 88, '2025-03-29 06:05:40');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `age` int(11) NOT NULL,
  `weight` decimal(5,2) NOT NULL,
  `height` decimal(5,2) NOT NULL,
  `disease` varchar(255) DEFAULT NULL,
  `disorder` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `patients`
--

INSERT INTO `patients` (`id`, `username`, `password`, `email`, `full_name`, `age`, `weight`, `height`, `disease`, `disorder`, `created_at`) VALUES
(1, 'Juan', '$2y$10$Qb10uO8fCUS6mcASQWRt7Oo.nifjaY/9GpeZSs1ZaaBJoxShFXVCa', 'martinezjuande72@gmail.com', 'Juan Martínez', 20, 80.00, 1.73, 'asd', '', '2025-03-29 06:03:55'),
(2, 'Salvador', '$2y$10$TYUHzR26TuCt5ZrmGTtrR.VKQ0BT0G8oJYoYkudePdpf4sLafOaqC', 'chava@gmail.com', 'Salvador Estrada', 20, 70.00, 1.72, '', '', '2025-03-29 06:25:28');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `anomalies`
--
ALTER TABLE `anomalies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indices de la tabla `heart_rate`
--
ALTER TABLE `heart_rate`
  ADD PRIMARY KEY (`id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indices de la tabla `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `anomalies`
--
ALTER TABLE `anomalies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `heart_rate`
--
ALTER TABLE `heart_rate`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `anomalies`
--
ALTER TABLE `anomalies`
  ADD CONSTRAINT `anomalies_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);

--
-- Filtros para la tabla `heart_rate`
--
ALTER TABLE `heart_rate`
  ADD CONSTRAINT `heart_rate_ibfk_1` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
