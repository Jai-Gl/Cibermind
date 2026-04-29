<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();

$stats = [
    ["label" => "Partidas", "value" => floor($user["score"] / 20), "icon" => "🎮"],
    ["label" => "Aciertos", "value" => floor($user["score"] / 2), "icon" => "✅"],
    ["label" => "Racha", "value" => $user["racha"], "icon" => "🔥"],
    ["label" => "Mejor Racha", "value" => $user["max_racha"], "icon" => "⭐"],
    ["label" => "Nivel", "value" => $user["nivel"], "icon" => "📊"],
    ["label" => "XP", "value" => $user["xp"], "icon" => "✨"],
    ["label" => "Puntos", "value" => $user["score"], "icon" => "🏆"],
    ["label" => "Vidas", "value" => $user["vidas"], "icon" => "❤️"],
];

$rank = $conn->query("SELECT COUNT(*) as c FROM usuarios WHERE score > ".$user["score"])->fetch_assoc();
$posicion = $rank["c"] + 1;
?>

<!DOCTYPE html>
<html>
<head>
<title>Stats - CiberMind</title>
</head>
<body>

<style>
:root {
    --primary: #bf00ff;
    --primary-dark: #8b00ff;
    --bg: #08020a;
    --bg-light: #18101a;
    --text: #ffffff;
    --text-dim: #999999;
    --glow: rgba(191,0,255,0.15);
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    background: radial-gradient(ellipse at 30% 20%, rgba(191,0,255,0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 80%, rgba(139,0,255,0.1) 0%, transparent 40%), #08020a;
    min-height: 100vh; font-family: system-ui, sans-serif; padding: 20px;
}
.page { max-width: 600px; margin: 0 auto; }
.back {
    position: fixed; top: 20px; left: 20px;
    width: 45px; height: 45px;
    background: var(--bg-light); border: 1px solid #2a1a35;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    color: var(--primary); text-decoration: none; font-size: 18px;
}
h1 { text-align: center; color: var(--primary); margin-bottom: 25px; }
.rank-card {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: 16px; padding: 20px; text-align: center; margin-bottom: 25px; color: #fff;
}
.rank-num { font-size: 36px; font-weight: bold; }
.stats-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; }
.stat-card {
    background: var(--bg-light); border: 1px solid #2a1a35;
    border-radius: 14px; padding: 18px; text-align: center;
}
.stat-icon { font-size: 24px; display: block; margin-bottom: 8px; }
.stat-value { font-size: 24px; font-weight: bold; color: var(--primary); display: block; }
.stat-label { font-size: 11px; color: var(--text-dim); }
.progreso-section {
    margin-top: 30px; background: var(--bg-light); border: 1px solid #2a1a35;
    border-radius: 14px; padding: 20px;
}
.progreso-section h3 { font-size: 14px; margin-bottom: 15px; }
.progress-bar { background: #2a1a35; height: 12px; border-radius: 10px; overflow: hidden; }
.progress-fill {
    background: linear-gradient(90deg, var(--primary), var(--primary-dark));
    height: 100%; border-radius: 10px;
}
.progress-text { display: block; text-align: right; font-size: 12px; color: var(--text-dim); margin-top: 8px; }
</style>

<div class="page">
    <a href="menu.php" class="back">←</a>
    <h1>📊 Mis Estadísticas</h1>
    
    <div class="rank-card">
        <span class="rank-label">Posición Global</span>
        <span class="rank-num">#<?= $posicion ?></span>
    </div>
    
    <div class="stats-grid">
        <?php foreach($stats as $s): ?>
        <div class="stat-card">
            <span class="stat-icon"><?= $s["icon"] ?></span>
            <span class="stat-value"><?= $s["value"] ?></span>
            <span class="stat-label"><?= $s["label"] ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    
    <div class="progreso-section">
        <h3>Progreso al siguiente nivel</h3>
        <div class="progress-bar">
            <div class="progress-fill" style="width: <?= $user["xp"] ?>%"></div>
        </div>
        <span class="progress-text"><?= $user["xp"] ?>% / 100%</span>
    </div>
</div>

</body>
</html>