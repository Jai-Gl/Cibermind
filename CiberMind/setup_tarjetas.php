<?php
include "config.php";

$conn->query("CREATE TABLE IF NOT EXISTS tarjetas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    concepto VARCHAR(255) NOT NULL,
    definicion VARCHAR(255) NOT NULL,
    categoria VARCHAR(50) DEFAULT 'general'
)");

$result = $conn->query("SELECT COUNT(*) as c FROM tarjetas");
if($result->fetch_assoc()["c"] == 0) {
    $conn->query("INSERT INTO tarjetas (concepto, definicion, categoria) VALUES
    ('Phishing', 'Técnica de ingeniería social para robar credenciales', 'Ataque'),
    ('Ransomware', 'Malware que cifra archivos y exige rescate', 'Malware'),
    ('Firewall', 'Dispositivo que filtra el tráfico de red', 'Defensa'),
    ('VPN', 'Red privada virtual que cifra la conexión', 'Seguridad'),
    ('Autenticación 2FA', 'Verificación en dos pasos', 'Autenticación'),
    ('Cifrado AES', 'Estándar de cifrado simétrico de 256 bits', 'Cifrado'),
    ('Zero-Day', 'Vulnerabilidad desconocida sin parche disponible', 'Vulnerabilidad'),
    ('DDoS', 'Ataque distribuido de denegación de servicio', 'Ataque'),
    ('SQL Injection', 'Inyección de código SQL malicioso', 'Ataque'),
    ('XSS', 'Cross-Site Scripting: scripts maliciosos en web', 'Ataque'),
    ('Antivirus', 'Software que detecta y elimina malware', 'Defensa'),
    ('IDS/IPS', 'Sistemas de detección/prevención de intrusiones', 'Defensa'),
    ('SIEM', 'Sistema de gestión de eventos de seguridad', 'Monitorización'),
    ('Backup', 'Copia de seguridad de datos críticos', 'Recuperación'),
    ('Penetration Testing', 'Evaluación de seguridad simulando ataques', 'Auditoría'),
    ('Hash SHA-256', 'Función hash criptográfica unidireccional', 'Cifrado')
    ");
}

echo "Tabla tarjetas creada";
?>