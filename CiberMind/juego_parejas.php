<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();
$hoy = date("Y-m-d");
$ayer = date("Y-m-d", strtotime("-1 day"));
$racha = ($user["ultima_fecha"] == $hoy || $user["ultima_fecha"] == $ayer) ? $user["racha"] : 0;

$tarjetas = $conn->query("SELECT * FROM tarjetas ORDER BY RAND() LIMIT 8")->fetch_all(MYSQLI_ASSOC);

$pares = [];
foreach($tarjetas as $t) {
    $pares[] = ["id" => $t["id"], "texto" => $t["concepto"], "tipo" => "concepto"];
    $pares[] = ["id" => $t["id"], "texto" => $t["definicion"], "tipo" => "definicion"];
}
shuffle($pares);

$_SESSION["pares"] = $pares;
$_SESSION["aciertos"] = 0;
$_SESSION["intentos"] = 0;

$pares_json = json_encode(array_column($pares, 'id'));
$total_pares = count($pares) / 2;
?>

<!DOCTYPE html>
<html>
<head>
<title>Parejas - CiberMind</title>
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="particles-bg" id="particles"></div>
<div class="grid-bg"></div>

<div class="container">
    
    <!-- Game Header -->
    <header class="game-header">
        <a href="menu.php" class="back-btn">←</a>
        <div class="game-title-simple">
            <span class="game-icon-sm">🔐</span>
            <h1>Parejas de Seguridad</h1>
        </div>
        <div class="game-stats-mini">
            <span class="mini-stat" title="Nivel">📊 <?= $user["nivel"] ?></span>
            <span class="mini-stat" title="Vidas">❤️ <?= $user["vidas"] ?></span>
            <span class="mini-stat" title="Racha">🔥 <?= $racha ?></span>
        </div>
    </header>

    <!-- How to Play -->
    <div class="howto-box mb-4">
        <div class="howto-header">
            <span class="howto-icon">📖</span>
            <span class="howto-title">¿Cómo Jugar?</span>
        </div>
        <div class="howto-steps">
            <div class="step">
                <span class="step-num">1</span>
                <span>Toca una carta para revelarla</span>
            </div>
            <div class="step">
                <span class="step-num">2</span>
                <span>Empareja conceptos de ciberseguridad</span>
            </div>
            <div class="step">
                <span class="step-num">3</span>
                <span>Encuentra todos los <?= $total_pares ?> pares</span>
            </div>
        </div>
    </div>

    <!-- Game Timer & Stats -->
    <div class="game-info-bar mb-4">
        <div class="timer-display-large">
            <div class="timer-ring" id="timerRing">
                <span id="timer">60</span>
            </div>
            <span class="timer-label">Segundos</span>
        </div>
        <div class="game-progress">
            <div class="progress-label">
                <span>Pares encontrados</span>
                <span><span id="aciertos">0</span>/<?= $total_pares ?></span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill"></div>
            </div>
        </div>
        <div class="attempts-display">
            <span class="attempts-number" id="intentos">0</span>
            <span class="attempts-label">Intentos</span>
        </div>
    </div>

    <!-- Memory Game Board -->
    <div class="memory-board mb-4">
        <?php foreach($pares as $i => $p): ?>
        <div class="memory-card" data-id="<?= $p["id"] ?>" data-tipo="<?= $p["tipo"] ?>" 
             onclick="voltear(this)" style="animation-delay: <?= $i * 0.05 ?>s">
            <div class="card-inner">
                <div class="card-front">
                    <span class="card-question">?</span>
                </div>
                <div class="card-back <?= $p["tipo"] ?>">
                    <span class="card-type"><?= $p["tipo"] == "concepto" ? "📌" : "📝" ?></span>
                    <span class="card-text"><?= htmlspecialchars($p["texto"]) ?></span>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Legend -->
    <div class="legend-box mb-4">
        <div class="legend-item">
            <span class="legend-color concepto"></span>
            <span>Concepto</span>
        </div>
        <div class="legend-item">
            <span class="legend-color definicion"></span>
            <span>Definición</span>
        </div>
    </div>

    <!-- Back Button -->
    <div class="text-center">
        <a href="menu.php" class="btn btn-ghost-outline">
            ← Volver al Menú
        </a>
    </div>

</div>

<script>
let primeraCarta = null;
let segundaCarta = null;
let bloqueado = false;
let intentos = 0;
let aciertos = 0;
const totalParejas = <?= $total_pares ?>;

