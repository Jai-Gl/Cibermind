-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 26-04-2026 a las 00:30:58
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
-- Base de datos: `cibermind`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `amigos`
--

CREATE TABLE `amigos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `amigo_id` int(11) NOT NULL,
  `estado` enum('pendiente','aceptado') DEFAULT 'pendiente',
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `avatares`
--

CREATE TABLE `avatares` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) DEFAULT NULL,
  `icono` varchar(100) DEFAULT NULL,
  `nivel_req` int(11) DEFAULT 1,
  `conceptos_req` int(11) DEFAULT 0,
  `juegos_req` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `avatares`
--

INSERT INTO `avatares` (`id`, `nombre`, `icono`, `nivel_req`, `conceptos_req`, `juegos_req`) VALUES
(1, 'Novato', '🐱', 1, 0, 0),
(2, 'Aprendiz', '😺', 2, 3, 5),
(3, 'Analista', '😼', 3, 8, 15),
(4, 'Defensor', '🐈', 5, 15, 30),
(5, 'Especialista', '🦊', 7, 25, 50),
(6, 'Ciberguerrero', '🤖', 10, 40, 80),
(7, 'Shadow Master', '🐉', 15, 60, 120),
(8, 'Leyenda Cibernética', '🌌', 20, 100, 200);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `conceptos_aprendidos`
--

CREATE TABLE `conceptos_aprendidos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `concepto` varchar(100) DEFAULT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones`
--

CREATE TABLE `evaluaciones` (
  `id` int(11) NOT NULL,
  `categoria` varchar(50) DEFAULT NULL,
  `pregunta` text DEFAULT NULL,
  `opcion_a` varchar(255) DEFAULT NULL,
  `opcion_b` varchar(255) DEFAULT NULL,
  `opcion_c` varchar(255) DEFAULT NULL,
  `opcion_d` varchar(255) DEFAULT NULL,
  `respuesta_correcta` char(1) DEFAULT NULL,
  `explicacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `evaluaciones`
--

