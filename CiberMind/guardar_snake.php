<?php
include "config.php";
if(!isset($_SESSION["id"])) exit;

$puntos = (int)$_GET["puntos"];
$datos = (int)($_GET["datos"] ?? 0);

if($puntos > 0) {
    $conn->query("UPDATE usuarios SET score=score+$puntos WHERE id=".$_SESSION["id"]);
    
    // Actualizar misiones
    if($datos > 0) {
        $conn->query("INSERT INTO progreso_snake (usuario_id, datos_recogidos, puntos) VALUES (".$_SESSION["id"].", $datos, $puntos) ON DUPLICATE KEY UPDATE datos_recogidos = datos_recogidos + $datos, puntos = puntos + $puntos");
    }
}

echo "OK";
?>