function playSound(type) {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    
    if(type === 'flip') {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.frequency.setValueAtTime(600, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.05, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.1);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.1);
    } else if(type === 'match') {
        [523, 659, 784].forEach((freq, i) => {
            const o = audioCtx.createOscillator();
            const g = audioCtx.createGain();
            o.connect(g);
            g.connect(audioCtx.destination);
            o.frequency.setValueAtTime(freq, audioCtx.currentTime + i * 0.1);
            g.gain.setValueAtTime(0.08, audioCtx.currentTime + i * 0.1);
            g.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + i * 0.1 + 0.2);
            o.start(audioCtx.currentTime + i * 0.1);
            o.stop(audioCtx.currentTime + i * 0.1 + 0.2);
        });
    } else if(type === 'nomatch') {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.frequency.setValueAtTime(200, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.15);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.15);
    } else if(type === 'win') {
        [523, 659, 784, 1047, 1318].forEach((freq, i) => {
            const o = audioCtx.createOscillator();
            const g = audioCtx.createGain();
            o.connect(g);
            g.connect(audioCtx.destination);
            o.frequency.setValueAtTime(freq, audioCtx.currentTime + i * 0.1);
            g.gain.setValueAtTime(0.1, audioCtx.currentTime + i * 0.1);
            g.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + i * 0.1 + 0.3);
            o.start(audioCtx.currentTime + i * 0.1);
            o.stop(audioCtx.currentTime + i * 0.1 + 0.3);
        });
    }
}

function createParticles(x, y) {
    const colors = ['#bf00ff', '#00d4ff', '#00ff9f', '#ffd700'];
    for(let i = 0; i < 12; i++) {
        const p = document.createElement('div');
        p.className = 'particle-burst';
        p.style.cssText = `
            left: ${x}px;
            top: ${y}px;
            background: ${colors[i % colors.length]};
        `;
        document.body.appendChild(p);
        setTimeout(() => p.remove(), 600);
    }
}

function createParticles() {
    const container = document.getElementById('particles');
    const colors = ['#bf00ff', '#00d4ff', '#00ff9f', '#ffd700'];
    
    for(let i = 0; i < 15; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.cssText = `
            left: ${Math.random() * 100}%;
            top: ${Math.random() * 100}%;
            background: ${colors[Math.floor(Math.random() * colors.length)]};
            animation-delay: ${Math.random() * 5}s;
        `;
        container.appendChild(particle);
    }
}

createParticles();

function voltear(card) {
    if(bloqueado) return;
    if(card.classList.contains('flipped') || card.classList.contains('matched')) return;
    
    card.classList.add('flipped');
    playSound('flip');
    
    if(primeraCarta === null) {
        primeraCarta = card;
    } else {
        segundaCarta = card;
        bloqueado = true;
        
        intentos++;
        document.getElementById('intentos').innerText = intentos;
        
        if(primeraCarta.dataset.id === segundaCarta.dataset.id) {
            primeraCarta.classList.add('matched');
            segundaCarta.classList.add('matched');
            
            aciertos++;
            document.getElementById('aciertos').innerText = aciertos;
            
            const progress = (aciertos / totalParejas) * 100;
            document.getElementById('progressFill').style.width = progress + '%';
            
            playSound('match');
            
            const rect = primeraCarta.getBoundingClientRect();
            createParticles(rect.left + rect.width/2, rect.top + rect.height/2);
            
            primeraCarta = null;
            segundaCarta = null;
            bloqueado = false;
            
            if(aciertos === totalParejas) {
                playSound('win');
                setTimeout(() => {
                    location.href = 'verificar_parejas.php?aciertos=' + aciertos + '&intentos=' + intentos;
                }, 800);
            }
        } else {
            setTimeout(() => {
                playSound('nomatch');
                primeraCarta.classList.remove('flipped');
                segundaCarta.classList.remove('flipped');
                primeraCarta = null;
                segundaCarta = null;
                bloqueado = false;
            }, 1000);
        }
    }
}

let tiempo = 60;
setInterval(() => {
    tiempo--;
    const timerEl = document.getElementById('timer');
    const ringEl = document.getElementById('timerRing');
    
    timerEl.innerText = Math.max(0, tiempo);
    
    if(tiempo <= 5) {
        ringEl.style.borderColor = '#ff4444';
        ringEl.style.color = '#ff4444';
        timerEl.style.color = '#ff4444';
    } else if(tiempo <= 10) {
        ringEl.style.borderColor = '#ff8800';
        ringEl.style.color = '#ff8800';
        timerEl.style.color = '#ff8800';
    }
    
    if(tiempo <= 0) location.href = 'verificar_parejas.php?tiempo=0';
}, 1000);
</script>

