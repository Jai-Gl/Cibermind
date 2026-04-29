<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();
$hoy = date("Y-m-d");
$ayer = date("Y-m-d", strtotime("-1 day"));
$racha = ($user["ultima_fecha"] == $hoy || $user["ultima_fecha"] == $ayer) ? $user["racha"] : 0;

$categorias = [
    "Frontend" => [
        ["nombre" => "HTML", "desc" => "Lenguaje de marcado"],
        ["nombre" => "CSS", "desc" => "Estilos visuales"],
        ["nombre" => "JavaScript", "desc" => "Interactividad web"],
        ["nombre" => "React", "desc" => "Framework UI"],
        ["nombre" => "Vue", "desc" => "Framework progresivo"]
    ],
    "Backend" => [
        ["nombre" => "PHP", "desc" => "Lenguaje del servidor"],
        ["nombre" => "Node.js", "desc" => "JS en servidor"],
        ["nombre" => "Python", "desc" => "Lenguaje versátil"],
        ["nombre" => "MySQL", "desc" => "Base de datos"],
        ["nombre" => "API", "desc" => "Interfaz de servicios"]
    ],
    "Herramientas" => [
        ["nombre" => "Git", "desc" => "Control de versiones"],
        ["nombre" => "Docker", "desc" => "Contenedores"],
        ["nombre" => "VS Code", "desc" => "Editor de código"],
        ["nombre" => "NPM", "desc" => "Gestor de paquetes"],
        ["nombre" => "Linux", "desc" => "Sistema operativo"]
    ]
];

$cats = array_keys($categorias);
$elementos = [];

foreach($categorias as $cat => $items) {
    foreach($items as $item) {
        $elementos[] = [
            "texto" => $item["nombre"],
            "desc" => $item["desc"],
            "categoria" => $cat
        ];
    }
}
shuffle($elementos);

$_SESSION["elementos"] = $elementos;
$_SESSION["cats"] = $cats;
?>

<!DOCTYPE html>
<html>
<head>
<title>Arrastrar - CiberMind</title>
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
            <span class="game-icon-sm">🏷️</span>
            <h1>Clasificar</h1>
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
                <span>Arrastra cada elemento a su categoría</span>
            </div>
            <div class="step">
                <span class="step-num">2</span>
                <span>Frontend, Backend o Herramientas</span>
            </div>
            <div class="step">
                <span class="step-num">3</span>
                <span>5 elementos por categoría</span>
            </div>
        </div>
    </div>

    <!-- Game Info Bar -->
    <div class="game-info-bar mb-4">
        <div class="timer-display-large">
            <div class="timer-ring" id="timerRing">
                <span id="timer">60</span>
            </div>
            <span class="timer-label">Segundos</span>
        </div>
        <div class="progress-section">
            <div class="progress-label">
                <span>Elementos restantes</span>
                <span><span id="remaining">15</span>/15</span>
            </div>
            <div class="progress-bar">
                <div class="progress-fill" id="progressFill" style="width: 0%"></div>
            </div>
        </div>
        <div class="score-display">
            <span class="score-number" id="correctos">0</span>
            <span class="score-label">Correctos</span>
        </div>
    </div>

    <!-- Elements Pool -->
    <div class="elements-pool-section mb-4">
        <div class="pool-header">
            <span class="pool-title">📦 Elementos Disponibles</span>
            <span class="pool-hint">Arrastra a las categorías de abajo</span>
        </div>
        <div class="elements-pool" id="elements">
            <?php foreach($elementos as $el): ?>
            <div class="draggable-item" draggable="true" data-cat="<?= $el["categoria"] ?>">
                <span class="drag-handle">⟷</span>
                <div class="draggable-content">
                    <span class="draggable-name"><?= htmlspecialchars($el["texto"]) ?></span>
                    <span class="draggable-desc"><?= htmlspecialchars($el["desc"]) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="categories-section mb-4">
        <div class="categories-grid">
            <?php foreach($cats as $i => $cat): ?>
            <?php
            $cat_colors = ["#bf00ff", "#00d4ff", "#00ff9f"];
            $cat_icons = ["🎨", "⚙️", "🛠️"];
            ?>
            <div class="category-box" data-nombre="<?= $cat ?>" style="--cat-color: <?= $cat_colors[$i] ?>">
                <div class="category-header">
                    <span class="cat-icon"><?= $cat_icons[$i] ?></span>
                    <span class="cat-name"><?= $cat ?></span>
                    <span class="cat-count" id="count-<?= $cat ?>">0/5</span>
                </div>
                <div class="category-items" id="items-<?= $cat ?>"></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Result -->
    <div id="resultado" class="resultado" style="display: none;"></div>

    <!-- Verify Button -->
    <button class="btn-verify" onclick="verificarArrastre()">
        <span class="verify-icon">✓</span>
        <span>VERIFICAR CLASIFICACIÓN</span>
    </button>

    <!-- Back -->
    <div class="text-center mt-4">
        <a href="menu.php" class="btn btn-ghost-outline">
            ← Volver al Menú
        </a>
    </div>

