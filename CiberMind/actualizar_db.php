<?php
include "config.php";
$conn->query("CREATE TABLE IF NOT EXISTS avatares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    icono VARCHAR(100),
    nivel_req INT DEFAULT 1,
    conceptos_req INT DEFAULT 0,
    juegos_req INT DEFAULT 0
)");

$conn->query("CREATE TABLE IF NOT EXISTS progreso_juegos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    juego VARCHAR(20),
    partidas INT DEFAULT 0,
    mejor_puntuacion INT DEFAULT 0,
    fecha_ultima DATE
)");

$conn->query("CREATE TABLE IF NOT EXISTS usuario_avatares (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    avatar_id INT,
    desbloqueado_at DATETIME,
    UNIQUE KEY unique_user_avatar (usuario_id, avatar_id)
)");

$conn->query("CREATE TABLE IF NOT EXISTS conceptos_aprendidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    concepto VARCHAR(100),
    fecha DATE,
    UNIQUE KEY unique_user_concept (usuario_id, concepto)
)");

$conn->query("CREATE TABLE IF NOT EXISTS evaluaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria VARCHAR(50),
    pregunta TEXT,
    opcion_a VARCHAR(255),
    opcion_b VARCHAR(255),
    opcion_c VARCHAR(255),
    opcion_d VARCHAR(255),
    respuesta_correcta CHAR(1),
    explicacion TEXT
)");

$conn->query("CREATE TABLE IF NOT EXISTS evaluaciones_usuario (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    evaluacion_id INT,
    correcta BOOLEAN,
    fecha DATETIME
)");

