<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$correctos = (int)($_GET["correctos"] ?? 0);

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();

echo "<link rel='stylesheet' href='style.css'>";
echo "<div class='grid-bg'></div>";
echo "<div class='card'>";

if($correctos === 5) {
    $xp = 30;
    $nivel = $user["nivel"];
    $xp_total = $user["xp"] + $xp;
    $racha = $user["racha"] + 1;
    
    if($xp_total >= 100) {
        $nivel++;
        $xp_total -= 100;
    }
    
    echo "<h2 style='color:#00ff9f'>¡CORRECTO! ✅</h2>";
    echo "<h3>+$xp XP</h3>";
    
    $conn->query("UPDATE usuarios SET xp=$xp_total, nivel=$nivel, racha=$racha, ultima_fecha=NOW() WHERE id=".$_SESSION["id"]);
    
    <script>
    fetch('actualizar_mision.php?tipo=ordenar');
    fetch('actualizar_mision.php?tipo=juego_total');
    </script>
} else {
    $racha = 0;
    $vidas = $user["vidas"] - 1;
    
    echo "<h2 style='color:red'>INCORRECTO ❌</h2>";
    echo "<p>Inténtalo de nuevo</p>";
    
    $conn->query("UPDATE usuarios SET racha=0, vidas=$vidas WHERE id=".$_SESSION["id"]);
    
    if($vidas <= 0) {
        echo "<h1 style='color:red'>GAME OVER</h1>";
        $conn->query("UPDATE usuarios SET vidas=3, nivel=1, xp=0, racha=0 WHERE id=".$_SESSION["id"]);
    }
}

echo "<a href='menu.php' class='btn-menu'>← Menú</a>";
echo "</div>";
?>