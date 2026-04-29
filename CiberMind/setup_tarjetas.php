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
    ('HTML', 'Lenguaje de marcado de hipertexto', 'Web'),
    ('CSS', 'Hojas de estilo en cascada', 'Web'),
    ('JavaScript', 'Lenguaje de programación web', 'Web'),
    ('PHP', 'Preprocesador de hipertexto', 'Backend'),
    ('MySQL', 'Sistema de gestión de bases de datos', 'Backend'),
    ('API', 'Interfaz de programación de aplicaciones', 'Concepto'),
    ('JSON', 'Notación de objetos JavaScript', 'Formato'),
    ('Git', 'Sistema de control de versiones', 'Herramienta'),
    ('Docker', 'Plataforma de contenedores', 'Herramienta'),
    ('Linux', 'Sistema operativo de código abierto', 'SO'),
    ('SQL', 'Lenguaje de consulta estructurada', 'Backend'),
    ('HTTP', 'Protocolo de transferencia de hipertexto', 'Protocolo'),
    ('URL', 'Localizador de recursos uniforme', 'Concepto'),
    ('SSH', 'Shell seguro para acceso remoto', 'Protocolo'),
    ('DNS', 'Sistema de nombres de dominio', 'Protocolo'),
    ('AJAX', 'JavaScript asíncrono y XML', 'Tecnología')
    ");
}

echo "Tabla tarjetas creada";
?>