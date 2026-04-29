<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();

$mis_logros = $conn->query("
    SELECT l.*, ul.unlocked_at 
    FROM logros l 
    LEFT JOIN usuarios_logros ul ON l.id = ul.logro_id AND ul.usuario_id = ".$_SESSION["id"]."
")->fetch_all(MYSQLI_ASSOC);

$desbloqueados = array_filter($mis_logros, fn($l) => $l["unlocked_at"] !== null);
$bloqueados = array_filter($mis_logros, fn($l) => $l["unlocked_at"] === null);

$requisitos = [
    "primera_victoria" => $user["score"] >= 10,
    "racha_5" => $user["max_racha"] >= 5,
    "racha_10" => $user["max_racha"] >= 10,
    "racha_25" => $user["max_racha"] >= 25,
    "nivel_5" => $user["nivel"] >= 5,
    "nivel_10" => $user["nivel"] >= 10,
    "nivel_25" => $user["nivel"] >= 25,
    "score_100" => $user["score"] >= 100,
    "score_1000" => $user["score"] >= 1000,
];

$xp_total_logros = 0;
foreach($bloqueados as &$logro) {
    if(isset($requisitos[$logro["requisito"]]) && $requisitos[$logro["requisito"]]) {
        $conn->query("INSERT IGNORE INTO usuarios_logros (usuario_id, logro_id) VALUES (".$_SESSION["id"].", ".$logro["id"].")");
        $conn->query("UPDATE usuarios SET xp = xp + ".$logro["xp_reward"]." WHERE id = ".$_SESSION["id"]);
        $logro["unlocked_at"] = date("Y-m-d H:i:s");
        $xp_total_logros += $logro["xp_reward"];
    }
}
$xp_total_logros = array_sum(array_column($desbloqueados, 'xp_reward'));
?>

<link rel="stylesheet" href="style.css">

<div class="glow-bg"></div>
<div class="grid-bg"></div>
<div id="particulas"></div>

<a href="menu.php" class="btn-float">←</a>

<div class="logros-page">
    <div class="hero-section">
        <div class="hero-icon">🏆</div>
        <h1>Logros</h1>
        <p>Desbloquea logros para ganar recompensas</p>
    </div>

    <div class="stats-bar">
        <div class="stat-item">
            <span class="stat-value"><?= count($desbloqueados) ?></span>
            <span class="stat-label">Completados</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <span class="stat-value"><?= count($mis_logros) - count($desbloqueados) ?></span>
            <span class="stat-label">Pendientes</span>
        </div>
        <div class="stat-divider"></div>
        <div class="stat-item">
            <span class="stat-value">+<?= $xp_total_logros ?></span>
            <span class="stat-label">XP Ganado</span>
        </div>
    </div>

    <div class="progress-circle">
        <svg viewBox="0 0 120 120">
            <circle cx="60" cy="60" r="54" class="bg"/>
            <circle cx="60" cy="60" r="54" class="progress" 
                stroke-dasharray="<?= count($mis_logros) > 0 ? (count($desbloqueados) / count($mis_logros)) * 339 : 0 ?>, 339"/>
        </svg>
        <div class="progress-text">
            <span class="percent"><?= count($mis_logros) > 0 ? round((count($desbloqueados) / count($mis_logros)) * 100) : 0 ?>%</span>
            <span class="label">Completado</span>
        </div>
    </div>

    <?php if(count($desbloqueados) > 0): ?>
    <div class="section">
        <div class="section-header">
            <span class="section-icon">⭐</span>
            <h2>Desbloqueados</h2>
        </div>
        <div class="logros-list">
            <?php foreach($desbloqueados as $i => $l): ?>
            <div class="logro-item" style="animation-delay: <?= $i * 0.05 ?>s">
                <div class="logro-badge"><?= $l["icono"] ?></div>
                <div class="logro-info">
                    <div class="logro-title"><?= $l["nombre"] ?></div>
                    <div class="logro-desc"><?= $l["descripcion"] ?></div>
                </div>
                <div class="logro-xp">+<?= $l["xp_reward"] ?> XP</div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <div class="section">
        <div class="section-header">
            <span class="section-icon">🎯</span>
            <h2>Pendientes</h2>
        </div>
        <div class="logros-list">
            <?php foreach($bloqueados as $i => $l): ?>
            <div class="logro-item locked" style="animation-delay: <?= $i * 0.03 ?>s">
                <div class="logro-badge blur"><?= $l["icono"] ?></div>
                <div class="logro-info">
                    <div class="logro-title"><?= $l["nombre"] ?></div>
                    <div class="logro-desc"><?= $l["descripcion"] ?></div>
                </div>
                <div class="logro-xp">+<?= $l["xp_reward"] ?> XP</div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
createParticles();
function createParticles() {
    const container = document.getElementById('particulas');
    const colors = ['#00ff9f', '#ffd700', '#ff00ff', '#00d4ff'];
    for(let i = 0; i < 30; i++) {
        setTimeout(() => {
            const p = document.createElement('div');
            p.style.cssText = `
                position: fixed;
                width: ${Math.random() * 6 + 2}px;
                height: ${Math.random() * 6 + 2}px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                border-radius: 50%;
                left: ${Math.random() * 100}vw;
                top: 100vh;
                animation: floatUp ${Math.random() * 3 + 2}s ease-out forwards;
                opacity: ${Math.random() * 0.5 + 0.3};
                pointer-events: none;
            `;
            document.body.appendChild(p);
            setTimeout(() => p.remove(), 5000);
        }, i * 100);
    }
}
</script>

<style>
.logros-page {
    max-width: 650px;
    margin: 0 auto;
    padding: 20px;
    animation: pageEnter 0.6s ease-out;
}

@keyframes pageEnter {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}

.glow-bg {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 600px;
    height: 600px;
    background: radial-gradient(circle, rgba(0,255,159,0.15) 0%, transparent 70%);
    pointer-events: none;
    z-index: -2;
    animation: glowPulse 4s ease-in-out infinite;
}

@keyframes glowPulse {
    0%, 100% { transform: translate(-50%, -50%) scale(1); opacity: 0.5; }
    50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.8; }
}

