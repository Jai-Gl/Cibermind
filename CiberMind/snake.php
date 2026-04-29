<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();
$hoy = date("Y-m-d");
$ayer = date("Y-m-d", strtotime("-1 day"));
$racha = ($user["ultima_fecha"] == $hoy || $user["ultima_fecha"] == $ayer) ? $user["racha"] : 0;

$cyber_facts = [
    ["concept" => "🛡️ FIREWALL", "fact" => "Un firewall filtra el tráfico de red, actuando como barrera entre redes confiables y no confiables."],
    ["concept" => "🔐 ENCRIPTACIÓN", "fact" => "La encriptación convierte datos legibles en código ilegible para proteger la información."],
    ["concept" => "🎣 PHISHING", "fact" => "El phishing usa correos fraudulentos para engañar y robar información confidencial."],
    ["concept" => "🔑 CONTRASEÑA", "fact" => "Usa contraseñas largas con mayúsculas, números y símbolos. Mínimo 12 caracteres."],
    ["concept" => "🦠 MALWARE", "fact" => "El malware incluye virus, gusanos y troyanos que pueden dañar tu sistema."],
    ["concept" => "🌐 HTTPS", "fact" => "HTTPS usa TLS/SSL para cifrar la comunicación entre tu navegador y el servidor."],
    ["concept" => "🔍 DNS", "fact" => "DNS traduce nombres de dominio (google.com) a direcciones IP numéricas."],
    ["concept" => "📧 SPAM", "fact" => "El correo spam representa el 45% de todos los correos electrónicos enviados."],
    ["concept" => "💻 VIRUS", "fact" => "Un virus necesita un archivo host para propagarse, a diferencia de los gusanos."],
    ["concept" => "🔓 EXPLOIT", "fact" => "Un exploit es un código que aprovecha una vulnerabilidad para acceder a un sistema."],
    ["concept" => "🎭 SPOOFING", "fact" => "El spoofing suplanta la identidad de alguien para engañar a las víctimas."],
    ["concept" => "🐍 TROYANO", "fact" => "Los troyanos se disfrazan de software legítimo pero contienen código malicioso."],
    ["concept" => "🔍 SCAN", "fact" => "Nmap es la herramienta más popular para escanear puertos y descubrir redes."],
    ["concept" => "📊 LOGS", "fact" => "Los logs registran eventos del sistema y son esenciales para detectar intrusiones."],
    ["concept" => "🔑 CIFRADO", "fact" => "AES-256 es el estándar de cifrado avanzado más utilizado actualmente."],
    ["concept" => "🌐 PROXY", "fact" => "Un proxy actúa como intermediario entre el usuario y el servidor web."],
    ["concept" => "🦺 HTTPS", "fact" => "El candado verde en el navegador indica que la conexión es segura con HTTPS."],
    ["concept" => "💾 BACKUP", "fact" => "La regla 3-2-1: 3 copias, 2 medios diferentes, 1 fuera del sitio."],
    ["concept" => "🔐 MFA", "fact" => "El MFA (Multi-Factor) usa múltiples métodos: algo que sabes, tienes y eres."],
    ["concept" => "🚨 IDS/IPS", "fact" => "IDS detecta amenazas, IPS las detecta y bloquea en tiempo real."]
];

shuffle($cyber_facts);
$facts_json = json_encode($cyber_facts);
?>

