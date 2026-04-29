<?php
include "config.php";

$respuesta = $_POST["respuesta"];
$correcta = $_POST["correcta"];
$exp = $_POST["explicacion"];
$tiempo_inicio = $_POST["tiempo_inicio"];
$dificultad = $_POST["dificultad"] ?? 1;
$xp_base = $_POST["xp_base"] ?? 20;

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();

$hoy = date("Y-m-d");
$ayer = date("Y-m-d", strtotime("-1 day"));

$es_correcto = ($respuesta == $correcta);
?>

<link rel="stylesheet" href="style.css">

<div class="grid-bg"></div>

<div id="particulas"></div>

<?php if($es_correcto): ?>
<script>
parent.playSound('correct');
parent.createParticles('#00ff9f');
parent.document.getElementById('gameCard').classList.add('correct');
</script>
<?php else: ?>
<script>
parent.playSound('wrong');
parent.document.getElementById('gameCard').classList.add('incorrect');
</script>
<?php endif; ?>

<div class="card" id="resultCard">
<?php if($es_correcto): ?>
    <h2 style="color:#00ff9f">✅ ¡CORRECTO!</h2>
    <?php 
    $tiempo_usado = (time() * 1000 - $tiempo_inicio) / 1000;
    $bonus_rapidez = $tiempo_usado < 5 ? 10 : ($tiempo_usado < 10 ? 5 : 0);
    $bonus_racha = min($user["racha"] * 2, 20);
    
    $xp = $user["xp"] + $xp_base + $bonus_rapidez + $bonus_racha;
    $nivel = $user["nivel"];
    $racha = $user["racha"] + 1;
    $max_racha = $user["max_racha"];
    
    if($racha > $max_racha) $max_racha = $racha;
    
    $subio_nivel = false;
    if($xp >= 100) {
        $nivel++;
        $xp = $xp - 100;
        $dificultad++;
        $subio_nivel = true;
    }
    
    if($racha % 5 == 0) {
        echo "<div class='combo-display'>🔥 Racha de $racha! +5 XP</div>";
        $xp += 5;
    }
    
    echo "<p>+{$xp_base} XP +{$bonus_rapidez} Velocidad +{$bonus_racha} Racha</p>";
    
    if($subio_nivel): ?>
    <script>parent.playSound('levelup');</script>
    <div class="levelup-effect"></div>
    <h2 style="color:#ffaa00">🚀 ¡SUBISTE DE NIVEL! Nivel <?= $nivel ?></h2>
    <?php endif; ?>
    
    <?php $conn->query("UPDATE usuarios SET xp=$xp, nivel=$nivel, racha=$racha, max_racha=$max_racha, dificultad=$dificultad, ultima_fecha=NOW(), score=score+$xp WHERE id=".$_SESSION["id"]); ?>
    
    <script>
    fetch('actualizar_mision.php?tipo=trivia');
    fetch('actualizar_mision.php?tipo=racha');
    fetch('actualizar_mision.php?tipo=juego_total');
    </script>

<?php else: ?>
    <h2 style="color:red">❌ INCORRECTO</h2>
    <?php 
    $racha = 0;
    echo "<p>Racha reiniciada</p>";
    
    $vidas = $user["vidas"] - 1;
    $conn->query("UPDATE usuarios SET racha=0, vidas=$vidas WHERE id=".$_SESSION["id"]);
    
    if($vidas <= 0): ?>
    <h1 style="color:red">GAME OVER</h1>
    <?php $conn->query("UPDATE usuarios SET vidas=3, xp=0, nivel=1, racha=0, dificultad=1 WHERE id=".$_SESSION["id"]); ?>
    <?php endif; ?>
<?php endif; ?>

<p><strong>Aprende:</strong><br><?= $exp ?></p>
<div class="btn-group">
    <a href="menu.php" class="btn-menu">← Menú</a>
    <a href="juego.php" class="btn-menu">← Jugar</a>
</div>
</div>

<script>
setTimeout(() => {
    document.getElementById('resultCard').style.animation = 'cardAppear 0.5s ease-out';
}, 100);
</script>