<style>
/* =============================================
   GAME HEADER
   ============================================= */
.game-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 0;
    margin-bottom: 30px;
}

.back-btn {
    width: 50px;
    height: 50px;
    background: rgba(18, 11, 26, 0.8);
    border: 1px solid rgba(191, 0, 255, 0.3);
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    color: #bf00ff;
    transition: all 0.3s;
}

.back-btn:hover {
    background: rgba(191, 0, 255, 0.2);
    transform: translateX(-5px);
}

.game-title-simple {
    display: flex;
    align-items: center;
    gap: 15px;
}

.game-icon-sm {
    font-size: 2.5rem;
}

.game-title-simple h1 {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.8rem;
    color: #fff;
}

.game-stats-mini {
    display: flex;
    gap: 10px;
}

.mini-stat {
    padding: 8px 14px;
    background: rgba(18, 11, 26, 0.8);
    border: 1px solid rgba(191, 0, 255, 0.2);
    border-radius: 10px;
    font-family: 'Rajdhani', sans-serif;
    font-size: 0.9rem;
}

/* =============================================
   HOW TO PLAY BOX
   ============================================= */
.howto-box {
    background: linear-gradient(135deg, rgba(18, 11, 26, 0.9), rgba(13, 10, 21, 0.95));
    border: 1px solid rgba(0, 212, 255, 0.2);
    border-radius: 16px;
    padding: 20px;
}

.howto-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 15px;
}

.howto-icon {
    font-size: 1.5rem;
}

.howto-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 1rem;
    color: #00d4ff;
}

