<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();

$hoy = date("Y-m-d");
$ultimo_bono = $user["ultima_fecha"] ?? "2020-01-01";

$msg = "";
$ya_gano = ($ultimo_bono === $hoy);

if(isset($_POST["action"])) {
    $premio = 15;
    $conn->query("UPDATE usuarios SET score=score+$premio, vidas=vidas+1, ultima_fecha=NOW() WHERE id=".$_SESSION["id"]);
    $msg = "🎉 +$premio Puntos y +1 Vida!";
    $ya_gano = true;
    $user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();
}

if($ya_gano && $msg == "") {
    $msg = "⏰ ¡Vuelve mañana!";
}
?>

<link rel="stylesheet" href="style.css">

<div class="grid-bg"></div>

<div class="page">
    <a href="menu.php" class="back">←</a>
    
    <h1>🎁 Bono Diario</h1>
    
    <div class="calendario">
        <?php for($i = 1; $i <= 7; $i++): ?>
        <div class="dia <?= ($i <= ($ya_gano ? 7 : 0)) ? "ganado" : "" ?>">
            <span class="dia-num"><?= $i ?></span>
            <span class="dia-prem">+<?= 10 + ($i * 5) ?></span>
        </div>
        <?php endfor; ?>
    </div>
    
    <?php if($msg): ?>
    <div class="msg <?= $ya_gano && $msg != "" ? "bonus" : "" ?>"><?= $msg ?></div>
    <?php endif; ?>
    
    <?php if(!$ya_gano): ?>
    <form method="POST">
        <input type="hidden" name="action" value="1">
        <button type="submit" class="btn-recoger">
            🎁 RECOGER BONO
        </button>
    </form>
    <?php endif; ?>
    
    <div class="info">
        <p>¡Collecte daily rewards every day!</p>
        <p>Day 7 = Maximum reward!</p>
    </div>
</div>

<style>
.page { max-width: 600px; margin: 0 auto; padding: 20px; }
.back {
    position: fixed; top: 20px; left: 20px;
    width: 45px; height: 45px;
    background: var(--bg-light); border: 1px solid #222;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    color: var(--primary); text-decoration: none; font-size: 18px;
}
h1 { text-align: center; color: var(--primary); margin-bottom: 30px; }
.calendario { display: flex; justify-content: center; gap: 10px; margin-bottom: 30px; flex-wrap: wrap; }
.dia {
    width: 45px; height: 60px;
    background: var(--bg-light); border: 2px solid #222;
    border-radius: 10px;
    display: flex; flex-direction: column; align-items: center; justify-content: center;
}
.dia.ganado { background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border-color: var(--primary); }
.dia-num { font-size: 14px; color: #fff; }
.dia.ganado .dia-num { color: #000; }
.dia-prem { font-size: 12px; color: var(--text-dim); font-weight: bold; }
.dia.ganado .dia-prem { color: #000; }
.msg {
    text-align: center; padding: 15px; border-radius: 12px; margin-bottom: 20px;
    background: var(--bg-light); border: 1px solid #222; color: var(--text-dim);
}
.msg.bonus { background: linear-gradient(135deg, rgba(0,255,159,0.15), rgba(0,255,159,0.05)); border-color: var(--primary); color: var(--primary); }
.btn-recoger {
    display: block; width: 100%;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border: none; padding: 18px; border-radius: 14px;
    font-size: 18px; font-weight: bold; color: #000;
    cursor: pointer; transition: all 0.3s;
}
.info { text-align: center; margin-top: 30px; color: var(--text-dim); font-size: 13px; }
.info p { margin: 5px 0; }
</style>