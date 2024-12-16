-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 13-12-2024 a las 00:35:32
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
-- Base de datos: `consultasmedicas`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `admins`
--

INSERT INTO `admins` (`id`, `name`, `email`, `password`, `created_at`) VALUES
(6, 'Majo', 'majivavinces96@gmail.com', '$2y$10$fgcSOQuDtCvhvO9mtloJYeidPvAoyqvPQpR4xiw3IsbqEq56I1wgy', '2024-12-09 15:01:06'),
(7, 'Hector', 'Ramsesanchundia@outlook.es', '$2y$10$rDPXxk5M6HeBlbvlSSnQK.ZpF3tDd1k/kQs2vxf8VyorlkhOb.kmW', '2024-12-09 15:20:18'),
(8, 'Nancy ', 'alcivarn57@gmail.com', '$2y$10$E6Oustjg3wqLwdrxR71tmutPAK0i2FDoMF81gjlfHTx1j7YkTRszy', '2024-12-09 15:22:39'),
(9, 'Melani', 'melalucas15@gmail.com', '$2y$10$CjZ8zoy.wsQKp4sdd/qPTO/GC4NdrbRan/MqSt1VwydkrN5XwtvjS', '2024-12-10 20:56:03');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointments`
--

CREATE TABLE `appointments` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `patient_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `status` enum('pending','confirmed','cancelled','completed') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `appointments`
--

INSERT INTO `appointments` (`id`, `doctor_id`, `patient_id`, `date`, `time`, `status`, `created_at`, `updated_at`) VALUES
(64, 1, 14, '2024-12-16', '08:00:00', 'pending', '2024-12-10 21:30:02', '2024-12-10 21:30:02'),
(65, 1, 14, '2024-12-16', '08:00:00', 'pending', '2024-12-10 21:33:26', '2024-12-10 21:33:26'),
(69, 29, 14, '2024-12-14', '14:24:00', 'confirmed', '2024-12-12 20:52:36', '2024-12-12 22:54:16'),
(71, 29, 14, '2024-12-14', '16:24:00', 'confirmed', '2024-12-12 20:54:47', '2024-12-12 23:34:26'),
(72, 29, 14, '2024-12-14', '18:24:00', 'completed', '2024-12-12 20:54:56', '2024-12-12 22:25:35'),
(73, 29, 14, '2024-12-14', '14:24:00', 'completed', '2024-12-12 21:28:18', '2024-12-12 22:34:50');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `appointment_history`
--

CREATE TABLE `appointment_history` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `change_details` text NOT NULL,
  `changed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `patient_id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cancellation_policies`
--

CREATE TABLE `cancellation_policies` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `cancellation_notice_hours` int(11) NOT NULL,
  `max_wait_time_minutes` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `cancellation_policies`
--

INSERT INTO `cancellation_policies` (`id`, `doctor_id`, `cancellation_notice_hours`, `max_wait_time_minutes`) VALUES
(1, 1, 24, 15),
(3, 3, 24, 15),
(4, 4, 24, 15),
(6, 6, 24, 15),
(7, 7, 24, 15),
(8, 8, 24, 15),
(9, 9, 24, 15),
(10, 10, 24, 15),
(11, 11, 24, 15),
(12, 12, 24, 15),
(13, 13, 24, 15),
(15, 15, 24, 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doctors`
--

CREATE TABLE `doctors` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `specialty` varchar(100) NOT NULL,
  `credentials` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `doctors`
--