<!DOCTYPE html>
<html>
<head>
<title>Snake - CiberMind</title>
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
            <span class="game-icon-sm">🐍</span>
            <h1>Snake Cyber</h1>
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
                <span>Usa las flechas para mover la serpiente</span>
            </div>
            <div class="step">
                <span class="step-num">2</span>
                <span>Come los datos para aprender conceptos</span>
            </div>
            <div class="step">
                <span class="step-num">3</span>
                <span>Lee los datos: ¡son curiosidades de ciberseguridad!</span>
            </div>
        </div>
    </div>

    <!-- Score Display -->
    <div class="score-display-row mb-4">
        <div class="score-box">
            <span class="score-label">Puntos</span>
            <span class="score-value" id="puntos">0</span>
        </div>
        <div class="score-box">
            <span class="score-label">Datos</span>
            <span class="score-value" id="comida">0</span>
        </div>
        <div class="score-box highlight">
            <span class="score-label">Leídos</span>
            <span class="score-value" id="leidos">0</span>
        </div>
    </div>

    <!-- Current Fact Display -->
    <div class="fact-display mb-4" id="factDisplay">
        <div class="fact-header">
            <span class="fact-icon">📚</span>
            <span class="fact-title">Dato del momento</span>
        </div>
        <p class="fact-text" id="factText">Come los datos brillantes para aprender sobre ciberseguridad</p>
    </div>

    <!-- Game Canvas -->
    <div class="canvas-container mb-4">
        <canvas id="game" width="380" height="380"></canvas>
        
        <!-- Start Overlay -->
        <div class="overlay" id="startOverlay">
            <div class="overlay-content">
                <div class="overlay-icon">🐍</div>
                <h3>Snake Cyber</h3>
                <p>Recolecta datos y aprende ciberseguridad</p>
                <button class="btn-start" onclick="iniciarJuego()">
                    <span>▶</span> INICIAR
                </button>
            </div>
        </div>
        
        <!-- Game Over Overlay -->
        <div class="overlay game-over" id="gameOver" style="display: none;">
            <div class="overlay-content">
                <div class="overlay-icon">💀</div>
                <h3>Game Over</h3>
                <div class="final-score">
                    <span>Datos leídos:</span>
                    <strong id="final">0</strong>
                </div>
                <div class="learned-summary" id="learnedSummary"></div>
                <button class="btn-restart" onclick="iniciarJuego()">
                    🔄 Jugar de nuevo
                </button>
            </div>
        </div>
    </div>

    <!-- Fact History -->
    <div class="fact-history mb-4" id="factHistory">
        <div class="history-header">
            <span class="history-icon">📋</span>
            <span>Conceptos aprendidos</span>
        </div>
        <div class="history-list" id="historyList">
            <p class="history-empty">Aún no has recogido ningún dato...</p>
        </div>
    </div>

    <!-- Mobile Controls -->
    <div class="mobile-controls mb-4">
        <div class="control-row">
            <button class="control-btn" onclick="setDirection(0, -1)">▲</button>
        </div>
        <div class="control-row">
            <button class="control-btn" onclick="setDirection(-1, 0)">◀</button>
            <button class="control-btn" onclick="togglePause()">⏸</button>
            <button class="control-btn" onclick="setDirection(1, 0)">▶</button>
        </div>
        <div class="control-row">
            <button class="control-btn" onclick="setDirection(0, 1)">▼</button>
        </div>
    </div>

    <!-- Keyboard Hint -->
    <div class="keyboard-hint mb-3">
        <span class="hint-icon">⌨️</span>
        <span>Usa las teclas de flechas ↑↓←→ en tu teclado</span>
    </div>

    <!-- Back -->
    <div class="text-center">
        <a href="menu.php" class="btn btn-ghost-outline">
            ← Volver al Menú
        </a>
    </div>

</div>

<script>
const canvas = document.getElementById('game');
const ctx = canvas.getContext('2d');
const size = 20;
const cols = canvas.width / size;
const rows = canvas.height / size;

const facts = <?= $facts_json ?>;
let currentFactIndex = 0;
let learnedFacts = [];

let snake = [];
let comida = {
    x: Math.floor(Math.random() * cols),
    y: Math.floor(Math.random() * rows),
    concept: "🛡️",
    fact: "Come los datos brillantes para aprender sobre ciberseguridad"
};
let puntos = 0;
let comidaCount = 0;
let leidosCount = 0;
let direccion = {x: 1, y: 0};
let siguienteDireccion = {x: 1, y: 0};
let juegoActivo = false;
let paused = false;
let speed = 120;
let showingFact = false;
let factDisplayTimeout = null;

