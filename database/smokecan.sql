-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Temps de generació: 11-06-2026 a les 17:58:33
-- Versió del servidor: 10.4.32-MariaDB
-- Versió de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de dades: `smokecan`
--

-- --------------------------------------------------------

--
-- Estructura de la taula `comentarios`
--

CREATE TABLE `comentarios` (
  `id` int(11) NOT NULL,
  `sitio` varchar(255) NOT NULL,
  `valoracion` int(11) NOT NULL,
  `comentario` text DEFAULT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `usuario_nombre` varchar(100) DEFAULT NULL,
  `estado` enum('pendiente','aprobado') DEFAULT 'pendiente',
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Bolcament de dades per a la taula `comentarios`
--

INSERT INTO `comentarios` (`id`, `sitio`, `valoracion`, `comentario`, `foto`, `usuario_id`, `usuario_nombre`, `estado`, `fecha`) VALUES
(2, 'a', 4, 'asd', NULL, NULL, 'Administrador', 'pendiente', '2026-05-03 18:59:17'),
(6, 'Colegi virolai', 4, '', 'uploads/1781179411_best-recipes-in-The-Legend-of-Zelda-Breath-of-the-Wild-Best-ingredients-locations-more.webp', 4, 'Administrador', 'aprobado', '2026-06-11 12:03:31'),
(7, 'Plaça de la bàscula', 4, 'Un sitio muy tranquilo y agradable', 'uploads/1781183308_asadada.webp', 4, 'Administrador', 'aprobado', '2026-06-11 13:08:28'),
(8, 'Pont de la cadena', 4, 'Esta zona esta muy bien marcada ya que aqui vienen muchos niños', NULL, 4, 'Administrador', 'aprobado', '2026-06-11 13:10:16'),
(9, 'Colegi virolai', 3, 'a', NULL, NULL, 'Invitado', 'pendiente', '2026-06-11 14:36:44');

-- --------------------------------------------------------

--
-- Estructura de la taula `usuario`
--

CREATE TABLE `usuario` (
  `id` int(50) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('user','admin') DEFAULT 'user',
  `fecha_registro` date NOT NULL,
  `avatar` varchar(255) DEFAULT 'img/icono-usuario.png'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Bolcament de dades per a la taula `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `apellidos`, `email`, `password`, `rol`, `fecha_registro`, `avatar`) VALUES
(1, 'Alejandro', 'Zambrano', 'lalejandrozt@gmail.com', '$2y$10$NR3Ru1anysuGG.O26OWN6.zREd1hwhNTfTI4zoSlqqyIU4XXM89ly', 'admin', '2026-04-13', 'img/icono-usuario.png'),
(2, 'Alejandro', 'Zambrano', 'alejandrozt2704@gmail.com', '$2y$10$VQZO5w87piV3SxPlRNA3mOgZm.ZHaCsFa2.vD.q/loacY63FhYOua', 'user', '2026-04-13', 'img/icono-usuario.png'),
(3, 'alejandro', 'zambrano', 'alejandro@gmail.com', '$2y$10$ouSqwHwASciRebBAKm0VluoJHOpGpK7UPeYe66ps9H5cIMkiHHFJq', 'user', '2026-05-03', 'img/icono-usuario.png'),
(4, 'Administrador', 'Smokecan', 'admin@gmail.com', '$2y$10$DI0I.dtsT3mVHC5vP4RHheZlFqHf5I.1Am/tWtHO68kbpR7.O/ioW', 'admin', '0000-00-00', 'img/avatares/avatar-pipa.webp'),
(5, 'alejadro', 'zambrano', 'alejandro1@gmail.com', '$2y$10$lkUKBch0MKcm8XRhqUQ89e5LFtpRRgZx7JGPuWy3bPBHUFlV9tWYS', 'user', '2026-05-24', 'img/icono-usuario.png'),
(6, 'Link', 'Zelda', 'link@gmail.com', '$2y$10$1/2s8Mb7XPjiFwQYze.WsOfykGLUUBldBNhyfdtCcHbagV9obDeQK', 'user', '2026-06-11', 'img/avatares/avatar-pipa.webp'),
(7, 'Asta', 'Clover', 'asta@gmail.com', '$2y$10$FxTTuuQJlF/oudOjVlN8feW0ueYLYrUoQkXbuSvGQEfJDNaq26P/y', 'user', '2026-06-11', 'img/avatares/avatar-nube.webp');

-- --------------------------------------------------------

--
-- Estructura de la taula `votos_zonas`
--

CREATE TABLE `votos_zonas` (
  `id` int(11) NOT NULL,
  `zona_id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `voto` tinyint(4) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Bolcament de dades per a la taula `votos_zonas`
--

INSERT INTO `votos_zonas` (`id`, `zona_id`, `usuario_id`, `voto`) VALUES
(3, 2, 3, 1),
(4, 3, 3, 1),
(5, 4, 5, 1),
(6, 11, 4, 1);

-- --------------------------------------------------------

--
-- Estructura de la taula `zonas_oficiales`
--

CREATE TABLE `zonas_oficiales` (
  `id` int(11) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `radio` int(11) NOT NULL,
  `tipo` varchar(50) DEFAULT NULL,
  `nivel` varchar(50) DEFAULT 'prohibido'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Bolcament de dades per a la taula `zonas_oficiales`
--

INSERT INTO `zonas_oficiales` (`id`, `nombre`, `lat`, `lng`, `radio`, `tipo`, `nivel`) VALUES
(1, 'CAP Molins de Rei', 41.418514, 2.012755, 75, 'hospital', 'prohibido'),
(2, 'Parc de la Mariona', 41.405747, 2.021982, 110, 'parque', 'prohibido'),
(3, 'Escola El Palau', 41.41163, 2.016379, 80, 'colegio', 'prohibido'),
(4, 'Institut Bernat el Ferrer', 41.410682, 2.027206, 100, 'colegio', 'prohibido'),
(5, 'Escola l\'Alzina', 41.414016, 2.02279, 70, 'colegio', 'prohibido'),
(6, 'Escola Castell Ciuró', 41.41103, 2.026207, 75, 'colegio', 'prohibido'),
(7, 'Escola Pont de la Cadena', 41.406292, 2.018489, 85, 'colegio', 'prohibido'),
(8, 'Parc de la Sèquia del Molí', 41.417575, 2.014021, 90, 'parque', 'prohibido'),
(9, 'Escola la Sínia', 41.41836, 2.01169, 80, 'colegio', 'prohibido');

-- --------------------------------------------------------

--
-- Estructura de la taula `zonas_para_fumar`
--

CREATE TABLE `zonas_para_fumar` (
  `id` int(11) NOT NULL,
  `lat` decimal(10,6) NOT NULL,
  `lng` decimal(10,6) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `nombre_sitio` varchar(255) DEFAULT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Bolcament de dades per a la taula `zonas_para_fumar`
--

INSERT INTO `zonas_para_fumar` (`id`, `lat`, `lng`, `usuario_id`, `nombre_sitio`, `fecha`) VALUES
(1, 41.408969, 2.020843, 4, 'Plaça de la bàscula', '2026-06-11 13:07:28'),
(2, 41.415561, 2.011893, 6, 'Rbla de la Granja', '2026-06-11 14:56:48'),
(3, 41.427282, 2.016336, 1, 'Rotonda DSV', '2026-06-11 15:01:44'),
(4, 41.410114, 2.025256, 1, 'Escalera con vistas', '2026-06-11 15:03:31');

-- --------------------------------------------------------

--
-- Estructura de la taula `zonas_usuarios`
--

CREATE TABLE `zonas_usuarios` (
  `id` int(11) NOT NULL,
  `lat` double NOT NULL,
  `lng` double NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `fecha` timestamp NOT NULL DEFAULT current_timestamp(),
  `nombre_sitio` varchar(255) DEFAULT NULL,
  `tipo_zona` varchar(50) DEFAULT 'libre_humo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Bolcament de dades per a la taula `zonas_usuarios`
--

INSERT INTO `zonas_usuarios` (`id`, `lat`, `lng`, `usuario_id`, `fecha`, `nombre_sitio`, `tipo_zona`) VALUES
(2, 41.41453375559853, 2.0196390151977544, 3, '2026-05-03 18:42:20', NULL, 'libre_humo'),
(3, 41.41185020217817, 2.0222139358520512, 3, '2026-05-03 18:45:05', NULL, 'libre_humo'),
(4, 41.417178036772206, 2.0199823379516606, 1, '2026-05-24 21:49:17', NULL, 'libre_humo'),
(5, 41.417821894671945, 2.0239305496215825, 1, '2026-05-24 21:49:18', NULL, 'libre_humo'),
(7, 41.41926992686738, 2.0167100429534917, 5, '2026-05-24 21:55:09', NULL, 'libre_humo'),
(9, 41.412514734927115, 2.028511762619019, 4, '2026-06-11 12:02:24', 'Colegi virolai', 'libre_humo'),
(11, 41.40847892697623, 2.016924619674683, 4, '2026-06-11 13:09:43', 'Pont de la cadena', 'libre_humo');

--
-- Índexs per a les taules bolcades
--

--
-- Índexs per a la taula `comentarios`
--
ALTER TABLE `comentarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_usuario_coment` (`usuario_id`);

--
-- Índexs per a la taula `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- Índexs per a la taula `votos_zonas`
--
ALTER TABLE `votos_zonas`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `zona_id` (`zona_id`,`usuario_id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índexs per a la taula `zonas_oficiales`
--
ALTER TABLE `zonas_oficiales`
  ADD PRIMARY KEY (`id`);

--
-- Índexs per a la taula `zonas_para_fumar`
--
ALTER TABLE `zonas_para_fumar`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Índexs per a la taula `zonas_usuarios`
--
ALTER TABLE `zonas_usuarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- AUTO_INCREMENT per les taules bolcades
--

--
-- AUTO_INCREMENT per la taula `comentarios`
--
ALTER TABLE `comentarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT per la taula `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(50) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT per la taula `votos_zonas`
--
ALTER TABLE `votos_zonas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT per la taula `zonas_oficiales`
--
ALTER TABLE `zonas_oficiales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT per la taula `zonas_para_fumar`
--
ALTER TABLE `zonas_para_fumar`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT per la taula `zonas_usuarios`
--
ALTER TABLE `zonas_usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Restriccions per a les taules bolcades
--

--
-- Restriccions per a la taula `comentarios`
--
ALTER TABLE `comentarios`
  ADD CONSTRAINT `fk_usuario_coment` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`);

--
-- Restriccions per a la taula `votos_zonas`
--
ALTER TABLE `votos_zonas`
  ADD CONSTRAINT `votos_zonas_ibfk_1` FOREIGN KEY (`zona_id`) REFERENCES `zonas_usuarios` (`id`),
  ADD CONSTRAINT `votos_zonas_ibfk_2` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`);

--
-- Restriccions per a la taula `zonas_para_fumar`
--
ALTER TABLE `zonas_para_fumar`
  ADD CONSTRAINT `zonas_para_fumar_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`) ON DELETE CASCADE;

--
-- Restriccions per a la taula `zonas_usuarios`
--
ALTER TABLE `zonas_usuarios`
  ADD CONSTRAINT `zonas_usuarios_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuario` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
