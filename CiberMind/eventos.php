<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$eventos = $conn->query("SELECT * FROM eventos WHERE activo = 1 AND fin >= CURDATE() ORDER BY fin")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Eventos - CiberMind</title>
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
.eventos-list { display: flex; flex-direction: column; gap: 15px; }
.evento {
    background: var(--bg-light); border: 1px solid #2a1a35;
    border-radius: 16px; padding: 20px;
    transition: all 0.3s;
}
.evento:hover { border-color: var(--primary); }
.evento-header {
    display: flex; align-items: center; gap: 12px; margin-bottom: 12px;
}
.evento-icon { font-size: 30px; }
.evento-titulo { font-weight: 600; font-size: 16px; flex: 1; }
.evento-badge {
    background: linear-gradient(135deg, #ffd700, #ffaa00);
    color: #000; padding: 4px 12px; border-radius: 20px; font-size: 12px; font-weight: bold;
}
.evento-desc { font-size: 13px; color: var(--text-dim); margin-bottom: 12px; }
.evento-footer {
    display: flex; justify-content: space-between; font-size: 12px; color: var(--text-dim);
}
.evento-banner {
    background: linear-gradient(135deg, rgba(191,0,255,0.1), rgba(139,0,255,0.05));
    border: 1px solid var(--primary);
}
.no-eventos {
    text-align: center; color: var(--text-dim); padding: 40px;
}
</style>

<div class="page">
    <a href="menu.php" class="back">←</a>
    <h1>📢 Eventos Especiales</h1>
    
    <div class="eventos-list">
        <?php if(count($eventos) == 0): ?>
        <div class="no-eventos">
            <span style="font-size: 40px;">📢</span>
            <p>No hay eventos activos en este momento</p>
            <p>¡Vuelve pronto!</p>
        </div>
        <?php else: ?>
        <?php foreach($eventos as $e): ?>
        <div class="evento">
            <div class="evento-header">
                <span class="evento-icon">⚡</span>
                <span class="evento-titulo"><?= htmlspecialchars($e["nombre"]) ?></span>
                <span class="evento-badge">+<?= $e["bonus"] ?>x</span>
            </div>
            <p class="evento-desc"><?= htmlspecialchars($e["descripcion"]) ?></p>
            <div class="evento-footer">
                <span>📅 Inicio: <?= date("d/m", strtotime($e["inicio"])) ?></span>
                <span>⏰ Fin: <?= date("d/m", strtotime($e["fin"])) ?></span>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>