INSERT INTO `evaluaciones` (`id`, `categoria`, `pregunta`, `opcion_a`, `opcion_b`, `opcion_c`, `opcion_d`, `respuesta_correcta`, `explicacion`) VALUES
(1, 'Firewalls', '¿Qué es un firewall?', 'Un programa para crear contraseñas', 'Sistema que filtra tráfico de red', 'Un tipo de virus', 'Una base de datos', 'B', 'Los firewalls actúan como barreras que controlan el tráfico de red entrante y saliente.'),
(2, 'Firewalls', '¿Cuál es la diferencia entre IDS e IPS?', 'IDS es más caro que IPS', 'IDS detecta, IPS detecta y bloquea', 'No hay diferencia', 'IPS es para redes sociales', 'B', 'IDS detecta intrusiones, mientras que IPS además las bloquea en tiempo real.'),
(3, 'Contraseñas', '¿Cuál es la característica de una contraseña fuerte?', 'Solo letras minúsculas', 'Tu nombre de nacimiento', 'Más de 12 caracteres con mezcla', 'Solo números', 'C', 'Las contraseñas fuertes deben ser largas y usar mayúsculas, minúsculas, números y símbolos.'),
(4, 'Contraseñas', '¿Qué es MFA?', 'Un formato de archivo', 'Multi-Factor Authentication', 'Un protocolo de red', 'Un lenguaje de programación', 'B', 'MFA requiere múltiples formas de verificación: algo que sabes, tienes y eres.'),
(5, 'Malware', '¿Qué tipo de malware se disfraza de software legítimo?', 'Virus', 'Gusano', 'Troyano', 'Adware', 'C', 'Los troyanos aparentan ser software legítimo pero contienen código malicioso.'),
(6, 'Malware', '¿Cuál se propaga sin necesidad de un archivo host?', 'Virus', 'Gusano (Worm)', 'Troyano', 'Spyware', 'B', 'Los gusanos pueden autoreplicarse sin necesidad de adjunto o archivo host.'),
(7, 'Phishing', '¿Qué es phishing?', 'Un tipo de firewall', 'Correos engañosos para robar datos', 'Un algoritmo de cifrado', 'Un lenguaje de programación', 'B', 'Phishing usa comunicaciones falsas para engañar víctimas y robar información.'),
(8, 'Phishing', '¿Cuál es la mejor defensa contra phishing?', 'Usar redes Wi-Fi públicas', 'Ignorar todos los correos', 'Educación y desconfiar de solicitudes inesperadas', 'Desactivar el correo', 'C', 'La educación sobre ingeniería social es la mejor defensa contra phishing.'),
(9, 'Cifrado', '¿Qué tipo de cifrado usa par de claves pública/privada?', 'Simétrico', 'Asimétrico', 'Hash', 'Ninguno', 'B', 'El cifrado asimétrico usa un par de claves: pública para cifrar, privada para descifrar.'),
(10, 'Cifrado', '¿Qué algoritmo es estándar para cifrado moderno?', 'MD5', 'SHA-1', 'AES-256', 'Base64', 'C', 'AES-256 es el estándar de cifrado avanzado más utilizado actualmente.'),
(11, 'Redes', '¿Qué puerto usa HTTPS?', '21', '22', '443', '80', 'C', 'HTTPS usa el puerto 443 para comunicaciones web seguras con TLS/SSL.'),
(12, 'Redes', '¿Qué hace DNS?', 'Envía correos', 'Traduce dominios a IPs', 'Cifra archivos', 'Detecta virus', 'B', 'DNS traduce nombres de dominio legibles (google.com) a direcciones IP numéricas.'),
(13, 'Backup', '¿Qué significa la regla 3-2-1?', '3GB de RAM, 2 procesadores, 1 disco', '3 copias, 2 medios, 1 fuera del sitio', '3 contraseñas, 2 claves, 1 respaldo', '3 discos, 2 nubes, 1 local', 'B', '3-2-1: Mantén 3 copias de datos, en 2 medios diferentes, con 1 copia fuera del sitio.'),
(14, 'Ataques', '¿Qué es un exploit?', 'Un antivirus', 'Código que aprovecha vulnerabilidades', 'Un tipo de firewall', 'Un protocolo de red', 'B', 'Un exploit es código que aprovecha una vulnerabilidad conocida para acceder a un sistema.'),
(15, 'Ataques', '¿Qué es spoofing?', 'Eliminar virus', 'Suplantar identidad', 'Crear contraseñas', 'Instalar actualizaciones', 'B', 'Spoofing suplanta la identidad de algo o alguien para engañar a las víctimas.'),
(16, 'OSI', '¿Cuántas capas tiene el modelo OSI?', '5', '6', '7', '8', 'C', 'El modelo OSI tiene 7 capas: Física, Enlace, Red, Transporte, Sesión, Presentación, Aplicación.'),
(17, 'OSI', '¿En qué capa opera HTTP?', 'Red', 'Transporte', 'Aplicación', 'Enlace', 'C', 'HTTP opera en la capa de Aplicación (capa 7) del modelo OSI.'),
(18, 'OWASP', '¿Qué es OWASP?', 'Un lenguaje de programación', 'Organización de seguridad web', 'Un protocolo de red', 'Un antivirus', 'B', 'OWASP es una comunidad que produce herramientas y documentación sobre seguridad de aplicaciones web.'),
(19, 'OWASP', '¿Cuál es la vulnerabilidad #1 en OWASP Top 10?', 'XSS', 'Inyección', 'CSRF', 'SSRF', 'B', 'Inyección (como SQL Injection) es la vulnerabilidad más común según OWASP Top 10.'),
(20, 'Herramientas', '¿Para qué se usa Nmap?', 'Crear páginas web', 'Escanear puertos y redes', 'Enviar correos', 'Editar imágenes', 'B', 'Nmap es el scanner de puertos más popular para descubrir hosts y servicios en redes.'),
(21, 'Herramientas', '¿Qué hace Wireshark?', 'Análisis de tráfico de red', 'Crear bases de datos', 'Programar webs', 'Compilar código', 'A', 'Wireshark captura y analiza paquetes de red en detalle para troubleshooting y seguridad.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evaluaciones_usuario`
--

CREATE TABLE `evaluaciones_usuario` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `evaluacion_id` int(11) DEFAULT NULL,
  `correcta` tinyint(1) DEFAULT NULL,
  `fecha` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--

CREATE TABLE `eventos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('xp','vidas','especial') DEFAULT 'xp',
  `bonus` int(11) DEFAULT 10,
  `inicio` date DEFAULT NULL,
  `fin` date DEFAULT NULL,
  `activo` tinyint(4) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `eventos`
--

INSERT INTO `eventos` (`id`, `nombre`, `descripcion`, `tipo`, `bonus`, `inicio`, `fin`, `activo`) VALUES
(1, 'Hackathon Weekend', '¡Gana doble XP este fin de semana!', 'xp', 2, '2026-04-24', '2026-04-26', 1),
(2, 'Cyber Defense', 'Completa partidas para obtener escudo', 'especial', 1, '2026-04-24', '2026-05-01', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `juegos`
--

CREATE TABLE `juegos` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `tipo` enum('parejas','ordenar','arrastrar','completar') DEFAULT 'parejas',
  `dificultad_min` int(11) DEFAULT 1,
  `xp_base` int(11) DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `juegos`
--

INSERT INTO `juegos` (`id`, `nombre`, `descripcion`, `tipo`, `dificultad_min`, `xp_base`) VALUES
(1, 'Parejas', 'Empareja conceptos con definiciones', 'parejas', 1, 15),
(2, 'Ordenar', 'Ordena los elementos del más básico al avanzado', 'ordenar', 2, 20),
(3, 'Arrastrar', 'Arrastra cada elemento a su categoría', 'arrastrar', 3, 25),
(4, 'Completar', 'Completa el código o frase', 'completar', 1, 10);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `logros`
--

CREATE TABLE `logros` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `icono` varchar(10) DEFAULT NULL,
  `requisito` varchar(50) DEFAULT NULL,
  `xp_reward` int(11) DEFAULT 10
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `logros`
--

INSERT INTO `logros` (`id`, `nombre`, `descripcion`, `icono`, `requisito`, `xp_reward`) VALUES
(1, 'Primera Victoria', 'Gana tu primera partida', '🎯', 'primera_victoria', 10),
(2, 'Racha de 5', 'Consigue 5 respuestas correctas seguidas', '🔥', 'racha_5', 15),
(3, 'Racha de 10', 'Consigue 10 respuestas correctas seguidas', '⚡', 'racha_10', 25),
(4, 'Racha de 25', 'Consigue 25 respuestas correctas seguidas', '🌟', 'racha_25', 50),
(5, 'Nivel 5', 'Alcanza el nivel 5', '📈', 'nivel_5', 20),
(6, 'Nivel 10', 'Alcanza el nivel 10', '🚀', 'nivel_10', 40),
(7, 'Nivel 25', 'Alcanza el nivel 25', '👑', 'nivel_25', 100),
(8, 'Maestro Parejas', 'Gana 10 partidas de Parejas', '🃏', 'parejas_10', 30),
(9, 'Ordenador', 'Gana 10 partidas de Ordenar', '📊', 'ordenar_10', 30),
(10, 'Clasificador', 'Gana 10 partidas de Clasificar', '🏷️', 'arrastrar_10', 30),
(11, 'Centenario', 'Alcanza 100 puntos de score', '💯', 'score_100', 25),
(12, 'Millennial', 'Alcanza 1000 puntos de score', '🏆', 'score_1000', 50),
(13, 'Noche de Trivia', 'Juega 20 partidas', '🌙', 'partidas_20', 25),
(14, 'Adicto', 'Juega 50 partidas', '💀', 'partidas_50', 50),
(15, 'Vidas Extra', 'Sobrevive 5 veces', '❤️', 'supervivencia_5', 20),
(16, 'Teclado Veloz', 'Responde en menos de 3 segundos', '⚡', 'rapido_3', 15),
(17, 'Primer Login', 'Inicia sesión por primera vez', '👤', 'primer_login', 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `misiones_diarias`
--

CREATE TABLE `misiones_diarias` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `objetivo` int(11) NOT NULL,
  `progreso` int(11) DEFAULT 0,
  `completado` tinyint(4) DEFAULT 0,
  `fecha` date DEFAULT NULL,
  `recompensa` int(11) DEFAULT 15
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `misiones_diarias`
--

INSERT INTO `misiones_diarias` (`id`, `usuario_id`, `tipo`, `objetivo`, `progreso`, `completado`, `fecha`, `recompensa`) VALUES
(9, 15, 'trivia', 5, 0, 0, '2026-04-26', 15);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `preguntas`
--

CREATE TABLE `preguntas` (
  `id` int(11) NOT NULL,
  `pregunta` text DEFAULT NULL,
  `opcion1` varchar(255) DEFAULT NULL,
  `opcion2` varchar(255) DEFAULT NULL,
  `opcion3` varchar(255) DEFAULT NULL,
  `correcta` int(11) DEFAULT NULL,
  `explicacion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `preguntas`
--

INSERT INTO `preguntas` (`id`, `pregunta`, `opcion1`, `opcion2`, `opcion3`, `correcta`, `explicacion`) VALUES
(1, '¿Cuál es una contraseña segura?', '123456', 'password', 'Gx_49mL*eQ!', 3, 'Debe tener símbolos, números y letras.'),
(2, '¿Qué es phishing?', 'Antivirus', 'Robo de datos con engaño', 'Juego online', 2, 'Intentan engañarte para robar información.'),
(3, '¿Debes confiar en WiFi pública?', 'Sí siempre', 'Solo redes abiertas', 'No, puede ser insegura', 3, 'Pueden robar datos.'),
(4, '¿Para qué sirve el 2FA?', 'Decoración', 'Seguridad extra', 'Eliminar virus', 2, 'Añade una capa extra de protección.'),
(5, '¿Qué es malware?', 'Software malicioso', 'Firewall', 'Navegador', 1, 'Puede robar datos o dañar el sistema.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progreso_juegos`
--

CREATE TABLE `progreso_juegos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `juego` varchar(20) DEFAULT NULL,
  `partidas` int(11) DEFAULT 0,
  `mejor_puntuacion` int(11) DEFAULT 0,
  `fecha_ultima` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `progreso_snake`
--

CREATE TABLE `progreso_snake` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `datos_recogidos` int(11) DEFAULT 0,
  `puntos` int(11) DEFAULT 0,
  `ultima_partida` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tarjetas`
--

CREATE TABLE `tarjetas` (
  `id` int(11) NOT NULL,
  `concepto` varchar(255) NOT NULL,
  `definicion` varchar(255) NOT NULL,
  `categoria` varchar(50) DEFAULT 'general'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `tarjetas`
--

INSERT INTO `tarjetas` (`id`, `concepto`, `definicion`, `categoria`) VALUES
(1, 'HTML', 'Lenguaje de marcado de hipertexto', 'Web'),
(2, 'CSS', 'Hojas de estilo en cascada', 'Web'),
(3, 'JavaScript', 'Lenguaje de programación web', 'Web'),
(4, 'PHP', 'Preprocesador de hipertexto', 'Backend'),
(5, 'MySQL', 'Sistema de gestión de bases de datos', 'Backend'),
(6, 'API', 'Interfaz de programación de aplicaciones', 'Concepto'),
(7, 'JSON', 'Notación de objetos JavaScript', 'Formato'),
(8, 'Git', 'Sistema de control de versiones', 'Herramienta'),
(9, 'Docker', 'Plataforma de contenedores', 'Herramienta'),
(10, 'Linux', 'Sistema operativo de código abierto', 'SO'),
(11, 'SQL', 'Lenguaje de consulta estructurada', 'Backend'),
(12, 'HTTP', 'Protocolo de transferencia de hipertexto', 'Protocolo'),
(13, 'URL', 'Localizador de recursos uniforme', 'Concepto'),
(14, 'SSH', 'Shell seguro para acceso remoto', 'Protocolo'),
(15, 'DNS', 'Sistema de nombres de dominio', 'Protocolo'),
(16, 'AJAX', 'JavaScript asíncrono y XML', 'Tecnología');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `score` int(11) DEFAULT 0,
  `nivel` int(11) DEFAULT 1,
  `xp` int(11) DEFAULT 0,
  `vidas` int(11) DEFAULT 3,
  `racha` int(11) DEFAULT 0,
  `dificultad` int(11) DEFAULT 1,
  `ultima_fecha` datetime DEFAULT NULL,
  `max_racha` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `username`, `password`, `score`, `nivel`, `xp`, `vidas`, `racha`, `dificultad`, `ultima_fecha`, `max_racha`) VALUES
(6, 'sebastian', '1234\r\n', 3, 1, 20, 1, 0, 1, NULL, 0),
(10, 'millos', '$2y$10$dhvx5gwgkaHRrmiQwaq8jOmxRge1ETBiZtuCD0ptUhQ0GIWo.OAn.', 0, 1, 0, 3, 0, 1, NULL, 0),
(12, 'JUAN', '$2y$10$k/IwwRNrxvpr2i7S4vIRs.Gs3I5w7/IY149NHr6HAgBQmMA7ZNHWi', 0, 1, 0, 3, 0, 1, NULL, 0),
(15, 'jaider', '$2y$10$yW2/wS.JqfYUwxTIVKZDu.yeuh45UaXlsEmVU5rot12FGwPQk5mE6', 415, 2, 15, 1, 0, 4, '2026-04-25 14:57:34', 7),
(18, 'jaime', '$2y$10$ynfdY2DjM6Lg9q3vO4yy.ektZfQTqo8k2BWR5pV3sT5z.RbgxFGHK', 0, 1, 0, 3, 0, 1, NULL, 0),
(19, 'la turbe', '$2y$10$6ZQKIFW7uMrBxftrBXBbNehE1OqSPtIMckFIQ5xIiZdfreB3zD3pW', 0, 1, 0, 3, 0, 1, NULL, 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios_logros`
--

CREATE TABLE `usuarios_logros` (
  `usuario_id` int(11) NOT NULL,
  `logro_id` int(11) NOT NULL,
  `unlocked_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuarios_logros`
--

INSERT INTO `usuarios_logros` (`usuario_id`, `logro_id`, `unlocked_at`) VALUES
(15, 1, '2026-04-24 15:12:41'),
(15, 2, '2026-04-24 15:12:41'),
(15, 11, '2026-04-24 15:12:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_avatares`
--

CREATE TABLE `usuario_avatares` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `avatar_id` int(11) DEFAULT NULL,
  `desbloqueado_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario_avatares`
--

INSERT INTO `usuario_avatares` (`id`, `usuario_id`, `avatar_id`, `desbloqueado_at`) VALUES
(1, 15, 1, '2026-04-25 14:53:43');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `amigos`
--
ALTER TABLE `amigos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_amigo` (`usuario_id`,`amigo_id`);

--
-- Indices de la tabla `avatares`
--
ALTER TABLE `avatares`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `conceptos_aprendidos`
--
ALTER TABLE `conceptos_aprendidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_concept` (`usuario_id`,`concepto`);

--
-- Indices de la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `evaluaciones_usuario`
--
ALTER TABLE `evaluaciones_usuario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `eventos`
--
ALTER TABLE `eventos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `juegos`
--
ALTER TABLE `juegos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `logros`
--
ALTER TABLE `logros`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_logro` (`requisito`);

--
-- Indices de la tabla `misiones_diarias`
--
ALTER TABLE `misiones_diarias`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_mision` (`usuario_id`,`tipo`,`fecha`);

--
-- Indices de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `progreso_juegos`
--
ALTER TABLE `progreso_juegos`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `progreso_snake`
--
ALTER TABLE `progreso_snake`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tarjetas`
--
ALTER TABLE `tarjetas`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indices de la tabla `usuarios_logros`
--
ALTER TABLE `usuarios_logros`
  ADD PRIMARY KEY (`usuario_id`,`logro_id`);

--
-- Indices de la tabla `usuario_avatares`
--
ALTER TABLE `usuario_avatares`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_user_avatar` (`usuario_id`,`avatar_id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `amigos`
--
ALTER TABLE `amigos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `avatares`
--
ALTER TABLE `avatares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `conceptos_aprendidos`
--
ALTER TABLE `conceptos_aprendidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `evaluaciones`
--
ALTER TABLE `evaluaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT de la tabla `evaluaciones_usuario`
--
ALTER TABLE `evaluaciones_usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `eventos`
--
ALTER TABLE `eventos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `juegos`
--
ALTER TABLE `juegos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `logros`
--
ALTER TABLE `logros`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `misiones_diarias`
--
ALTER TABLE `misiones_diarias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `preguntas`
--
ALTER TABLE `preguntas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `progreso_juegos`
--
ALTER TABLE `progreso_juegos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `progreso_snake`
--
ALTER TABLE `progreso_snake`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tarjetas`
--
ALTER TABLE `tarjetas`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT de la tabla `usuario_avatares`
--
ALTER TABLE `usuario_avatares`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
