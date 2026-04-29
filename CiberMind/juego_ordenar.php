<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();
$hoy = date("Y-m-d");
$ayer = date("Y-m-d", strtotime("-1 day"));
$racha = ($user["ultima_fecha"] == $hoy || $user["ultima_fecha"] == $ayer) ? $user["racha"] : 0;

$secuencias = [
    ["categoria" => "Historia Web", "items" => [
        ["texto" => "ARPANET", "orden" => 1, "desc" => "Primera red de computadoras (1969)"],
        ["texto" => "HTML", "orden" => 2, "desc" => "Lenguaje de marcado para web (1990)"],
        ["texto" => "Netscape", "orden" => 3, "desc" => "Primer navegador popular (1994)"],
        ["texto" => "Web 2.0", "orden" => 4, "desc" => "Web interactiva y colaborativa"],
        ["texto" => "HTML5", "orden" => 5, "desc" => "Estándar moderno de HTML"]
    ]],
    ["categoria" => "Niveles de Sistema Operativo", "items" => [
        ["texto" => "Kernel", "orden" => 1, "desc" => "Núcleo que gestiona recursos"],
        ["texto" => "Shell", "orden" => 2, "desc" => "Interfaz de línea de comandos"],
        ["texto" => "Sistema de archivos", "orden" => 3, "desc" => "Organiza datos en disco"],
        ["texto" => "Aplicaciones", "orden" => 4, "desc" => "Software del usuario"],
        ["texto" => "Interfaz gráfica", "orden" => 5, "desc" => "GUI para interacción visual"]
    ]],
    ["categoria" => "Ciclo de Desarrollo de Software", "items" => [
        ["texto" => "Requisitos", "orden" => 1, "desc" => "Definir qué necesita el sistema"],
        ["texto" => "Diseño", "orden" => 2, "desc" => "Planificar la arquitectura"],
        ["texto" => "Implementación", "orden" => 3, "desc" => "Escribir el código"],
        ["texto" => "Pruebas", "orden" => 4, "desc" => "Verificar funcionamiento"],
        ["texto" => "Mantenimiento", "orden" => 5, "desc" => "Actualizar y corregir errores"]
    ]],
    ["categoria" => "Modelo OSI (Capas de Red)", "items" => [
        ["texto" => "Física", "orden" => 1, "desc" => "Señales eléctricas y cables"],
        ["texto" => "Enlace de datos", "orden" => 2, "desc" => "Dirección MAC y frames"],
        ["texto" => "Red", "orden" => 3, "desc" => "Enrutamiento y direcciones IP"],
        ["texto" => "Transporte", "orden" => 4, "desc" => "TCP/UDP y puertos"],
        ["texto" => "Aplicación", "orden" => 5, "desc" => "HTTP, FTP, SMTP"]
    ]],
    ["categoria" => "Top 10 OWASP (Seguridad)", "items" => [
        ["texto" => "Inyección", "orden" => 1, "desc" => "SQL, NoSQL, OS Injection"],
        ["texto" => "Ruptura autenticación", "orden" => 2, "desc" => "Credenciales débiles"],
        ["texto" => "Exposición de datos", "orden" => 3, "desc" => "Datos sin cifrar"],
        ["texto" => "XSS", "orden" => 4, "desc" => "Cross-Site Scripting"],
        ["texto" => "Configuración incorrecta", "orden" => 5, "desc" => "Ajustes de seguridad faltantes"]
    ]],
    ["categoria" => "Ataques Cibernéticos (Ciclo)", "items" => [
        ["texto" => "Reconocimiento", "orden" => 1, "desc" => "Recopilar información"],
        ["texto" => "Acceso inicial", "orden" => 2, "desc" => "Entrar al sistema"],
        ["texto" => "Escalada privilegios", "orden" => 3, "desc" => "Obtener más acceso"],
        ["texto" => "Movimiento lateral", "orden" => 4, "desc" => "Explorar la red"],
        ["texto" => "Exfiltración", "orden" => 5, "desc" => "Robar datos"]
    ]]
];

$seq = $secuencias[array_rand($secuencias)];
$items = $seq["items"];
shuffle($items);

$_SESSION["secuencia"] = $items;
$_SESSION["categoria"] = $seq["categoria"];
$_SESSION["ordenados"] = 0;

$items_json = json_encode($items);
?>