.btn-float {
    position: fixed;
    top: 20px;
    left: 20px;
    width: 50px;
    height: 50px;
    background: var(--bg-light);
    border: 2px solid var(--primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    color: var(--primary);
    text-decoration: none;
    z-index: 100;
    transition: all 0.3s;
    animation: floatIn 0.5s ease-out 0.3s backwards;
}

@keyframes floatIn {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}

.btn-float:hover {
    background: var(--primary);
    color: #000;
    transform: scale(1.1);
}

.hero-section {
    text-align: center;
    margin-bottom: 40px;
    animation: heroEnter 0.8s ease-out;
}

@keyframes heroEnter {
    from { opacity: 0; transform: scale(0.8); }
    50% { transform: scale(1.05); }
    to { opacity: 1; transform: scale(1); }
}

.hero-icon {
    font-size: 80px;
    margin-bottom: 15px;
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.hero-section h1 {
    font-size: 48px;
    margin-bottom: 10px;
    background: linear-gradient(135deg, #ffd700, #ffaa00, #ffd700);
    background-size: 200% 200%;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

.hero-section p {
    color: var(--text-dim);
    font-size: 16px;
}

.stats-bar {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 30px;
    background: var(--bg-light);
    border: 1px solid #222;
    border-radius: 50px;
    padding: 20px 40px;
    margin-bottom: 30px;
}

.stat-item {
    text-align: center;
}

.stat-value {
    display: block;
    font-size: 28px;
    font-weight: bold;
    color: var(--primary);
}

.stat-label {
    font-size: 12px;
    color: var(--text-dim);
}

.stat-divider {
    width: 1px;
    height: 40px;
    background: #333;
}

.progress-circle {
    position: relative;
    width: 150px;
    height: 150px;
    margin: 30px auto;
}

.progress-circle svg {
    transform: rotate(-90deg);
}

.progress-circle .bg {
    fill: none;
    stroke: #222;
    stroke-width: 8;
}

.progress-circle .progress {
    fill: none;
    stroke: url(#gradient);
    stroke-width: 8;
    stroke-linecap: round;
    transition: stroke-dasharray 1s ease-out;
}

.progress-circle .progress-text {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    text-align: center;
}

.progress-circle .percent {
    display: block;
    font-size: 32px;
    font-weight: bold;
    color: var(--primary);
}

.progress-circle .label {
    font-size: 12px;
    color: var(--text-dim);
}

.section {
    margin-top: 40px;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 15px;
    border-bottom: 1px solid #222;
}

.section-icon {
    font-size: 24px;
}

.section-header h2 {
    font-size: 20px;
    margin: 0;
}

.logros-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.logro-item {
    display: flex;
    align-items: center;
    gap: 15px;
    background: var(--bg-light);
    border: 1px solid #222;
    border-radius: 16px;
    padding: 16px 20px;
    transition: all 0.3s;
    animation: slideIn 0.5s ease-out backwards;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(-20px); }
    to { opacity: 1; transform: translateX(0); }
}

.logro-item:hover {
    transform: translateX(10px);
    border-color: var(--primary);
    box-shadow: 0 5px 25px rgba(0,255,159,0.15);
}

.logro-item.unlocked {
    border-color: rgba(0,255,159,0.3);
}

.logro-item.locked {
    opacity: 0.5;
}

.logro-item.locked .logro-badge {
    filter: grayscale(1);
    opacity: 0.4;
}

.logro-badge {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, var(--bg-lighter), var(--bg));
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.logro-item.unlocked .logro-badge {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    box-shadow: 0 0 20px rgba(0,255,159,0.3);
}

.logro-info {
    flex: 1;
}

.logro-title {
    font-weight: 600;
    font-size: 15px;
    margin-bottom: 4px;
}

.logro-item.unlocked .logro-title {
    color: var(--primary);
}

.logro-desc {
    font-size: 12px;
    color: var(--text-dim);
}

.logro-xp {
    background: linear-gradient(135deg, #ffd700, #ffaa00);
    color: #000;
    padding: 6px 14px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: bold;
}

@media (max-width: 600px) {
    .stats-bar {
        gap: 15px;
        padding: 15px 20px;
    }
    
    .stat-value {
        font-size: 22px;
    }
    
    .logro-item {
        padding: 14px;
    }
    
    .logro-badge {
        width: 40px;
        height: 40px;
        font-size: 20px;
    }
}
</style>