$check = $conn->query("SELECT COUNT(*) as c FROM avatares")->fetch_assoc();
if($check['c'] == 0) {
    $avatares = [
        ["nombre" => "Kitten", "icono" => "🐱", "nivel" => 1, "conceptos" => 0, "juegos" => 0],
        ["nombre" => "Gatito Curioso", "icono" => "😺", "nivel" => 2, "conceptos" => 3, "juegos" => 5],
        ["nombre" => "Gato Hacker", "icono" => "😼", "nivel" => 3, "conceptos" => 8, "juegos" => 15],
        ["nombre" => "Gato Ninja", "icono" => "🐈", "nivel" => 5, "conceptos" => 15, "juegos" => 30],
        ["nombre" => "Gato Maestro", "icono" => "🦊", "nivel" => 7, "conceptos" => 25, "juegos" => 50],
        ["nombre" => "Cyber Cat", "icono" => "🤖", "nivel" => 10, "conceptos" => 40, "juegos" => 80],
        ["nombre" => "Dragon Cat", "icono" => "🐉", "nivel" => 15, "conceptos" => 60, "juegos" => 120],
        ["nombre" => "宇宙猫", "icono" => "🌌", "nivel" => 20, "conceptos" => 100, "juegos" => 200]
    ];
    
    foreach($avatares as $a) {
        $conn->query("INSERT INTO avatares (nombre, icono, nivel_req, conceptos_req, juegos_req) 
            VALUES ('{$a['nombre']}', '{$a['icono']}', {$a['nivel']}, {$a['conceptos']}, {$a['juegos']})");
    }
}

$check = $conn->query("SELECT COUNT(*) as c FROM evaluaciones")->fetch_assoc();
if($check['c'] == 0) {
    $evaluaciones = [
        ["categoria" => "Firewalls", "pregunta" => "¿Qué es un firewall?", "a" => "Un programa para crear contraseñas", "b" => "Sistema que filtra tráfico de red", "c" => "Un tipo de virus", "d" => "Una base de datos", "correcta" => "B", "explicacion" => "Los firewalls actúan como barreras que controlan el tráfico de red entrante y saliente."],
        ["categoria" => "Firewalls", "pregunta" => "¿Cuál es la diferencia entre IDS e IPS?", "a" => "IDS es más caro que IPS", "b" => "IDS detecta, IPS detecta y bloquea", "c" => "No hay diferencia", "d" => "IPS es para redes sociales", "correcta" => "B", "explicacion" => "IDS detecta intrusiones, mientras que IPS además las bloquea en tiempo real."],
        ["categoria" => "Contraseñas", "pregunta" => "¿Cuál es la característica de una contraseña fuerte?", "a" => "Solo letras minúsculas", "b" => "Tu nombre de nacimiento", "c" => "Más de 12 caracteres con mezcla", "d" => "Solo números", "correcta" => "C", "explicacion" => "Las contraseñas fuertes deben ser largas y usar mayúsculas, minúsculas, números y símbolos."],
        ["categoria" => "Contraseñas", "pregunta" => "¿Qué es MFA?", "a" => "Un formato de archivo", "b" => "Multi-Factor Authentication", "c" => "Un protocolo de red", "d" => "Un lenguaje de programación", "correcta" => "B", "explicacion" => "MFA requiere múltiples formas de verificación: algo que sabes, tienes y eres."],
        ["categoria" => "Malware", "pregunta" => "¿Qué tipo de malware se disfraza de software legítimo?", "a" => "Virus", "b" => "Gusano", "c" => "Troyano", "d" => "Adware", "correcta" => "C", "explicacion" => "Los troyanos aparentan ser software legítimo pero contienen código malicioso."],
        ["categoria" => "Malware", "pregunta" => "¿Cuál se propaga sin necesidad de un archivo host?", "a" => "Virus", "b" => "Gusano (Worm)", "c" => "Troyano", "d" => "Spyware", "correcta" => "B", "explicacion" => "Los gusanos pueden autoreplicarse sin necesidad de adjunto o archivo host."],
        ["categoria" => "Phishing", "pregunta" => "¿Qué es phishing?", "a" => "Un tipo de firewall", "b" => "Correos engañosos para robar datos", "c" => "Un algoritmo de cifrado", "d" => "Un lenguaje de programación", "correcta" => "B", "explicacion" => "Phishing usa comunicaciones falsas para engañar víctimas y robar información."],
        ["categoria" => "Phishing", "pregunta" => "¿Cuál es la mejor defensa contra phishing?", "a" => "Usar redes Wi-Fi públicas", "b" => "Ignorar todos los correos", "c" => "Educación y desconfiar de solicitudes inesperadas", "d" => "Desactivar el correo", "correcta" => "C", "explicacion" => "La educación sobre ingeniería social es la mejor defensa contra phishing."],
        ["categoria" => "Cifrado", "pregunta" => "¿Qué tipo de cifrado usa par de claves pública/privada?", "a" => "Simétrico", "b" => "Asimétrico", "c" => "Hash", "d" => "Ninguno", "correcta" => "B", "explicacion" => "El cifrado asimétrico usa un par de claves: pública para cifrar, privada para descifrar."],
        ["categoria" => "Cifrado", "pregunta" => "¿Qué algoritmo es estándar para cifrado moderno?", "a" => "MD5", "b" => "SHA-1", "c" => "AES-256", "d" => "Base64", "correcta" => "C", "explicacion" => "AES-256 es el estándar de cifrado avanzado más utilizado actualmente."],
        ["categoria" => "Redes", "pregunta" => "¿Qué puerto usa HTTPS?", "a" => "21", "b" => "22", "c" => "443", "d" => "80", "correcta" => "C", "explicacion" => "HTTPS usa el puerto 443 para comunicaciones web seguras con TLS/SSL."],
        ["categoria" => "Redes", "pregunta" => "¿Qué hace DNS?", "a" => "Envía correos", "b" => "Traduce dominios a IPs", "c" => "Cifra archivos", "d" => "Detecta virus", "correcta" => "B", "explicacion" => "DNS traduce nombres de dominio legibles (google.com) a direcciones IP numéricas."],
        ["categoria" => "Backup", "pregunta" => "¿Qué significa la regla 3-2-1?", "a" => "3GB de RAM, 2 procesadores, 1 disco", "b" => "3 copias, 2 medios, 1 fuera del sitio", "c" => "3 contraseñas, 2 claves, 1 respaldo", "d" => "3 discos, 2 nubes, 1 local", "correcta" => "B", "explicacion" => "3-2-1: Mantén 3 copias de datos, en 2 medios diferentes, con 1 copia fuera del sitio."],
        ["categoria" => "Ataques", "pregunta" => "¿Qué es un exploit?", "a" => "Un antivirus", "b" => "Código que aprovecha vulnerabilidades", "c" => "Un tipo de firewall", "d" => "Un protocolo de red", "correcta" => "B", "explicacion" => "Un exploit es código que aprovecha una vulnerabilidad conocida para acceder a un sistema."],
        ["categoria" => "Ataques", "pregunta" => "¿Qué es spoofing?", "a" => "Eliminar virus", "b" => "Suplantar identidad", "c" => "Crear contraseñas", "d" => "Instalar actualizaciones", "correcta" => "B", "explicacion" => "Spoofing suplanta la identidad de algo o alguien para engañar a las víctimas."],
        ["categoria" => "OSI", "pregunta" => "¿Cuántas capas tiene el modelo OSI?", "a" => "5", "b" => "6", "c" => "7", "d" => "8", "correcta" => "C", "explicacion" => "El modelo OSI tiene 7 capas: Física, Enlace, Red, Transporte, Sesión, Presentación, Aplicación."],
        ["categoria" => "OSI", "pregunta" => "¿En qué capa opera HTTP?", "a" => "Red", "b" => "Transporte", "c" => "Aplicación", "d" => "Enlace", "correcta" => "C", "explicacion" => "HTTP opera en la capa de Aplicación (capa 7) del modelo OSI."],
        ["categoria" => "OWASP", "pregunta" => "¿Qué es OWASP?", "a" => "Un lenguaje de programación", "b" => "Organización de seguridad web", "c" => "Un protocolo de red", "d" => "Un antivirus", "correcta" => "B", "explicacion" => "OWASP es una comunidad que produce herramientas y documentación sobre seguridad de aplicaciones web."],
        ["categoria" => "OWASP", "pregunta" => "¿Cuál es la vulnerabilidad #1 en OWASP Top 10?", "a" => "XSS", "b" => "Inyección", "c" => "CSRF", "d" => "SSRF", "correcta" => "B", "explicacion" => "Inyección (como SQL Injection) es la vulnerabilidad más común según OWASP Top 10."],
        ["categoria" => "Herramientas", "pregunta" => "¿Para qué se usa Nmap?", "a" => "Crear páginas web", "b" => "Escanear puertos y redes", "c" => "Enviar correos", "d" => "Editar imágenes", "correcta" => "B", "explicacion" => "Nmap es el scanner de puertos más popular para descubrir hosts y servicios en redes."],
        ["categoria" => "Herramientas", "pregunta" => "¿Qué hace Wireshark?", "a" => "Análisis de tráfico de red", "b" => "Crear bases de datos", "c" => "Programar webs", "d" => "Compilar código", "correcta" => "A", "explicacion" => "Wireshark captura y analiza paquetes de red en detalle para troubleshooting y seguridad."]
    ];
    
    foreach($evaluaciones as $e) {
        $conn->query("INSERT INTO evaluaciones (categoria, pregunta, opcion_a, opcion_b, opcion_c, opcion_d, respuesta_correcta, explicacion)
            VALUES ('{$e['categoria']}', '{$e['pregunta']}', '{$e['a']}', '{$e['b']}', '{$e['c']}', '{$e['d']}', '{$e['correcta']}', '{$e['explicacion']}')");
    }
}

echo "✓ Base de datos actualizada con éxito";
echo "\n- Avatares de gatos: 8 niveles";
echo "\n- Evaluaciones: 20 preguntas de ciberseguridad";
echo "\n- Seguimiento de conceptos aprendidos";
?>