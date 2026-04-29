<?php
include "config.php";

$conn->query("CREATE TABLE IF NOT EXISTS progreso_snake (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    datos_recogidos INT DEFAULT 0,
    puntos INT DEFAULT 0,
    ultima_partida DATETIME
)");

$conn->query("CREATE TABLE IF NOT EXISTS progreso_juegos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    juego VARCHAR(20),
    partidas INT DEFAULT 0,
    mejor_puntuacion INT DEFAULT 0,
    fecha_ultima DATE
)");

echo "✓ Tablas creadas para seguimiento de progreso";
?>