INSERT INTO `doctors` (`id`, `user_id`, `specialty`, `credentials`, `created_at`) VALUES
(1, 1, 'Odontologia', 'Se requiere un aviso mínimo de 24 horas para cancelar citas.', '2024-11-17 02:55:31'),
(3, 3, 'Cardiología', 'Las cancelaciones con menos de 24 horas tendrán un cargo.', '2024-11-17 02:55:31'),
(4, 4, 'Cardiología', 'Se permite reprogramar citas hasta 48 horas antes.', '2024-11-17 02:55:31'),
(6, 6, 'Pediatría', 'Las cancelaciones deben notificarse con 24 horas de antelación.', '2024-11-17 02:55:31'),
(7, 7, 'Pediatría', 'No hay penalización si se avisa con 48 horas de antelación.', '2024-11-17 02:55:31'),
(8, 8, 'Pediatría', 'El tiempo máximo de espera es de 20 minutos.', '2024-11-17 02:55:31'),
(9, 9, 'Pediatría', 'Reprogramaciones solo se aceptan con justificación.', '2024-11-17 02:55:31'),
(10, 10, 'Pediatría', 'Políticas de cancelación flexibles.', '2024-11-17 02:55:31'),
(11, 11, 'Dermatología', 'Se requiere un aviso mínimo de 24 horas para cancelaciones.', '2024-11-17 02:55:31'),
(12, 12, 'Dermatología', 'Cancelaciones con menos de 48 horas tendrán penalización.', '2024-11-17 02:55:31'),
(13, 13, 'Dermatología', 'El tiempo máximo de espera permitido es de 15 minutos.', '2024-11-17 02:55:31'),
(15, 15, 'Pediatria', 'Sin penalización por cancelaciones anticipadas.', '2024-11-17 02:55:31'),
(29, 62, 'Oftalmologia', 'cosas de doctor', '2024-12-07 04:33:17'),
(32, 71, 'Odontologia', '1234', '2024-12-10 21:39:25');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `doctor_availability`
--

