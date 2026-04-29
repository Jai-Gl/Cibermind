<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();

$total_conceptos = 0;
$total_juegos = 0;

$try1 = $conn->query("SELECT COUNT(DISTINCT concepto) as c FROM conceptos_aprendidos WHERE usuario_id=".$_SESSION["id"]);
if($try1) $total_conceptos = $try1->fetch_assoc()['c'] ?? 0;

$try2 = $conn->query("SELECT SUM(partidas) as c FROM progreso_juegos WHERE usuario_id=".$_SESSION["id"]);
if($try2) $total_juegos = $try2->fetch_assoc()['c'] ?? 0;

$avatares = $conn->query("SELECT * FROM avatares ORDER BY nivel_req")->fetch_all(MYSQLI_ASSOC);
$desbloqueados = $conn->query("SELECT avatar_id FROM usuario_avatares WHERE usuario_id=".$_SESSION["id"])->fetch_all(MYSQLI_ASSOC);
$desbloqueados_ids = array_column($desbloqueados, 'avatar_id');

$avatar_actual = isset($user["avatar_id"]) ? $user["avatar_id"] : 1;

foreach($avatares as $i => $a) {
    $avatares[$i]["desbloqueado"] = in_array($a["id"], $desbloqueados_ids);
    $avatares[$i]["puede_desbloquear"] = false;
    
    if(!$avatares[$i]["desbloqueado"]) {
        if($user["nivel"] >= $a["nivel_req"] && $total_conceptos >= $a["conceptos_req"] && $total_juegos >= $a["juegos_req"]) {
            $avatares[$i]["puede_desbloquear"] = true;
        }
    }
}

if(isset($_POST["desbloquear"])) {
    $avatar_id = (int)$_POST["avatar_id"];
    $check = $conn->query("SELECT * FROM avatares WHERE id=$avatar_id")->fetch_assoc();
    
    if($user["nivel"] >= $check["nivel_req"] && $total_conceptos >= $check["conceptos_req"] && $total_juegos >= $check["juegos_req"]) {
        $conn->query("INSERT IGNORE INTO usuario_avatares (usuario_id, avatar_id, desbloqueado_at) VALUES (".$_SESSION["id"].", $avatar_id, NOW())");
        header("Location: avatares.php");
    }
}

