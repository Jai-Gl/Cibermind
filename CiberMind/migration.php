<?php
include "config.php";

$conn->query("ALTER TABLE usuarios ADD COLUMN racha INT DEFAULT 0");
$conn->query("ALTER TABLE usuarios ADD COLUMN dificultad INT DEFAULT 1");
$conn->query("ALTER TABLE usuarios ADD COLUMN ultima_fecha DATETIME DEFAULT NULL");
$conn->query("ALTER TABLE usuarios ADD COLUMN max_racha INT DEFAULT 0");

$conn->query("CREATE TABLE IF NOT EXISTS juegos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    tipo ENUM('parejas','ordenar','arrastrar','completar') DEFAULT 'parejas',
    dificultad_min INT DEFAULT 1,
    xp_base INT DEFAULT 10
)");

$conn->query("INSERT INTO juegos (nombre, descripcion, tipo, dificultad_min, xp_base) VALUES
('Parejas', 'Empareja conceptos con definiciones', 'parejas', 1, 15),
('Ordenar', 'Ordena los elementos del más básico al avanzado', 'ordenar', 2, 20),
('Arrastrar', 'Arrastra cada elemento a su categoría', 'arrastrar', 3, 25),
('Completar', 'Completa el código o frase', 'completar', 1, 10)
ON DUPLICATE KEY UPDATE nombre=nombre");

echo "Migración completa\n";
?>