<?php
include "config.php";
if(!isset($_SESSION["id"])) exit;

$concepto = $_GET["concepto"] ?? "";
if($concepto) {
    $stmt = $conn->prepare("INSERT IGNORE INTO conceptos_aprendidos (usuario_id, concepto, fecha) VALUES (?, ?, CURDATE())");
    $stmt->bind_param("is", $_SESSION["id"], $concepto);
    $stmt->execute();
}
?>