<!DOCTYPE html>
<html>
<head>
<title>Ordenar - CiberMind</title>
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
            <span class="game-icon-sm">📊</span>
            <h1>Ordenar Secuencia</h1>
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
                <span>Arrastra los elementos para ordenarlos</span>
            </div>
            <div class="step">
                <span class="step-num">2</span>
                <span>Del más básico al más avanzado</span>
            </div>
            <div class="step">
                <span class="step-num">3</span>
                <span>Presiona VERIFICAR para comprobar</span>
            </div>
        </div>
    </div>

    <!-- Game Info Bar -->
    <div class="game-info-bar mb-4">
        <div class="timer-display-large">
            <div class="timer-ring" id="timerRing">
                <span id="timer">45</span>
            </div>
            <span class="timer-label">Segundos</span>
        </div>
        <div class="category-badge">
            <span class="category-icon">📂</span>
            <span class="category-name"><?= $seq["categoria"] ?></span>
        </div>
        <div class="score-display">
            <span class="score-number" id="correctos">0</span>
            <span class="score-label">/5 correctos</span>
        </div>
    </div>

    <!-- Instruction Banner -->
    <div class="instruction-banner mb-4">
        <span class="instruction-icon">💡</span>
        <span>Arrastra los elementos de arriba hacia abajo para ordenarlos correctamente</span>
    </div>

    <!-- Orderable Items -->
    <div id="ordenable" class="ordenable mb-4">
        <?php foreach($items as $i => $item): ?>
        <div class="orden-item" draggable="true" data-orden="<?= $item["orden"] ?>" data-index="<?= $i ?>">
            <div class="orden-number"><?= $i + 1 ?></div>
            <div class="orden-content">
                <span class="orden-texto"><?= htmlspecialchars($item["texto"]) ?></span>
                <span class="orden-desc"><?= htmlspecialchars($item["desc"]) ?></span>
            </div>
            <div class="orden-drag-icon">⋮⋮</div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Result Display -->
    <div id="resultado" class="resultado" style="display: none;"></div>

    <!-- Verify Button -->
    <button class="btn-verify" onclick="verificarOrden()">
        <span class="verify-icon">✓</span>
        <span>VERIFICAR RESPUESTA</span>
    </button>

    <!-- Back -->
    <div class="text-center mt-4">
        <a href="menu.php" class="btn btn-ghost-outline">
            ← Volver al Menú
        </a>
    </div>

</div>