function playSound(type) {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    
    if(type === 'eat') {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.frequency.setValueAtTime(800, audioCtx.currentTime);
        osc.frequency.exponentialRampToValueAtTime(1200, audioCtx.currentTime + 0.1);
        gain.gain.setValueAtTime(0.1, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.15);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.15);
    } else if(type === 'learn') {
        [600, 800, 1000].forEach((freq, i) => {
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
    } else if(type === 'die') {
        [400, 300, 200, 100].forEach((freq, i) => {
            const o = audioCtx.createOscillator();
            const g = audioCtx.createGain();
            o.connect(g);
            g.connect(audioCtx.destination);
            o.frequency.setValueAtTime(freq, audioCtx.currentTime + i * 0.1);
            g.gain.setValueAtTime(0.08, audioCtx.currentTime + i * 0.1);
            g.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + i * 0.1 + 0.15);
            o.start(audioCtx.currentTime + i * 0.1);
            o.stop(audioCtx.currentTime + i * 0.1 + 0.15);
        });
    }
}

function createParticles(x, y) {
    const colors = ['#bf00ff', '#ff00ff', '#00ffff', '#ffff00', '#00ff9f'];
    for(let i = 0; i < 12; i++) {
        const p = document.createElement('div');
        p.className = 'particle-burst';
        p.style.cssText = `
            left: ${x}px;
            top: ${y}px;
            background: ${colors[i % colors.length]};
            --tx: ${(Math.random() - 0.5) * 100}px;
            --ty: ${(Math.random() - 0.5) * 100}px;
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

function getNextFact() {
    const fact = facts[currentFactIndex];
    currentFactIndex = (currentFactIndex + 1) % facts.length;
    
    let x, y;
    let attempts = 0;
    do {
        x = Math.floor(Math.random() * cols);
        y = Math.floor(Math.random() * rows);
        attempts++;
    } while(snake.length > 0 && snake.some(parte => parte.x === x && parte.y === y) && attempts < 50);
    
    return { 
        x: x, 
        y: y, 
        concept: fact.concept, 
        fact: fact.fact 
    };
}

function showFact(fact) {
    const factDisplay = document.getElementById('factDisplay');
    const factText = document.getElementById('factText');
    const historyList = document.getElementById('historyList');
    
    factDisplay.classList.add('show');
    factText.innerHTML = `<strong>${fact.concept}</strong><br>${fact.fact}`;
    
    if(factDisplayTimeout) clearTimeout(factDisplayTimeout);
    factDisplayTimeout = setTimeout(() => {
        factDisplay.classList.remove('show');
    }, 5000);
    
    if(learnedFacts.length < 5) {
        learnedFacts.push(fact);
    }
    
    if(learnedFacts.length > 0) {
        historyList.innerHTML = learnedFacts.map(f => 
            `<div class="history-item"><span>${f.concept}</span><span>${f.fact.substring(0, 50)}...</span></div>`
        ).join('');
    }
}

function iniciarJuego() {
    snake = [{x: 8, y: 8}, {x: 7, y: 8}, {x: 6, y: 8}];
    puntos = 0;
    comidaCount = 0;
    leidosCount = 0;
    direccion = {x: 1, y: 0};
    siguienteDireccion = {x: 1, y: 0};
    speed = 120;
    paused = false;
    currentFactIndex = 0;
    learnedFacts = [];
    
    comida = getNextFact();
    
    document.getElementById('startOverlay').style.display = 'none';
    document.getElementById('gameOver').style.display = 'none';
    document.getElementById('puntos').innerText = '0';
    document.getElementById('comida').innerText = '0';
    document.getElementById('leidos').innerText = '0';
    document.getElementById('factText').innerHTML = 'Come los datos brillantes para aprender sobre ciberseguridad';
    document.getElementById('historyList').innerHTML = '<p class="history-empty">Aún no has recogido ningún dato...</p>';
    
    juegoActivo = true;
    loop();
}

function togglePause() {
    if(!juegoActivo) return;
    paused = !paused;
    if(!paused) loop();
}

function setDirection(x, y) {
    if(!juegoActivo || paused) return;
    if(direccion.x === -x && direccion.y === -y) return;
    if(direccion.x === x && direccion.y === y) return;
    siguienteDireccion = {x: x, y: y};
}

function loop() {
    if(!juegoActivo || paused) return;
    
    direccion = siguienteDireccion;
    const cabeza = {x: snake[0].x + direccion.x, y: snake[0].y + direccion.y};
    
    if(cabeza.x < 0 || cabeza.x >= cols || cabeza.y < 0 || cabeza.y >= rows) {
        gameOver();
        return;
    }
    
    for(let parte of snake) {
        if(cabeza.x === parte.x && cabeza.y === parte.y) {
            gameOver();
            return;
        }
    }
    
    snake.unshift(cabeza);
    
    if(cabeza.x === comida.x && cabeza.y === comida.y) {
        puntos += 10;
        comidaCount++;
        leidosCount++;
        playSound('eat');
        playSound('learn');
        
        showFact(comida);
        
        // Guardar concepto aprendido
        fetch('guardar_concepto.php?concepto=' + encodeURIComponent(comida.concept));
        
        const rect = canvas.getBoundingClientRect();
        createParticles(rect.left + comida.x * size + size/2, rect.top + comida.y * size + size/2);
        
        if(comidaCount % 3 === 0 && speed > 60) speed -= 5;
        
        document.getElementById('puntos').innerText = puntos;
        document.getElementById('comida').innerText = comidaCount;
        document.getElementById('leidos').innerText = leidosCount;
        
        comida = getNextFact();
    } else {
        snake.pop();
    }
    
    draw();
    setTimeout(loop, speed);
}

function draw() {
    ctx.fillStyle = '#0a0510';
    ctx.fillRect(0, 0, canvas.width, canvas.height);
    
    ctx.strokeStyle = 'rgba(191, 0, 255, 0.08)';
    ctx.lineWidth = 0.5;
    for(let i = 0; i < cols; i++) {
        ctx.beginPath();
        ctx.moveTo(i * size, 0);
        ctx.lineTo(i * size, canvas.height);
        ctx.stroke();
        ctx.beginPath();
        ctx.moveTo(0, i * size);
        ctx.lineTo(canvas.width, i * size);
        ctx.stroke();
    }
    
    snake.forEach((parte, i) => {
        const alpha = 1 - (i / snake.length) * 0.6;
        const isHead = i === 0;
        
        if(isHead) {
            ctx.shadowColor = '#ff00ff';
            ctx.shadowBlur = 15;
            ctx.fillStyle = '#ff00ff';
        } else {
            ctx.shadowBlur = 0;
            ctx.fillStyle = `rgba(191, 0, 255, ${alpha})`;
        }
        
        ctx.beginPath();
        ctx.roundRect(
            parte.x * size + 2, 
            parte.y * size + 2, 
            size - 4, 
            size - 4, 
            6
        );
        ctx.fill();
    });
    ctx.shadowBlur = 0;
    
    const pulseSize = 2 + Math.sin(Date.now() / 200) * 1;
    ctx.shadowColor = '#00ffff';
    ctx.shadowBlur = 25;
    ctx.fillStyle = '#00ffff';
    ctx.beginPath();
    ctx.arc(
        comida.x * size + size/2, 
        comida.y * size + size/2, 
        size/2 - 3 + pulseSize, 
        0, 
        Math.PI * 2
    );
    ctx.fill();
    
    ctx.shadowBlur = 0;
    ctx.fillStyle = '#fff';
    ctx.font = 'bold 9px Arial';
    ctx.textAlign = 'center';
    ctx.textBaseline = 'middle';
    
    if(comida.concept) {
        const icon = comida.concept.split(' ')[0];
        ctx.fillText(icon, comida.x * size + size/2, comida.y * size + size/2);
    }
}

function gameOver() {
    juegoActivo = false;
    playSound('die');
    
    document.getElementById('final').innerText = leidosCount;
    
    const summary = document.getElementById('learnedSummary');
    if(learnedFacts.length > 0) {
        summary.innerHTML = '<div class="learned-list">' + 
            learnedFacts.map(f => `<div class="learned-item">${f.concept}</div>`).join('') + 
            '</div>';
    } else {
        summary.innerHTML = '';
    }
    
    document.getElementById('gameOver').style.display = 'flex';
    
    if(puntos > 0) {
        fetch('guardar_snake.php?puntos=' + puntos + '&datos=' + leidosCount);
        fetch('actualizar_mision.php?tipo=snake');
        fetch('actualizar_mision.php?tipo=conceptos');
        fetch('actualizar_mision.php?tipo=juego_total');
    }
}

document.addEventListener('keydown', e => {
    if(e.key === 'ArrowUp') { e.preventDefault(); setDirection(0, -1); }
    if(e.key === 'ArrowDown') { e.preventDefault(); setDirection(0, 1); }
    if(e.key === 'ArrowLeft') { e.preventDefault(); setDirection(-1, 0); }
    if(e.key === 'ArrowRight') { e.preventDefault(); setDirection(1, 0); }
    if(e.key === ' ' || e.key === 'Escape') { e.preventDefault(); togglePause(); }
});

draw();
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
    border: 1px solid rgba(0, 255, 159, 0.2);
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
    color: #00ff9f;
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
    background: linear-gradient(135deg, #00ff9f, #00cc7a);
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
   FACT DISPLAY
   ============================================= */
.fact-display {
    background: linear-gradient(135deg, rgba(0, 255, 255, 0.1), rgba(0, 255, 255, 0.02));
    border: 2px solid rgba(0, 255, 255, 0.3);
    border-radius: 16px;
    padding: 20px;
    transform: translateY(-20px);
    opacity: 0;
    transition: all 0.5s ease-out;
    max-height: 0;
    overflow: hidden;
    margin-bottom: 0;
}

.fact-display.show {
    transform: translateY(0);
    opacity: 1;
    max-height: 200px;
    margin-bottom: 20px;
}

.fact-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 10px;
}

.fact-icon {
    font-size: 1.5rem;
}

.fact-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.9rem;
    color: #00ffff;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.fact-text {
    color: #fff;
    line-height: 1.6;
    font-size: 1rem;
}

.fact-text strong {
    color: #00ffff;
    font-size: 1.1rem;
    display: block;
    margin-bottom: 5px;
}

/* =============================================
   SCORE DISPLAY
   ============================================= */
.score-display-row {
    display: flex;
    justify-content: center;
    gap: 20px;
}

.score-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: linear-gradient(135deg, rgba(18, 11, 26, 0.9), rgba(13, 10, 21, 0.95));
    border: 1px solid rgba(191, 0, 255, 0.2);
    border-radius: 16px;
    padding: 20px 35px;
    transition: all 0.3s;
}

.score-box.highlight {
    border-color: rgba(0, 255, 255, 0.5);
    background: linear-gradient(135deg, rgba(0, 255, 255, 0.1), rgba(0, 255, 255, 0.02));
}

.score-label {
    font-size: 0.85rem;
    color: #666;
    margin-bottom: 8px;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.score-value {
    font-family: 'Orbitron', sans-serif;
    font-size: 2.2rem;
    font-weight: 900;
    color: #bf00ff;
}

.score-box.highlight .score-value {
    color: #00ffff;
}

/* =============================================
   CANVAS
   ============================================= */
.canvas-container {
    position: relative;
    display: flex;
    justify-content: center;
}

canvas {
    background: #0a0510;
    border: 3px solid #bf00ff;
    border-radius: 16px;
    box-shadow: 0 0 40px rgba(191, 0, 255, 0.4);
}

.overlay {
    position: absolute;
    inset: 3px;
    background: rgba(8, 2, 10, 0.95);
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: fadeIn 0.3s ease-out;
}

.overlay-content {
    text-align: center;
    max-width: 90%;
}

.overlay-icon {
    font-size: 4rem;
    margin-bottom: 15px;
}

.game-over .overlay-icon {
    animation: shake 0.5s ease-out;
}

.overlay-content h3 {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.8rem;
    color: #fff;
    margin-bottom: 10px;
}

.overlay-content p {
    color: #888;
    margin-bottom: 25px;
}

.final-score {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    margin-bottom: 15px;
    color: #888;
}

.final-score strong {
    font-family: 'Orbitron', sans-serif;
    font-size: 2.5rem;
    color: #00ffff;
}

.learned-summary {
    margin-bottom: 20px;
}

.learned-list {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 8px;
    margin-top: 10px;
}

.learned-item {
    background: rgba(0, 255, 255, 0.2);
    border: 1px solid rgba(0, 255, 255, 0.4);
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 0.8rem;
    color: #00ffff;
}

.btn-start {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #00ff9f, #00cc7a);
    border: none;
    border-radius: 12px;
    padding: 15px 40px;
    font-family: 'Orbitron', sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: #000;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-start:hover {
    transform: scale(1.05);
    box-shadow: 0 0 30px rgba(0, 255, 159, 0.5);
}

.btn-restart {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #bf00ff, #8b00ff);
    border: none;
    border-radius: 12px;
    padding: 15px 40px;
    font-family: 'Orbitron', sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-restart:hover {
    transform: scale(1.05);
    box-shadow: 0 0 30px rgba(191, 0, 255, 0.5);
}

/* =============================================
   FACT HISTORY
   ============================================= */
.fact-history {
    background: linear-gradient(135deg, rgba(18, 11, 26, 0.9), rgba(13, 10, 21, 0.95));
    border: 1px solid rgba(191, 0, 255, 0.2);
    border-radius: 16px;
    padding: 20px;
}

.history-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(191, 0, 255, 0.15);
}

.history-icon {
    font-size: 1.3rem;
}

.history-header span:last-child {
    font-family: 'Orbitron', sans-serif;
    font-size: 0.9rem;
    color: #bf00ff;
}

.history-list {
    max-height: 150px;
    overflow-y: auto;
}

.history-empty {
    color: #666;
    font-style: italic;
    text-align: center;
}

.history-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 8px 12px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 8px;
    margin-bottom: 8px;
}

.history-item span:first-child {
    font-size: 0.85rem;
    color: #00ffff;
    font-weight: 600;
}

.history-item span:last-child {
    font-size: 0.8rem;
    color: #888;
}

/* =============================================
   MOBILE CONTROLS
   ============================================= */
.mobile-controls {
    display: none;
    flex-direction: column;
    align-items: center;
    gap: 10px;
}

@media (max-width: 768px) {
    .mobile-controls {
        display: flex;
    }
}

.control-row {
    display: flex;
    gap: 10px;
}

.control-btn {
    width: 65px;
    height: 65px;
    background: linear-gradient(135deg, rgba(18, 11, 26, 0.9), rgba(24, 16, 31, 0.95));
    border: 2px solid rgba(191, 0, 255, 0.3);
    border-radius: 14px;
    color: #fff;
    font-size: 1.6rem;
    cursor: pointer;
    transition: all 0.2s;
    display: flex;
    align-items: center;
    justify-content: center;
}

.control-btn:active {
    background: linear-gradient(135deg, #bf00ff, #8b00ff);
    transform: scale(0.95);
}

/* =============================================
   KEYBOARD HINT
   ============================================= */
.keyboard-hint {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    color: #666;
    font-size: 0.9rem;
}

.hint-icon {
    font-size: 1.3rem;
}

@media (max-width: 768px) {
    .keyboard-hint {
        display: none;
    }
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
    100% { transform: translate(var(--tx), var(--ty)) scale(0); opacity: 0; }
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
   ANIMATIONS
   ============================================= */
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px) rotate(-5deg); }
    75% { transform: translateX(10px) rotate(5deg); }
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
    
    canvas {
        width: 320px;
        height: 320px;
    }
    
    .score-display-row {
        gap: 10px;
    }
    
    .score-box {
        padding: 15px 20px;
    }
    
    .score-value {
        font-size: 1.6rem;
    }
}

@media (max-width: 480px) {
    .game-title-simple h1 {
        font-size: 1.4rem;
    }
    
    canvas {
        width: 280px;
        height: 280px;
    }
}
</style>

</body>
</html>