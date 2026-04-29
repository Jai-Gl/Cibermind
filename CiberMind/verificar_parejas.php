<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$aciertos = (int)($_GET["aciertos"] ?? 0);
$intentos = (int)($_GET["intentos"] ?? 999);

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();

echo "<link rel='stylesheet' href='style.css'>";

echo "<div class='grid-bg'></div>";
echo "<div class='card'>";

if($aciertos >= 4) {
    $xp = 25 - min($intentos - $aciertos, 10);
    if($xp < 10) $xp = 10;
    
    $nivel = $user["nivel"];
    $xp_total = $user["xp"] + $xp;
    $racha = $user["racha"] + 1;
    $max_racha = $user["max_racha"];
    
    if($racha > $max_racha) $max_racha = $racha;
    
    if($xp_total >= 100) {
        $nivel++;
        $xp_total -= 100;
    }
    
    echo "<h2 style='color:#00ff9f'>¡COMPLETADO! ✅</h2>";
    echo "<h3>+$xp XP</h3>";
    echo "<p>Intentos: $intentos</p>";
    
    $conn->query("UPDATE usuarios SET xp=$xp_total, nivel=$nivel, racha=$racha, max_racha=$max_racha, ultima_fecha=NOW(), score=score+$xp WHERE id=".$_SESSION["id"]);
    
    <script>
    fetch('actualizar_mision.php?tipo=parejas');
    fetch('actualizar_mision.php?tipo=juego_total');
    </script>
} else {
    $racha = 0;
    $vidas = $user["vidas"] - 1;
    
    echo "<h2 style='color:red'>Tiempo Agotado ❌</h2>";
    
    $conn->query("UPDATE usuarios SET racha=0, vidas=$vidas WHERE id=".$_SESSION["id"]);
    
    if($vidas <= 0) {
        echo "<h1 style='color:red'>GAME OVER</h1>";
        $conn->query("UPDATE usuarios SET vidas=3, nivel=1, xp=0, racha=0 WHERE id=".$_SESSION["id"]);
    }
}

echo "<a href='menu.php' class='btn-menu'>← Menú</a>";
echo "</div>";
?>