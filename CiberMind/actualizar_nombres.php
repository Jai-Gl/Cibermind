<?php
include "config.php";

// Actualizar nombres de avatares
$conn->query("UPDATE avatares SET nombre = 'Novato' WHERE id = 1");
$conn->query("UPDATE avatares SET nombre = 'Aprendiz' WHERE id = 2");
$conn->query("UPDATE avatares SET nombre = 'Analista' WHERE id = 3");
$conn->query("UPDATE avatares SET nombre = 'Defensor' WHERE id = 4");
$conn->query("UPDATE avatares SET nombre = 'Especialista' WHERE id = 5");
$conn->query("UPDATE avatares SET nombre = 'Ciberguerrero' WHERE id = 6");
$conn->query("UPDATE avatares SET nombre = 'Shadow Master' WHERE id = 7");
$conn->query("UPDATE avatares SET nombre = 'Leyenda Cibernética' WHERE id = 8");

echo "✓ Nombres actualizados:\n";
echo "1. Novato 🐱\n";
echo "2. Aprendiz 😺\n";
echo "3. Analista 😼\n";
echo "4. Defensor 🐈\n";
echo "5. Especialista 🦊\n";
echo "6. Ciberguerrero 🤖\n";
echo "7. Shadow Master 🐉\n";
echo "8. Leyenda Cibernética 🌌\n";
?>