</div>

<script>
const categoryCounts = { "Frontend": 0, "Backend": 0, "Herramientas": 0 };
let draggedItem = null;

function playSound(type) {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    
    if(type === 'pick') {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.frequency.setValueAtTime(500, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.05, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.1);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.1);
    } else if(type === 'drop') {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.frequency.setValueAtTime(700, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.05, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.08);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.08);
    } else if(type === 'success') {
        [523, 659, 784, 1047].forEach((freq, i) => {
            const o = audioCtx.createOscillator();
            const g = audioCtx.createGain();
            o.connect(g);
            g.connect(audioCtx.destination);
            o.frequency.setValueAtTime(freq, audioCtx.currentTime + i * 0.1);
            g.gain.setValueAtTime(0.08, audioCtx.currentTime + i * 0.1);
            g.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + i * 0.1 + 0.25);
            o.start(audioCtx.currentTime + i * 0.1);
            o.stop(audioCtx.currentTime + i * 0.1 + 0.25);
        });
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

function updateProgress() {
    const remaining = document.querySelectorAll('.elements-pool .draggable-item').length;
    document.getElementById('remaining').innerText = remaining;
    const progress = ((15 - remaining) / 15) * 100;
    document.getElementById('progressFill').style.width = progress + '%';
}

document.querySelectorAll('.draggable-item').forEach(d => {
    d.addEventListener('dragstart', e => {
        draggedItem = d;
        d.classList.add('dragging');
        playSound('pick');
        setTimeout(() => d.style.opacity = '0.5', 0);
    });
    d.addEventListener('dragend', e => {
        d.classList.remove('dragging');
        d.style.opacity = '1';
        draggedItem = null;
    });
});

document.querySelectorAll('.category-box').forEach(cat => {
    cat.addEventListener('dragover', e => {
        e.preventDefault();
        cat.classList.add('drag-over');
    });
    cat.addEventListener('dragleave', e => {
        cat.classList.remove('drag-over');
    });
    cat.addEventListener('drop', e => {
        e.preventDefault();
        cat.classList.remove('drag-over');
        if(draggedItem) {
            const catName = cat.dataset.nombre;
            
            cat.querySelector('.category-items').appendChild(draggedItem);
            categoryCounts[catName]++;
            document.getElementById('count-' + catName).innerText = categoryCounts[catName] + '/5';
            
            updateProgress();
            playSound('drop');
        }
    });
});

const elementsZone = document.getElementById('elements');
elementsZone.addEventListener('dragover', e => e.preventDefault());
elementsZone.addEventListener('drop', e => {
    e.preventDefault();
    if(draggedItem) {
        const catName = draggedItem.dataset.cat;
        categoryCounts[catName]--;
        document.getElementById('count-' + catName).innerText = categoryCounts[catName] + '/5';
        
        elementsZone.appendChild(draggedItem);
        
        updateProgress();
        playSound('drop');
    }
});