if(isset($_POST["seleccionar"])) {
    $avatar_id = (int)$_POST["avatar_id"];
    $check = $conn->query("SELECT avatar_id FROM usuario_avatares WHERE usuario_id=".$_SESSION["id"]." AND avatar_id=$avatar_id")->fetch_assoc();
    if($check) {
        $conn->query("UPDATE usuarios SET avatar_id=$avatar_id WHERE id=".$_SESSION["id"]);
        header("Location: avatares.php");
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Avatares - CiberMind</title>
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="particles-bg" id="particles"></div>
<div class="grid-bg"></div>

<div class="container">
    
    <header class="game-header">
        <a href="menu.php" class="back-btn">←</a>
        <div class="game-title-simple">
            <span class="game-icon-sm">😺</span>
            <h1>Avatares de Gatos</h1>
        </div>
        <div class="game-stats-mini">
            <span class="mini-stat">📊 <?= $user["nivel"] ?></span>
            <span class="mini-stat">❤️ <?= $user["vidas"] ?></span>
        </div>
    </header>

    <!-- Progress Stats -->
    <div class="progress-stats mb-4">
        <div class="stat-card">
            <span class="stat-icon">📚</span>
            <span class="stat-number"><?= $total_conceptos ?></span>
            <span class="stat-label">Conceptos</span>
        </div>
        <div class="stat-card">
            <span class="stat-icon">🎮</span>
            <span class="stat-number"><?= $total_juegos ?></span>
            <span class="stat-label">Partidas</span>
        </div>
        <div class="stat-card">
            <span class="stat-icon">📊</span>
            <span class="stat-number"><?= $user["nivel"] ?></span>
            <span class="stat-label">Nivel</span>
        </div>
    </div>

    <!-- Current Avatar -->
    <div class="current-avatar-section mb-4">
        <?php 
        $actual = $conn->query("SELECT * FROM avatares WHERE id=$avatar_actual")->fetch_assoc();
        ?>
        <div class="current-avatar-card">
            <div class="avatar-display">
                <span class="avatar-icon-large"><?= $actual["icono"] ?></span>
            </div>
            <div class="avatar-info">
                <h3>Avatar Actual</h3>
                <p><?= $actual["nombre"] ?></p>
            </div>
        </div>
    </div>

    <!-- Avatars Grid -->
    <div class="avatars-grid">
        <?php foreach($avatares as $a): ?>
        <div class="avatar-card <?= $a['desbloqueado'] ? 'unlocked' : ($a['puede_desbloquear'] ? 'can-unlock' : 'locked') ?>">
            <div class="avatar-icon"><?= $a["icono"] ?></div>
            
            <?php if($a['desbloqueado']): ?>
            <form method="POST">
                <input type="hidden" name="avatar_id" value="<?= $a['id'] ?>">
                <button type="submit" name="seleccionar" class="btn-select">
                    <?= $avatar_actual == $a['id'] ? '✓ Seleccionado' : 'Seleccionar' ?>
                </button>
            </form>
            <span class="status-badge unlocked">✓ Desbloqueado</span>
            
            <?php elseif($a['puede_desbloquear']): ?>
            <form method="POST">
                <input type="hidden" name="avatar_id" value="<?= $a['id'] ?>">
                <button type="submit" name="desbloquear" class="btn-unlock">
                    🔓 Desbloquear
                </button>
            </form>
            <span class="status-badge can-unlock">¡Disponible!</span>
            
            <?php else: ?>
            <span class="status-badge locked">🔒 Bloqueado</span>
            <?php endif; ?>
            
            <h4><?= $a["nombre"] ?></h4>
            
            <div class="avatar-requirements">
                <div class="req <?= $user["nivel"] >= $a['nivel_req'] ? 'met' : '' ?>">
                    <span>Nivel <?= $a["nivel_req"] ?></span>
                </div>
                <div class="req <?= $total_conceptos >= $a['conceptos_req'] ? 'met' : '' ?>">
                    <span>📚 <?= $a["conceptos_req"] ?></span>
                </div>
                <div class="req <?= $total_juegos >= $a['juegos_req'] ? 'met' : '' ?>">
                    <span>🎮 <?= $a["juegos_req"] ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Back -->
    <div class="text-center mt-4">
        <a href="menu.php" class="btn btn-ghost-outline">← Volver al Menú</a>
    </div>

</div>

<script>
function createParticles() {
    const container = document.getElementById('particles');
    const colors = ['#bf00ff', '#00d4ff', '#00ff9f', '#ffd700'];
    for(let i = 0; i < 15; i++) {
        const p = document.createElement('div');
        p.className = 'particle';
        p.style.cssText = `left: ${Math.random() * 100}%; top: ${Math.random() * 100}%; background: ${colors[i % colors.length]}; animation-delay: ${Math.random() * 5}s;`;
        container.appendChild(p);
    }
}
createParticles();
</script>

<style>
.game-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 0;
    margin-bottom: 30px;
}
.back-btn {
    width: 50px; height: 50px;
    background: rgba(18, 11, 26, 0.8);
    border: 1px solid rgba(191, 0, 255, 0.3);
    border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.3rem; color: #bf00ff; transition: all 0.3s;
}
.back-btn:hover { background: rgba(191, 0, 255, 0.2); transform: translateX(-5px); }
.game-title-simple { display: flex; align-items: center; gap: 15px; }
.game-icon-sm { font-size: 2.5rem; }
.game-title-simple h1 { font-family: 'Orbitron', sans-serif; font-size: 1.8rem; color: #fff; }
.game-stats-mini { display: flex; gap: 10px; }
.mini-stat {
    padding: 8px 14px; background: rgba(18, 11, 26, 0.8);
    border: 1px solid rgba(191, 0, 255, 0.2); border-radius: 10px;
    font-family: 'Rajdhani', sans-serif; font-size: 0.9rem;
}

.progress-stats {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-bottom: 30px;
}
.stat-card {
    display: flex; flex-direction: column; align-items: center;
    background: linear-gradient(135deg, rgba(18, 11, 26, 0.9), rgba(13, 10, 21, 0.95));
    border: 1px solid rgba(191, 0, 255, 0.2);
    border-radius: 16px; padding: 20px 35px;
}
.stat-icon { font-size: 1.5rem; margin-bottom: 5px; }
.stat-number { font-family: 'Orbitron', sans-serif; font-size: 2rem; font-weight: 900; color: #bf00ff; }
.stat-label { color: #666; font-size: 0.85rem; }

.current-avatar-section { margin-bottom: 30px; }
.current-avatar-card {
    display: flex; align-items: center; gap: 25px;
    background: linear-gradient(135deg, rgba(191, 0, 255, 0.2), rgba(191, 0, 255, 0.05));
    border: 2px solid #bf00ff; border-radius: 20px; padding: 25px;
}
.avatar-display {
    width: 100px; height: 100px;
    background: linear-gradient(135deg, rgba(191, 0, 255, 0.3), rgba(191, 0, 255, 0.1));
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
}
.avatar-icon-large { font-size: 4rem; }
.avatar-info h3 { font-family: 'Rajdhani', sans-serif; font-size: 1rem; color: #666; margin-bottom: 5px; }
.avatar-info p { font-family: 'Orbitron', sans-serif; font-size: 1.5rem; color: #fff; }

.avatars-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
}
.avatar-card {
    display: flex; flex-direction: column; align-items: center;
    background: rgba(18, 11, 26, 0.8);
    border: 2px solid rgba(191, 0, 255, 0.2);
    border-radius: 20px; padding: 20px; text-align: center;
    transition: all 0.3s;
}
.avatar-card.unlocked { border-color: #00ff9f; }
.avatar-card.can-unlock { 
    border-color: #ffd700;
    animation: glow 2s infinite;
}
.avatar-card.locked { opacity: 0.6; }
.avatar-icon { font-size: 3.5rem; margin-bottom: 15px; }
.avatar-card h4 {
    font-family: 'Orbitron', sans-serif; font-size: 1rem; color: #fff;
    margin-bottom: 15px;
}
.avatar-requirements {
    display: flex; gap: 8px; flex-wrap: wrap; justify-content: center;
}
.req {
    padding: 4px 8px; background: rgba(0,0,0,0.3); border-radius: 6px;
    font-size: 0.75rem; color: #666;
}
.req.met { color: #00ff9f; }

.btn-select, .btn-unlock {
    width: 100%; padding: 10px 15px; border: none; border-radius: 10px;
    font-family: 'Rajdhani', sans-serif; font-weight: 600; cursor: pointer;
    margin-bottom: 10px; transition: all 0.3s;
}
.btn-select {
    background: linear-gradient(135deg, #bf00ff, #8b00ff); color: #fff;
}
.btn-select:hover { transform: scale(1.05); }
.btn-unlock {
    background: linear-gradient(135deg, #ffd700, #ff8800); color: #000;
    animation: pulse 1s infinite;
}

.status-badge {
    font-size: 0.8rem; padding: 4px 10px; border-radius: 20px;
}
.status-badge.unlocked { background: rgba(0, 255, 159, 0.2); color: #00ff9f; }
.status-badge.can-unlock { background: rgba(255, 215, 0, 0.2); color: #ffd700; }
.status-badge.locked { background: rgba(100, 100, 100, 0.2); color: #666; }

.particles-bg { position: fixed; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; z-index: -1; overflow: hidden; }
.particle { position: absolute; width: 4px; height: 4px; border-radius: 50%; opacity: 0.5; animation: floatParticle 10s linear infinite; }
@keyframes floatParticle { 0% { transform: translateY(100vh); opacity: 0; } 10% { opacity: 0.5; } 90% { opacity: 0.5; } 100% { transform: translateY(-100px); opacity: 0; } }
@keyframes glow { 0%, 100% { box-shadow: 0 0 20px rgba(255, 215, 0, 0.3); } 50% { box-shadow: 0 0 40px rgba(255, 215, 0, 0.6); } }
@keyframes pulse { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.02); } }

.btn.btn-ghost-outline { background: transparent; border: 1px solid rgba(191, 0, 255, 0.3); color: #bf00ff; padding: 12px 30px; border-radius: 12px; font-family: 'Rajdhani', sans-serif; font-weight: 600; text-decoration: none; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
.btn.btn-ghost-outline:hover { background: rgba(191, 0, 255, 0.1); border-color: #bf00ff; }
.mt-4 { margin-top: 20px; }
.text-center { text-align: center; }

@media (max-width: 768px) {
    .progress-stats { gap: 10px; }
    .stat-card { padding: 15px 20px; }
    .current-avatar-card { flex-direction: column; text-align: center; }
    .avatars-grid { grid-template-columns: repeat(2, 1fr); }
}
</style>

</body>
</html>