CREATE TABLE `doctor_availability` (
  `id` int(11) NOT NULL,
  `doctor_id` int(11) NOT NULL,
  `day_of_week` enum('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `doctor_availability`
--

INSERT INTO `doctor_availability` (`id`, `doctor_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(1, 1, 'Monday', '08:00:00', '12:00:00'),
(2, 1, 'Monday', '13:00:00', '17:00:00'),
(4, 3, 'Wednesday', '14:00:00', '18:00:00'),
(5, 4, 'Thursday', '08:00:00', '12:00:00'),
(6, 6, 'Friday', '09:00:00', '12:00:00'),
(7, 7, 'Saturday', '10:00:00', '14:00:00'),
(8, 8, 'Sunday', '08:00:00', '11:00:00'),
(9, 9, 'Monday', '13:00:00', '16:00:00'),
(10, 10, 'Tuesday', '10:00:00', '14:00:00'),
(11, 11, 'Wednesday', '08:00:00', '12:00:00'),
(12, 12, 'Thursday', '14:00:00', '17:00:00'),
(13, 13, 'Friday', '09:00:00', '13:00:00'),
(15, 15, 'Sunday', '10:00:00', '14:00:00'),
(80, 29, 'Saturday', '14:24:00', '21:00:00'),
(81, 32, 'Monday', '12:00:00', '18:00:00');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forum_posts`
--

CREATE TABLE `forum_posts` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_post_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `forum_posts`
--

INSERT INTO `forum_posts` (`id`, `user_id`, `parent_post_id`, `content`, `created_at`) VALUES
(14, 69, NULL, 'hola  gracias por la cita \r\n', '2024-12-10 20:59:53'),
(15, 8, 14, 'Gracias por su \r\natencion ', '2024-12-10 21:08:38'),
(16, 69, NULL, 'hola\r\n', '2024-12-10 21:27:12'),
(17, 8, 16, '.', '2024-12-10 21:28:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `link` varchar(10000) NOT NULL,
  `appointment_id` int(11) DEFAULT NULL,
  `type` enum('estado','receta') NOT NULL DEFAULT 'estado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `message`, `is_read`, `created_at`, `link`, `appointment_id`, `type`) VALUES
(21, 69, 'Usted ha recibido una nueva receta médica de parte de dr.pablo', 1, '2024-12-12 22:25:54', '../php/fpdf/recetas/receta_medica_1734042354.pdf', NULL, 'estado');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `patients`
--

CREATE TABLE `patients` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `dob` date NOT NULL,
  `phone` varchar(20) NOT NULL,
  `canton` varchar(100) NOT NULL,
  `localidad` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `patients`
--

INSERT INTO `patients` (`id`, `user_id`, `dob`, `phone`, `canton`, `localidad`, `created_at`) VALUES
(14, 69, '2002-12-01', '0995478824', 'Manta', 'la proaño', '2024-12-09 15:28:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `private_comments`
--

CREATE TABLE `private_comments` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `sender_id` int(11) NOT NULL,
  `receiver_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `sent_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `private_comments`
--

INSERT INTO `private_comments` (`id`, `appointment_id`, `sender_id`, `receiver_id`, `message`, `sent_at`) VALUES
(97, 69, 69, 62, 'prueba de notificaciones ', '2024-12-12 22:24:12');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `reminders`
--

CREATE TABLE `reminders` (
  `id` int(11) NOT NULL,
  `appointment_id` int(11) NOT NULL,
  `reminder_time` datetime NOT NULL,
  `is_sent` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `timezones`
--

CREATE TABLE `timezones` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `timezone` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `timezones`
--

INSERT INTO `timezones` (`id`, `user_id`, `timezone`) VALUES
(1, 1, 'UTC'),
(2, 2, 'UTC'),
(3, 3, 'UTC'),
(4, 4, 'UTC'),
(6, 6, 'UTC'),
(7, 7, 'UTC'),
(8, 8, 'UTC'),
(9, 9, 'UTC'),
(10, 10, 'UTC'),
(11, 11, 'UTC'),
(12, 12, 'UTC'),
(13, 13, 'UTC'),
(15, 15, 'UTC');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('patient','doctor','admin') NOT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `language` varchar(50) DEFAULT NULL,
  `timezone` varchar(50) DEFAULT 'UTC',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `phone` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `gender`, `language`, `timezone`, `created_at`, `phone`) VALUES
(1, 'Dr. Juan Pérez', 'juan.perez@hospital.com', '9c87400128d408cdcda0e4b3ff0e66fa', 'doctor', 'male', 'español', 'UTC', '2024-11-17 02:55:31', 0),
(2, 'Dra. María López', 'maria.lopez@hospital.com', '9c87400128d408cdcda0e4b3ff0e66fa', 'doctor', 'female', 'español', 'UTC', '2024-11-17 02:55:31', 0),
(3, 'Dr. Carlos Díaz', 'carlos.diaz@hospital.com', '9c87400128d408cdcda0e4b3ff0e66fa', 'doctor', 'male', 'español', 'UTC', '2024-11-17 02:55:31', 0),
(4, 'Dra. Elena Morales', 'elena.morales@hospital.com', '9c87400128d408cdcda0e4b3ff0e66fa', 'doctor', 'female', 'español', 'UTC', '2024-11-17 02:55:31', 0),
(6, 'Dra. Laura Gómez', 'laura.gomez@hospital.com', '9c87400128d408cdcda0e4b3ff0e66fa', 'doctor', 'female', 'español', 'UTC', '2024-11-17 02:55:31', 0),
(7, 'Dr. Pedro Rodríguez', 'pedro.rodriguez@hospital.com', '9c87400128d408cdcda0e4b3ff0e66fa', 'doctor', 'male', 'español', 'UTC', '2024-11-17 02:55:31', 0),
(8, 'Dra. Ana Sánchez', 'ana.sanchez@hospital.com', '9c87400128d408cdcda0e4b3ff0e66fa', 'doctor', 'female', 'español', 'UTC', '2024-11-17 02:55:31', 0),
(9, 'Dr. Javier Ruiz', 'javier.ruiz@hospital.com', '9c87400128d408cdcda0e4b3ff0e66fa', 'doctor', 'male', 'español', 'UTC', '2024-11-17 02:55:31', 0),
(10, 'Dra. Clara Vega', 'clara.vega@hospital.com', '9c87400128d408cdcda0e4b3ff0e66fa', 'doctor', 'female', 'español', 'UTC', '2024-11-17 02:55:31', 0),
(11, 'Dr. Fernando Herrera', 'fernando.herrera@hospital.com', '9c87400128d408cdcda0e4b3ff0e66fa', 'doctor', 'male', 'español', 'UTC', '2024-11-17 02:55:31', 0),
(12, 'Dra. Patricia Campos', 'patricia.campos@hospital.com', '9c87400128d408cdcda0e4b3ff0e66fa', 'doctor', 'female', 'español', 'UTC', '2024-11-17 02:55:31', 0),
(13, 'Dr. Jorge Torres', 'jorge.torres@hospital.com', '9c87400128d408cdcda0e4b3ff0e66fa', 'doctor', 'male', 'español', 'UTC', '2024-11-17 02:55:31', 0),
(15, 'Dr. Mario Gómez', 'mario.gomez@hospital.com', '9c87400128d408cdcda0e4b3ff0e66fa', 'doctor', 'male', 'español', 'UTC', '2024-11-17 02:55:31', 0),
(62, 'dr.pablo', 'pablo@gmail.com', '$2y$10$pOrLiglxm4YWxTKof1Ez4OImd59S6v4.kYD6TpjcoaU0kNTO7tXC2', 'doctor', 'male', 'español', 'UTC', '2024-12-07 04:33:17', 987654321),
(69, 'Nancy', 'alcivarn57@gmail.com', '$2y$10$JERLDsr4GieIxdNqrNrLdeqQ6M1dmsZQ1m5rpMbnhog..Q7ML/A/u', 'patient', 'female', NULL, 'UTC', '2024-12-09 15:28:28', 995478824),
(70, 'hector', 'prueba@gmail.com', '$2y$10$W0heq/M2dz5uMTHOlHGAYehYyLe/eWEfx4IYAEye7g0xp2BlNpjeK', 'patient', 'male', NULL, 'UTC', '2024-12-10 21:23:50', 993407845),
(71, 'mariaa', 'maria3@gmail.com', '$2y$10$aGmOdiNFZyFALk5GZE6YIu4MdOCqzETnKt8tRjiYvivUFN96YaJaW', 'doctor', 'female', 'español', 'UTC', '2024-12-10 21:39:25', 987656543);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`),
  ADD KEY `patient_id` (`patient_id`);

--
-- Indices de la tabla `appointment_history`
--
ALTER TABLE `appointment_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indices de la tabla `cancellation_policies`
--
ALTER TABLE `cancellation_policies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indices de la tabla `doctors`
--
ALTER TABLE `doctors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD PRIMARY KEY (`id`),
  ADD KEY `doctor_id` (`doctor_id`);

--
-- Indices de la tabla `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `parent_post_id` (`parent_post_id`);

--
-- Indices de la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `patients`
--
ALTER TABLE `patients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `private_comments`
--
ALTER TABLE `private_comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`),
  ADD KEY `sender_id` (`sender_id`),
  ADD KEY `receiver_id` (`receiver_id`);

--
-- Indices de la tabla `reminders`
--
ALTER TABLE `reminders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `appointment_id` (`appointment_id`);

--
-- Indices de la tabla `timezones`
--
ALTER TABLE `timezones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indices de la tabla `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT de la tabla `appointment_history`
--
ALTER TABLE `appointment_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=99;

--
-- AUTO_INCREMENT de la tabla `cancellation_policies`
--
ALTER TABLE `cancellation_policies`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `doctors`
--
ALTER TABLE `doctors`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `doctor_availability`
--
ALTER TABLE `doctor_availability`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT de la tabla `forum_posts`
--
ALTER TABLE `forum_posts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT de la tabla `patients`
--
ALTER TABLE `patients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT de la tabla `private_comments`
--
ALTER TABLE `private_comments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT de la tabla `reminders`
--
ALTER TABLE `reminders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `timezones`
--
ALTER TABLE `timezones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_ibfk_2` FOREIGN KEY (`patient_id`) REFERENCES `patients` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `appointment_history`
--
ALTER TABLE `appointment_history`
  ADD CONSTRAINT `appointment_history_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `cancellation_policies`
--
ALTER TABLE `cancellation_policies`
  ADD CONSTRAINT `cancellation_policies_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `doctors`
--
ALTER TABLE `doctors`
  ADD CONSTRAINT `doctors_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `doctor_availability`
--
ALTER TABLE `doctor_availability`
  ADD CONSTRAINT `doctor_availability_ibfk_1` FOREIGN KEY (`doctor_id`) REFERENCES `doctors` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `forum_posts`
--
ALTER TABLE `forum_posts`
  ADD CONSTRAINT `forum_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `forum_posts_ibfk_2` FOREIGN KEY (`parent_post_id`) REFERENCES `forum_posts` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `patients`
--
ALTER TABLE `patients`
  ADD CONSTRAINT `patients_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `private_comments`
--
ALTER TABLE `private_comments`
  ADD CONSTRAINT `private_comments_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `private_comments_ibfk_2` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `private_comments_ibfk_3` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `reminders`
--
ALTER TABLE `reminders`
  ADD CONSTRAINT `reminders_ibfk_1` FOREIGN KEY (`appointment_id`) REFERENCES `appointments` (`id`) ON DELETE CASCADE;

--
-- Filtros para la tabla `timezones`
--
ALTER TABLE `timezones`
  ADD CONSTRAINT `timezones_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