function verificarArrastre() {
    let correctos = 0;
    
    document.querySelectorAll('.category-box').forEach(cat => {
        const catName = cat.dataset.nombre;
        const droppables = cat.querySelectorAll('.draggable-item');
        
        droppables.forEach(el => {
            if(el.dataset.cat === catName) {
                correctos++;
                el.classList.add('correct');
                el.classList.remove('incorrect');
            } else {
                el.classList.add('incorrect');
                el.classList.remove('correct');
            }
        });
    });
    
    document.getElementById('correctos').innerText = correctos;
    const resultadoEl = document.getElementById('resultado');
    
    if(correctos === 15) {
        playSound('success');
        resultadoEl.innerHTML = `
            <div class="result-box success">
                <span class="result-icon">✓</span>
                <span class="result-text">¡Perfecto! Todos los 15 elementos clasificados correctamente</span>
            </div>`;
        resultadoEl.style.display = 'block';
        
        setTimeout(() => {
            location.href = 'verificar_arrastrar.php?correctos=15';
        }, 1200);
    } else {
        resultadoEl.innerHTML = `
            <div class="result-box partial">
                <span class="result-icon">✗</span>
                <span class="result-text">Correctos: ${correctos}/15 - Sigue intentando</span>
            </div>`;
        resultadoEl.style.display = 'block';
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
    
    if(tiempo <= 0) location.href = 'verificar_arrastrar.php?tiempo=0';
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
    border: 1px solid rgba(255, 136, 0, 0.2);
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
    color: #ff8800;
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
    background: linear-gradient(135deg, #ff8800, #ff4400);
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

.progress-section {
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
    color: #00d4ff;
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
    background: linear-gradient(90deg, #00d4ff, #bf00ff);
    border-radius: 10px;
    transition: width 0.3s ease-out;
    box-shadow: 0 0 15px rgba(0, 212, 255, 0.5);
}

.score-display {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.score-number {
    font-family: 'Orbitron', sans-serif;
    font-size: 2rem;
    font-weight: 900;
    color: #00ff9f;
}

.score-label {
    color: #666;
    font-size: 0.8rem;
}

/* =============================================
   ELEMENTS POOL
   ============================================= */
.elements-pool-section {
    background: linear-gradient(135deg, rgba(18, 11, 26, 0.9), rgba(13, 10, 21, 0.95));
    border: 1px solid rgba(191, 0, 255, 0.2);
    border-radius: 16px;
    padding: 20px;
}

.pool-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 1px solid rgba(191, 0, 255, 0.15);
}

.pool-title {
    font-family: 'Orbitron', sans-serif;
    font-size: 1rem;
    color: #bf00ff;
}

.pool-hint {
    color: #666;
    font-size: 0.85rem;
}

.elements-pool {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    min-height: 80px;
    padding: 15px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 12px;
    border: 2px dashed rgba(191, 0, 255, 0.2);
}

.draggable-item {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(24, 16, 31, 0.9);
    border: 2px solid rgba(191, 0, 255, 0.3);
    border-radius: 12px;
    padding: 12px 16px;
    cursor: grab;
    transition: all 0.3s;
    animation: slideUp 0.3s ease-out backwards;
}

.draggable-item:hover {
    border-color: #bf00ff;
    transform: scale(1.05);
    box-shadow: 0 0 20px rgba(191, 0, 255, 0.3);
}

.draggable-item.dragging {
    opacity: 0.5;
}

.draggable-item.correct {
    border-color: #00ff9f;
    background: rgba(0, 255, 159, 0.15);
}

.draggable-item.incorrect {
    border-color: #ff4444;
    background: rgba(255, 68, 68, 0.15);
}

.drag-handle {
    color: #666;
    font-size: 1.2rem;
}

.draggable-content {
    display: flex;
    flex-direction: column;
}

.draggable-name {
    font-family: 'Rajdhani', sans-serif;
    font-size: 1rem;
    font-weight: 600;
    color: #fff;
}

.draggable-desc {
    font-size: 0.75rem;
    color: #666;
}

/* =============================================
   CATEGORIES GRID
   ============================================= */
.categories-section {
    margin-bottom: 20px;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 15px;
}

@media (max-width: 768px) {
    .categories-grid {
        grid-template-columns: 1fr;
    }
}

.category-box {
    background: rgba(18, 11, 26, 0.9);
    border: 2px dashed var(--cat-color);
    border-radius: 16px;
    padding: 15px;
    min-height: 150px;
    transition: all 0.3s;
}

.category-box.drag-over {
    border-style: solid;
    background: rgba(191, 0, 255, 0.05);
    box-shadow: 0 0 20px rgba(191, 0, 255, 0.2);
}

.category-header {
    display: flex;
    align-items: center;
    gap: 10px;
    margin-bottom: 15px;
    padding-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.cat-icon {
    font-size: 1.5rem;
}

.cat-name {
    flex: 1;
    font-family: 'Orbitron', sans-serif;
    font-size: 1rem;
    color: var(--cat-color);
}

.cat-count {
    font-size: 0.85rem;
    color: #666;
}

.category-items {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    min-height: 60px;
}

.category-items .draggable-item {
    font-size: 0.85rem;
    padding: 8px 12px;
}

/* =============================================
   RESULT & BUTTON
   ============================================= */
.resultado {
    margin-bottom: 20px;
}

.result-box {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    padding: 20px;
    border-radius: 14px;
    animation: slideUp 0.3s ease-out;
}

.result-box.success {
    background: rgba(0, 255, 159, 0.15);
    border: 2px solid #00ff9f;
    color: #00ff9f;
}

.result-box.partial {
    background: rgba(255, 170, 0, 0.15);
    border: 2px solid #ff8800;
    color: #ff8800;
}

.result-icon {
    font-size: 2rem;
    font-weight: 900;
}

.result-text {
    font-size: 1.1rem;
}

.btn-verify {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    background: linear-gradient(135deg, #ff8800, #ff4400);
    border: none;
    border-radius: 14px;
    padding: 18px;
    font-family: 'Orbitron', sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: #fff;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-verify:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(255, 136, 0, 0.4);
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
    
    .game-info-bar {
        flex-direction: column;
    }
    
    .howto-steps {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .game-title-simple h1 {
        font-size: 1.4rem;
    }
    
    .draggable-item {
        padding: 8px 12px;
    }
}
</style>

</body>
</html>