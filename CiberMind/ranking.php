<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();

$rankings = $conn->query("
    SELECT username, nivel, score, racha, max_racha, xp 
    FROM usuarios 
    ORDER BY score DESC, nivel DESC, xp DESC
")->fetch_all(MYSQLI_ASSOC);

$mi_posicion = 1;
foreach($rankings as $i => $r) {
    if($r["username"] == $user["username"]) {
        $mi_posicion = $i + 1;
        break;
    }
}
?>

<link rel="stylesheet" href="style.css">

<div class="grid-bg"></div>
<div class="glow-bg"></div>

<a href="menu.php" class="btn-float">←</a>

<div class="ranking-page">
    <div class="hero-section">
        <div class="hero-icon">🏅</div>
        <h1>Ranking</h1>
        <p>Competencia global</p>
    </div>

    <div class="my-rank-card">
        <div class="rank-position">#<?= $mi_posicion ?></div>
        <div class="rank-info">
            <span class="rank-name"><?= htmlspecialchars($user["username"]) ?></span>
            <span class="rank-stats">
                <span>📊 <?= $user["nivel"] ?></span>
                <span>🔥 <?= $user["racha"] ?></span>
                <span>✨ <?= $user["xp"] ?>%</span>
            </span>
        </div>
        <div class="rank-score"><?= $user["score"] ?> pts</div>
    </div>

    <div class="top-3">
        <?php 
        $top = array_slice($rankings, 0, 3);
        $medals = ['🥇', '🥈', '🥉'];
        $colors = ['#ffd700', '#c0c0c0', '#cd7f32'];
        foreach($top as $i => $r): 
        ?>
        <div class="top-card" style="animation-delay: <?= $i * 0.1 ?>s">
            <div class="top-medal"><?= $medals[$i] ?></div>
            <div class="top-info">
                <div class="top-name"><?= htmlspecialchars($r["username"]) ?></div>
                <div class="top-stats">Nivel <?= $r["nivel"] ?> • <?= $r["score"] ?> pts</div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="ranking-list">
        <div class="list-header">
            <span>Posición</span>
            <span>Jugador</span>
            <span>Puntos</span>
        </div>
        <?php foreach($rankings as $i => $r): ?>
        <div class="ranking-row <?= $r['username'] == $user['username'] ? 'mine' : '' ?>" style="animation-delay: <?= $i * 0.03 ?>s">
            <span class="pos">#<?= $i + 1 ?></span>
            <span class="name"><?= htmlspecialchars($r["username"]) ?></span>
            <span class="score"><?= $r["score"] ?></span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function createParticles() {
    const container = document.createElement('div');
    container.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:9999';
    document.body.appendChild(container);
    const colors = ['#00ff9f', '#ffd700', '#ff00ff'];
    for(let i = 0; i < 20; i++) {
        setTimeout(() => {
            const p = document.createElement('div');
            p.style.cssText = `
                position: absolute;
                left: ${Math.random() * 100}vw;
                top: ${Math.random() * 50}vh;
                width: ${Math.random() * 4 + 2}px;
                height: ${Math.random() * 4 + 2}px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                border-radius: 50%;
                animation: float ${Math.random() * 3 + 2}s ease-out forwards;
            `;
            container.appendChild(p);
            setTimeout(() => p.remove(), 5000);
        }, i * 150);
    }
}
createParticles();
</script>

<style>
.ranking-page {
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    animation: pageEnter 0.6s ease-out;
}

@keyframes pageEnter {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
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
}

.btn-float:hover {
    background: var(--primary);
    color: #000;
    transform: scale(1.1);
}

.hero-section {
    text-align: center;
    margin-bottom: 30px;
}

.hero-icon {
    font-size: 70px;
    margin-bottom: 10px;
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.hero-section h1 {
    font-size: 42px;
    margin-bottom: 8px;
    background: linear-gradient(135deg, #ffd700, #ffaa00);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.hero-section p {
    color: var(--text-dim);
}

.my-rank-card {
    display: flex;
    align-items: center;
    gap: 15px;
    background: linear-gradient(135deg, var(--primary), #00cc7a);
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 30px;
    color: #000;
}

.my-rank-card .rank-position {
    font-size: 32px;
    font-weight: bold;
}

.my-rank-card .rank-info {
    flex: 1;
}

.my-rank-card .rank-name {
    font-size: 18px;
    font-weight: bold;
    display: block;
}

.my-rank-card .rank-stats {
    display: flex;
    gap: 12px;
    font-size: 13px;
    margin-top: 5px;
}

.my-rank-card .rank-score {
    font-size: 24px;
    font-weight: bold;
}

.top-3 {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 30px;
}

.top-card {
    flex: 1;
    max-width: 160px;
    background: var(--bg-light);
    border: 1px solid #333;
    border-radius: 16px;
    padding: 20px;
    text-align: center;
    animation: slideUp 0.5s ease-out backwards;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.top-card:first-child {
    border-color: #ffd700;
    transform: scale(1.05);
}

.top-card:first-child .top-medal {
    font-size: 40px;
}

.top-medal {
    font-size: 32px;
    margin-bottom: 10px;
}

.top-name {
    font-weight: 600;
    font-size: 14px;
    margin-bottom: 5px;
}

.top-stats {
    font-size: 12px;
    color: var(--text-dim);
}

.ranking-list {
    background: var(--bg-light);
    border: 1px solid #222;
    border-radius: 16px;
    overflow: hidden;
}

.list-header {
    display: grid;
    grid-template-columns: 60px 1fr 80px;
    padding: 15px 20px;
    background: var(--bg-lighter);
    font-size: 12px;
    color: var(--text-dim);
    font-weight: 600;
    text-transform: uppercase;
}

.ranking-row {
    display: grid;
    grid-template-columns: 60px 1fr 80px;
    padding: 15px 20px;
    border-bottom: 1px solid #222;
    align-items: center;
    transition: all 0.3s;
    animation: slideIn 0.4s ease-out backwards;
}

@keyframes slideIn {
    from { opacity: 0; transform: translateX(-10px); }
    to { opacity: 1; transform: translateX(0); }
}

.ranking-row:hover {
    background: var(--bg-lighter);
}

.ranking-row.mine {
    background: rgba(0,255,159,0.1);
    border-left: 3px solid var(--primary);
}

.ranking-row .pos {
    color: var(--text-dim);
}

.ranking-row .name {
    font-weight: 500;
}

.ranking-row .score {
    color: var(--warning);
    font-weight: 600;
}

@media (max-width: 480px) {
    .top-3 {
        flex-direction: column;
        align-items: center;
    }
    
    .top-card {
        max-width: 100%;
    }
}
</style>