<?php
include "config.php";

$total = $_POST["p1"] + $_POST["p2"] + $_POST["p3"];

$id = $_SESSION["id"];

$conn->query("UPDATE usuarios SET score = score + $total WHERE id=$id");

echo "<h2>Puntos ganados: $total</h2>";
echo "<a href='game.php'>Jugar otra vez</a>";
?>