.howto-steps {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.step {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
    min-width: 200px;
}

.step-num {
    width: 28px;
    height: 28px;
    background: linear-gradient(135deg, #bf00ff, #8b00ff);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Orbitron', sans-serif;
    font-size: 0.85rem;
    font-weight: 700;
}

.step span:last-child {
    color: #aaa;
    font-size: 0.9rem;
}

/* =============================================
   GAME INFO BAR
   ============================================= */
.game-info-bar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    background: linear-gradient(135deg, rgba(18, 11, 26, 0.9), rgba(13, 10, 21, 0.95));
    border: 1px solid rgba(191, 0, 255, 0.2);
    border-radius: 16px;
    padding: 20px;
    flex-wrap: wrap;
}

.timer-display-large {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.timer-ring {
    width: 80px;
    height: 80px;
    border: 4px solid #00d4ff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

#timer {
    font-family: 'Orbitron', sans-serif;
    font-size: 2rem;
    font-weight: 900;
    color: #00d4ff;
}

.timer-label {
    color: #666;
    font-size: 0.8rem;
    margin-top: 5px;
}

.game-progress {
    flex: 1;
    min-width: 200px;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-size: 0.9rem;
}

.progress-label span:first-child {
    color: #888;
}

.progress-label span:last-child {
    color: #00ff9f;
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
}

.progress-bar {
    height: 10px;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid rgba(191, 0, 255, 0.2);
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #00ff9f, #00d4ff);
    border-radius: 10px;
    transition: width 0.3s ease-out;
    box-shadow: 0 0 15px rgba(0, 255, 159, 0.5);
}

.attempts-display {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.attempts-number {
    font-family: 'Orbitron', sans-serif;
    font-size: 2rem;
    font-weight: 900;
    color: #ff8800;
}

.attempts-label {
    color: #666;
    font-size: 0.8rem;
    margin-top: 5px;
}

/* =============================================
   MEMORY BOARD
   ============================================= */
.memory-board {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
}

.memory-card {
    aspect-ratio: 1;
    perspective: 1000px;
    cursor: pointer;
    animation: slideUp 0.4s ease-out backwards;
}

.card-inner {
    position: relative;
    width: 100%;
    height: 100%;
    transition: transform 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    transform-style: preserve-3d;
}

.memory-card.flipped .card-inner,
.memory-card.matched .card-inner {
    transform: rotateY(180deg);
}

.card-front, .card-back {
    position: absolute;
    width: 100%;
    height: 100%;
    backface-visibility: hidden;
    border-radius: 12px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 8px;
    box-sizing: border-box;
}

.card-front {
    background: linear-gradient(135deg, #bf00ff, #8b00ff);
    border: 2px solid rgba(255, 255, 255, 0.2);
}

.card-question {
    font-size: 2rem;
    font-weight: 900;
    color: rgba(255, 255, 255, 0.7);
}

.card-back {
    background: var(--bg-card);
    border: 2px solid var(--primary);
    transform: rotateY(180deg);
}

.card-back.concepto {
    background: linear-gradient(135deg, rgba(191, 0, 255, 0.2), rgba(191, 0, 255, 0.05));
    border-color: #bf00ff;
}

.card-back.definicion {
    background: linear-gradient(135deg, rgba(0, 212, 255, 0.2), rgba(0, 212, 255, 0.05));
    border-color: #00d4ff;
}

.card-type {
    font-size: 1.2rem;
    margin-bottom: 5px;
}

.card-text {
    font-family: 'Rajdhani', sans-serif;
    font-size: 0.7rem;
    text-align: center;
    color: #fff;
    line-height: 1.3;
    word-break: break-word;
    overflow: hidden;
}

.memory-card:hover:not(.flipped):not(.matched) .card-inner {
    transform: scale(1.08);
}

.memory-card:hover:not(.flipped):not(.matched) .card-front {
    box-shadow: 0 0 25px rgba(191, 0, 255, 0.6);
}

.memory-card.matched .card-inner {
    animation: matchPulse 0.5s ease-out;
}

.memory-card.matched .card-back {
    background: rgba(0, 255, 159, 0.2);
    border-color: #00ff9f;
    box-shadow: 0 0 20px rgba(0, 255, 159, 0.4);
}

@keyframes matchPulse {
    0% { transform: rotateY(180deg) scale(1); }
    50% { transform: rotateY(180deg) scale(1.15); }
    100% { transform: rotateY(180deg) scale(1); }
}

/* =============================================
   LEGEND BOX
   ============================================= */
.legend-box {
    display: flex;
    justify-content: center;
    gap: 30px;
    padding: 15px;
    background: rgba(18, 11, 26, 0.5);
    border-radius: 12px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #888;
    font-size: 0.9rem;
}

.legend-color {
    width: 20px;
    height: 20px;
    border-radius: 6px;
    border: 2px solid;
}

.legend-color.concepto {
    background: rgba(191, 0, 255, 0.2);
    border-color: #bf00ff;
}

.legend-color.definicion {
    background: rgba(0, 212, 255, 0.2);
    border-color: #00d4ff;
}

/* =============================================
   BUTTONS
   ============================================= */
.btn.btn-ghost-outline {
    background: transparent;
    border: 1px solid rgba(191, 0, 255, 0.3);
    color: #bf00ff;
    padding: 12px 30px;
    border-radius: 12px;
    font-family: 'Rajdhani', sans-serif;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn.btn-ghost-outline:hover {
    background: rgba(191, 0, 255, 0.1);
    border-color: #bf00ff;
}

/* =============================================
   BACKGROUND
   ============================================= */
.particles-bg {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: -1;
    overflow: hidden;
}

.particle {
    position: absolute;
    width: 4px;
    height: 4px;
    border-radius: 50%;
    opacity: 0.5;
    animation: floatParticle 10s linear infinite;
}

@keyframes floatParticle {
    0% { transform: translateY(100vh); opacity: 0; }
    10% { opacity: 0.5; }
    90% { opacity: 0.5; }
    100% { transform: translateY(-100px); opacity: 0; }
}

.particle-burst {
    position: fixed;
    width: 8px;
    height: 8px;
    border-radius: 50%;
    pointer-events: none;
    animation: burst 0.6s ease-out forwards;
}

@keyframes burst {
    0% { transform: translate(0, 0) scale(1); opacity: 1; }
    100% { transform: translate(var(--tx, 50px), var(--ty, -50px)) scale(0); opacity: 0; }
}

/* =============================================
   RESPONSIVE
   ============================================= */
@media (max-width: 768px) {
    .game-header {
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .game-stats-mini {
        order: 3;
        width: 100%;
        justify-content: center;
    }
    
    .memory-board {
        grid-template-columns: repeat(4, 1fr);
        gap: 8px;
    }
    
    .game-info-bar {
        flex-direction: column;
    }
    
    .howto-steps {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .memory-board {
        grid-template-columns: repeat(4, 1fr);
        gap: 5px;
    }
    
    .card-text {
        font-size: 0.6rem;
    }
    
    .game-title-simple h1 {
        font-size: 1.4rem;
    }
}
</style>

</body>
</html>