<script>
const ordenable = document.getElementById('ordenable');
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
    } else if(type === 'partial') {
        const osc = audioCtx.createOscillator();
        const gain = audioCtx.createGain();
        osc.connect(gain);
        gain.connect(audioCtx.destination);
        osc.frequency.setValueAtTime(300, audioCtx.currentTime);
        gain.gain.setValueAtTime(0.06, audioCtx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.2);
        osc.start();
        osc.stop(audioCtx.currentTime + 0.2);
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

document.querySelectorAll('.orden-item').forEach(item => {
    item.addEventListener('dragstart', e => {
        draggedItem = item;
        item.classList.add('dragging');
        playSound('pick');
        setTimeout(() => item.style.opacity = '0.6', 0);
    });
    
    item.addEventListener('dragend', e => {
        item.classList.remove('dragging');
        item.style.opacity = '1';
        playSound('drop');
        draggedItem = null;
        updateNumbers();
    });
});

ordenable.addEventListener('dragover', e => {
    e.preventDefault();
    const afterElement = getDragAfterElement(e.clientY);
    if(draggedItem) {
        if(afterElement == null) {
            ordenable.appendChild(draggedItem);
        } else if(afterElement !== draggedItem) {
            ordenable.insertBefore(draggedItem, afterElement);
        }
    }
});

function getDragAfterElement(y) {
    const draggableElements = [...ordenable.querySelectorAll('.orden-item:not(.dragging)')];
    return draggableElements.reduce((closest, child) => {
        const box = child.getBoundingClientRect();
        const offset = y - box.top - box.height / 2;
        if(offset < 0 && offset > closest.offset) {
            return { offset: offset, element: child };
        }
        return closest;
    }, { offset: Number.NEGATIVE_INFINITY }).element;
}

function updateNumbers() {
    document.querySelectorAll('.orden-item').forEach((el, i) => {
        el.querySelector('.orden-number').innerText = i + 1;
    });
}

function verificarOrden() {
    const ordenados = document.querySelectorAll('.orden-item');
    let correctos = 0;
    
    ordenados.forEach((el, i) => {
        if(parseInt(el.dataset.orden) === i + 1) {
            correctos++;
            el.classList.add('correct');
            el.classList.remove('incorrect');
        } else {
            el.classList.remove('correct');
            el.classList.add('incorrect');
        }
    });
    
    document.getElementById('correctos').innerText = correctos;
    const resultadoEl = document.getElementById('resultado');
    
    if(correctos === 5) {
        playSound('success');
        resultadoEl.innerHTML = `
            <div class="result-box success">
                <span class="result-icon">✓</span>
                <span class="result-text">¡Perfecto! Todos los ${correctos}/5 elementos están en orden correcto</span>
            </div>`;
        resultadoEl.style.display = 'block';
        
        setTimeout(() => {
            location.href = 'verificar_ordenar.php?correctos=' + correctos;
        }, 1200);
    } else {
        playSound('partial');
        resultadoEl.innerHTML = `
            <div class="result-box partial">
                <span class="result-icon">✗</span>
                <span class="result-text">Correctos: ${correctos}/5 - Sigue intentando</span>
            </div>`;
        resultadoEl.style.display = 'block';
    }
}

let tiempo = 45;
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
    
    if(tiempo <= 0) location.href = 'verificar_ordenar.php?tiempo=0';
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
    font-size: 1.6rem;
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

.category-badge {
    display: flex;
    align-items: center;
    gap: 12px;
    background: rgba(191, 0, 255, 0.15);
    padding: 12px 25px;
    border-radius: 12px;
    border: 1px solid rgba(191, 0, 255, 0.3);
}

.category-icon {
    font-size: 1.5rem;
}

.category-name {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.1rem;
    color: #bf00ff;
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
   INSTRUCTION BANNER
   ============================================= */
.instruction-banner {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 12px;
    background: rgba(255, 170, 0, 0.1);
    border: 1px solid rgba(255, 170, 0, 0.3);
    border-radius: 12px;
    padding: 15px 20px;
    color: #ff8800;
}

.instruction-icon {
    font-size: 1.3rem;
}

/* =============================================
   ORDENABLE ITEMS
   ============================================= */
.ordenable {
    display: flex;
    flex-direction: column;
    gap: 10px;
    min-height: 200px;
    padding: 15px;
    background: linear-gradient(135deg, rgba(18, 11, 26, 0.9), rgba(13, 10, 21, 0.95));
    border: 2px dashed rgba(191, 0, 255, 0.3);
    border-radius: 16px;
}

.orden-item {
    display: flex;
    align-items: center;
    gap: 15px;
    background: rgba(24, 16, 31, 0.9);
    border: 2px solid rgba(191, 0, 255, 0.2);
    border-radius: 14px;
    padding: 15px 20px;
    cursor: grab;
    transition: all 0.3s;
    animation: slideUp 0.4s ease-out backwards;
}

.orden-item:hover {
    border-color: rgba(191, 0, 255, 0.5);
    background: rgba(191, 0, 255, 0.1);
    transform: translateX(8px);
}

.orden-item.dragging {
    opacity: 0.6;
    border-style: dashed;
    transform: scale(1.02);
}

.orden-item.correct {
    border-color: #00ff9f;
    background: rgba(0, 255, 159, 0.1);
}

.orden-item.incorrect {
    border-color: #ff4444;
    background: rgba(255, 68, 68, 0.1);
}

.orden-number {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, #bf00ff, #8b00ff);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Orbitron', sans-serif;
    font-size: 1.2rem;
    font-weight: 700;
    color: #fff;
}

.orden-content {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.orden-texto {
    font-family: 'Rajdhani', sans-serif;
    font-size: 1.1rem;
    font-weight: 600;
    color: #fff;
}

.orden-desc {
    font-size: 0.85rem;
    color: #666;
}

.orden-drag-icon {
    color: #444;
    font-size: 1.5rem;
    cursor: grab;
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
    background: linear-gradient(135deg, #00ff9f, #00cc7a);
    border: none;
    border-radius: 14px;
    padding: 18px;
    font-family: 'Orbitron', sans-serif;
    font-size: 1.1rem;
    font-weight: 700;
    color: #000;
    cursor: pointer;
    transition: all 0.3s;
}

.btn-verify:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0, 255, 159, 0.4);
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
    
    .category-badge {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .game-title-simple h1 {
        font-size: 1.2rem;
    }
    
    .orden-item {
        padding: 12px 15px;
    }
    
    .orden-number {
        width: 32px;
        height: 32px;
        font-size: 1rem;
    }
}
</style>

</body>
</html>