<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();
$hoy = date("Y-m-d");
$ayer = date("Y-m-d", strtotime("-1 day"));
$racha = ($user["ultima_fecha"] == $hoy || $user["ultima_fecha"] == $ayer) ? $user["racha"] : 0;
$dificultad = max(1, $user["dificultad"]);
$tiempo_base = max(8, 20 - ($dificultad - 1) * 2);

$porcentaje = $user["xp"];
$xp_max = 20 + ($dificultad * 5);

$textos = [
    [
        "titulo" => "🔐 Conceptos de Ciberseguridad",
        "texto" => "La ciberseguridad protege sistemas, redes y programas de ataques digitales. Las principales amenazas incluyen:\n\n• Malware: Software malicioso (virus, gusanos, troyanos)\n• Phishing: Correos engañosos para robar información\n• Ransomware: Bloquea archivos hasta pagar rescate\n• Ataques de fuerza bruta: Prueban muchas contraseñas\n\nLas organizaciones usan 'defensa en profundidad': múltiples capas de seguridad.",
        "pregunta" => "¿Qué es la 'defensa en profundidad'?",
        "opciones" => ["Un tipo específico de firewall", "Múltiples capas de seguridad", "Un algoritmo de cifrado avanzado"],
        "correcta" => 2,
        "explicacion" => "Defensa en profundidad es una estrategia de seguridad que utiliza múltiples capas de protección."
    ],
    [
        "titulo" => "🦠 Tipos de Malware",
        "texto" => "El malware (software malicioso) tiene varias variantes:\n\n• Virus: Necesita un archivo host para propagarse\n• Gusanos (Worms): Se autoreplican sin archivo host\n• Troyanos: Se disfrazan de software legítimo\n• Ransomware: Cifra archivos y pide rescate\n• Spyware: Espía tu actividad sin que lo sepas\n• Adware: Muestra publicidad no deseada\n\nCada tipo requiere diferentes métodos de protección.",
        "pregunta" => "¿Cuál se propaga automáticamente sin necesidad de un archivo host?",
        "opciones" => ["Virus", "Gusano (Worm)", "Troyano"],
        "correcta" => 2,
        "explicacion" => "Los gusanos (worms) pueden propagarse por sí solos sin necesidad de un archivo host."
    ],
    [
        "titulo" => "🔑 Fundamentos de Criptografía",
        "texto" => "La criptografía protege la confidencialidad de tus datos:\n\n• Cifrado Simétrico: Misma clave para cifrar y descifrar (AES)\n• Cifrado Asimétrico: Par de claves pública y privada (RSA)\n• Hash: Función que crea un 'fingerprint' único (SHA-256)\n\nEjemplo: Cuando ves el candado verde en una web, usa HTTPS con TLS/SSL para proteger tus datos.",
        "pregunta" => "¿Qué tipo de cifrado usa un par de claves (pública y privada)?",
        "opciones" => ["Cifrado Simétrico", "Cifrado Asimétrico", "Cifrado Hash"],
        "correcta" => 2,
        "explicacion" => "El cifrado asimétrico usa dos claves relacionadas: una pública para cifrar y una privada para descifrar."
    ],
    [
        "titulo" => "🌐 Protocolos de Seguridad Web",
        "texto" => "Los protocolos seguros protegen tus comunicaciones:\n\n• HTTPS (Puerto 443): Versión segura de HTTP con TLS\n• SSH (Puerto 22): Conexiones de línea de comandos seguras\n• FTPS (Puerto 21): Transferencia de archivos segura\n• SFTP (Puerto 22): FTP seguro sobre SSH\n• SSL/TLS: Cifra la comunicación entre cliente y servidor\n\nNunca envíes datos sensibles por HTTP (sin S).",
        "pregunta" => "¿Qué protocolo seguro se usa típicamente en el puerto 22?",
        "opciones" => ["FTP", "SSH", "HTTP"],
        "correcta" => 2,
        "explicacion" => "SSH (Secure Shell) usa el puerto 22 para establecer conexiones seguras."
    ],
    [
        "titulo" => "🛡️ Firewalls y Sistemas de Detección",
        "texto" => "Los firewalls son la primera línea de defensa:\n\n• Firewall de paquetes: Examina paquetes individuales\n• Firewall de estado: Rastrea el estado de las conexiones\n• WAF (Web Application Firewall): Protege aplicaciones web\n\nIDS/IPS:\n• IDS (Sistema de Detección): Detecta amenazas\n• IPS (Sistema de Prevención): Detecta y bloquea amenazas\n\nLos firewalls de próxima generación combinan ambas funciones.",
        "pregunta" => "¿Qué herramienta detecta Y previene intrusiones en tiempo real?",
        "opciones" => ["Firewall tradicional", "IDS/IPS", "VPN"],
        "correcta" => 2,
        "explicacion" => "IPS (Sistema de Prevención de Intrusiones) detecta y bloquea amenazas en tiempo real."
    ],
    [
        "titulo" => "🎣 Técnicas de Ingeniería Social",
        "texto" => "La ingeniería social manipula a las personas:\n\n• Phishing: Correos falsos que parecen reales\n• Pretexting: Crear escenarios falsos para obtener info\n• Baiting: Ofrecer algo falso a cambio de datos\n• Quid pro quo: Servicio a cambio de información\n• Tailgating: Seguir a alguien autorizado físicamente\n\nLa educación es la mejor defensa: nunca confíes en solicitudes inesperadas.",
        "pregunta" => "¿Qué es exactamente el phishing?",
        "opciones" => ["Robo físico de hardware", "Correos engañosos para robar datos", "Ataque automático de fuerza bruta"],
        "correcta" => 2,
        "explicacion" => "Phishing usa comunicaciones falsas (email, web) para engañar víctimas y robar información."
    ],
    [
        "titulo" => "💾 Backup y Recuperación de Datos",
        "texto" => "La estrategia 3-2-1 para backups:\n\n• 3 copias de tus datos\n• 2 medios diferentes (disco, nube, cinta)\n• 1 copia fuera del sitio (nube, otra ciudad)\n\nTipos de backup:\n• Completo: Todo el sistema\n• Incremental: Solo cambios desde último backup\n• Diferencial: Cambios desde último completo\n\n¡Probar restaurar es obligatorio!",
        "pregunta" => "Según la regla 3-2-1, ¿cuántas copias se deben mantener?",
        "opciones" => ["1 copia", "2 copias", "3 copias"],
        "correcta" => 3,
        "explicacion" => "3-2-1 significa: 3 copias totales, en 2 medios diferentes, con 1 copia fuera del sitio."
    ],
    [
        "titulo" => "🔍 Análisis de Vulnerabilidades",
        "texto" => "El análisis de vulnerabilidades encuentra debilidades:\n\n• Pen-testing: Simula ataques reales\n• Nmap: Descubre puertos abiertos en la red\n• Nessus/OpenVAS: Scanners de vulnerabilidades\n• Burp Suite: Análisis de aplicaciones web\n• CVSS: Sistema de puntuación de severidad\n\nPasos del análisis:\n1. Reconocimiento\n2. Escaneo\n3. Explotación\n4. Reporte y remediación",
        "pregunta" => "¿Cuál de estas herramientas se usa principalmente para descubrir puertos abiertos?",
        "opciones" => ["Nmap", "Wireshark", "Metasploit"],
        "correcta" => 1,
        "explicacion" => "Nmap es el scanner de puertos y hosts más utilizado en ciberseguridad."
    ],
    [
        "titulo" => "🔐 Autenticación y Control de Acceso",
        "texto" => "Los sistemas de autenticación incluyen:\n\n• Algo que sabes: Contraseña, PIN\n• Algo que tienes: Token, tarjeta, smartphone\n• Algo que eres: Huella, rostro, iris (biometría)\n\nMétodos modernos:\n• MFA (Multi-Factor): Combina varios métodos\n• SSO (Single Sign-On): Un login para varios servicios\n• OAuth: Permisos sin compartir contraseñas\n\nLa autenticación fuerte es crucial para la seguridad.",
        "pregunta" => "¿Qué es el MFA (Multi-Factor Authentication)?",
        "opciones" => ["Varias contraseñas para una cuenta", "Combinación de varios métodos de autenticación", "Encriptación de múltiples archivos"],
        "correcta" => 2,
        "explicacion" => "MFA requiere múltiples formas de verificación (ej: contraseña + código del teléfono)."
    ],
    [
        "titulo" => "📱 Seguridad Mobile",
        "texto" => "Los dispositivos móviles requieren atención especial:\n\n• MDM (Mobile Device Management): Gestión centralizada\n• Cifrado del dispositivo: Protege datos almacenados\n• Apps sandbox: Aislamiento de aplicaciones\n• Actualizaciones: Mantener OS y apps al día\n\nAmenazas móviles:\n• Aplicaciones maliciosas en tiendas no oficiales\n• Redes Wi-Fi comprometidas\n• Perdida/robo del dispositivo\n\nSiempre descarga de fuentes oficiales.",
        "pregunta" => "¿Qué práctica es más importante para seguridad móvil?",
        "opciones" => ["Usar Wi-Fi público frecuentemente", "Descargar apps solo de fuentes oficiales", "Desactivar el cifrado del dispositivo"],
        "correcta" => 2,
        "explicacion" => "Descargar apps solo de tiendas oficiales reduce enormemente el riesgo de malware."
    ]
];

$texto_actual = $textos[array_rand($textos)];
?>

<!DOCTYPE html>
<html>
<head>
<title>Trivia - CiberMind</title>
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
            <span class="game-icon-sm">🎯</span>
            <h1>Trivia</h1>
        </div>
        <div class="game-stats-mini">
            <span class="mini-stat" title="Nivel">📊 <?= $user["nivel"] ?></span>
            <span class="mini-stat" title="Vidas">❤️ <?= $user["vidas"] ?></span>
            <span class="mini-stat" title="Racha">🔥 <?= $racha ?></span>
        </div>
    </header>

    <!-- Reading Section (Phase 1) -->
    <div class="game-phase" id="lectureSection">
        <div class="phase-badge">
            <span class="phase-number">PASO 1</span>
            <span class="phase-name">Lectura de Texto</span>
        </div>
        
        <div class="lecture-card">
            <div class="lecture-header">
                <div class="lecture-icon">📖</div>
                <div class="lecture-title-group">
                    <h2><?= $texto_actual["titulo"] ?></h2>
                    <p class="lecture-subtitle">Lee cuidadosamente el texto para responder correctamente</p>
                </div>
            </div>
            
            <div class="lecture-content">
                <?php foreach(explode("\n\n", $texto_actual["texto"]) as $parrafo): ?>
                <p><?= htmlspecialchars($parrafo) ?></p>
                <?php endforeach; ?>
            </div>
            
            <div class="lecture-tips">
                <div class="tip">
                    <span class="tip-icon">💡</span>
                    <span>Tip: Busca las palabras clave en negrita</span>
                </div>
                <div class="tip">
                    <span class="tip-icon">⏱️</span>
                    <span>Tienes <?= $tiempo_base ?> segundos para responder</span>
                </div>
            </div>
            
            <button class="btn-start-game" onclick="startGame()">
                <span>Comenzar Pregunta</span>
                <span class="arrow">▶</span>
            </button>
        </div>
    </div>

    <!-- Question Section (Phase 2) -->
    <div class="game-phase" id="questionSection" style="display: none;">
        <div class="phase-badge question-phase">
            <span class="phase-number">PASO 2</span>
            <span class="phase-name">Responder Pregunta</span>
        </div>
        
        <div class="question-card">
            <div class="question-timer-container">
                <div class="timer-circle" id="timerCircle">
                    <span id="timer"><?= $tiempo_base ?></span>
                </div>
                <div class="timer-info">
                    <span>Segundos</span>
                    <span class="timer-subtitle">Disponibles</span>
                </div>
            </div>
            
            <div class="question-box">
                <h3 class="question-text"><?= $texto_actual["pregunta"] ?></h3>
            </div>
            
            <div class="powerups-container">
                <button class="powerup-btn" id="btnPista" onclick="usarPista()">
                    <span class="powerup-icon">💡</span>
                    <span class="powerup-text">Pista</span>
                </button>
                <button class="powerup-btn" onclick="sumarTiempo()">
                    <span class="powerup-icon">⏰</span>
                    <span class="powerup-text">+5s</span>
                </button>
            </div>
            
            <div id="pistaTexto" class="pista-alert" style="display: none;">
                <span class="pista-icon">💡</span>
                <span id="pistaContent"></span>
            </div>
            
            <form method="POST" action="verificar.php" id="answerForm">
                <div class="options-container">
                    <?php foreach($texto_actual["opciones"] as $i => $opc): ?>
                    <label class="option-card" onclick="selectOption(this)">
                        <input type="radio" name="respuesta" value="<?= $i + 1 ?>" required>
                        <span class="option-letter"><?= chr(65 + $i) ?></span>
                        <span class="option-text"><?= $opc ?></span>
                        <span class="option-check">✓</span>
                    </label>
                    <?php endforeach; ?>
                </div>
                
                <input type="hidden" name="correcta" value="<?= $texto_actual["correcta"] ?>">
                <input type="hidden" name="explicacion" value="<?= $texto_actual["explicacion"] ?>">
                <input type="hidden" name="tiempo_inicio" id="tiempoInicio" value="<?= time() ?>">
                <input type="hidden" name="dificultad" value="<?= $dificultad ?>">
                <input type="hidden" name="xp_base" value="<?= $xp_max ?>">
                
                <button type="submit" class="btn-submit-answer" id="submitBtn">
                    <span>ENTREGAR RESPUESTA</span>
                    <span class="arrow">✓</span>
                </button>
            </form>
        </div>
    </div>

</div>

<script>
let tiempo = <?= $tiempo_base ?>;
let tiempoExtra = 0;
let pistas = 1;
let pistaUsada = false;
let selectedOption = null;

function playSound(type) {
    const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
    
    if(type === 'start') {
        [400, 500, 600].forEach((freq, i) => {
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
    } else if(type === 'select') {
        const o = audioCtx.createOscillator();
        const g = audioCtx.createGain();
        o.connect(g);
        g.connect(audioCtx.destination);
        o.frequency.setValueAtTime(800, audioCtx.currentTime);
        g.gain.setValueAtTime(0.05, audioCtx.currentTime);
        g.gain.exponentialRampToValueAtTime(0.01, audioCtx.currentTime + 0.1);
        o.start();
        o.stop(audioCtx.currentTime + 0.1);
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

function startGame() {
    playSound('start');
    document.getElementById('lectureSection').style.display = 'none';
    document.getElementById('questionSection').style.display = 'block';
    startTimer();
}

function startTimer() {
    setInterval(() => {
        tiempo--;
        const total = tiempo + tiempoExtra;
        const timerEl = document.getElementById('timer');
        const circleEl = document.getElementById('timerCircle');
        
        timerEl.innerText = Math.max(0, total);
        
        if(total <= 5) {
            circleEl.style.borderColor = '#ff4444';
            circleEl.style.color = '#ff4444';
            timerEl.style.color = '#ff4444';
        } else if(total <= 10) {
            circleEl.style.borderColor = '#ff8800';
            circleEl.style.color = '#ff8800';
            timerEl.style.color = '#ff8800';
        }
        
        if(total <= 0) {
            location.reload();
        }
    }, 1000);
}

function usarPista() {
    if(pistas > 0 && !pistaUsada) {
        pistas--;
        pistaUsada = true;
        document.getElementById('btnPista').disabled = true;
        document.getElementById('btnPista').style.opacity = '0.5';
        
        const opts = document.querySelectorAll('.option-text');
        const correcta = <?= $texto_actual["correcta"] ?>;
        let eliminada = false;
        
        for(let i = 0; i < opts.length; i++) {
            if(i + 1 !== correcta && !eliminada) {
                opts[i].style.textDecoration = 'line-through';
                opts[i].style.opacity = '0.3';
                opts[i].closest('.option-card').style.pointerEvents = 'none';
                eliminada = true;
            }
        }
        
        const pistaBox = document.getElementById('pistaTexto');
        pistaBox.style.display = 'flex';
        document.getElementById('pistaContent').innerHTML = 'Se eliminó una opción incorrecta';
    }
}

function sumarTiempo() {
    tiempoExtra += 5;
    const timerEl = document.getElementById('timer');
    const circleEl = document.getElementById('timerCircle');
    circleEl.style.borderColor = '#00ff9f';
    circleEl.style.color = '#00ff9f';
    timerEl.style.color = '#00ff9f';
}

function selectOption(el) {
    document.querySelectorAll('.option-card').forEach(o => o.classList.remove('selected'));
    el.classList.add('selected');
    selectedOption = el;
    playSound('select');
    
    document.getElementById('submitBtn').style.background = 'linear-gradient(135deg, #00ff9f, #00cc7a)';
}
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
   PHASE BADGES
   ============================================= */
.phase-badge {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 25px;
}

.phase-number {
    background: linear-gradient(135deg, #bf00ff, #8b00ff);
    padding: 8px 20px;
    border-radius: 8px;
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
    font-size: 0.9rem;
}

.phase-name {
    font-family: 'Rajdhani', sans-serif;
    font-size: 1.1rem;
    color: #888;
}

.question-phase .phase-number {
    background: linear-gradient(135deg, #00d4ff, #0088ff);
}

/* =============================================
   LECTURE CARD
   ============================================= */
.lecture-card {
    background: linear-gradient(145deg, #120b1a, #0a0510);
    border: 1px solid rgba(191, 0, 255, 0.2);
    border-radius: 24px;
    padding: 35px;
    animation: slideUp 0.5s ease-out;
}

.lecture-header {
    display: flex;
    gap: 25px;
    margin-bottom: 30px;
    padding-bottom: 25px;
    border-bottom: 1px solid rgba(191, 0, 255, 0.15);
}

.lecture-icon {
    font-size: 3.5rem;
    filter: drop-shadow(0 0 15px #bf00ff);
}

.lecture-title-group h2 {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.5rem;
    color: #fff;
    margin-bottom: 8px;
}

.lecture-subtitle {
    color: #666;
    font-size: 0.9rem;
}

.lecture-content {
    margin-bottom: 30px;
}

.lecture-content p {
    color: #aaa;
    line-height: 1.9;
    margin-bottom: 15px;
    font-size: 1rem;
}

.lecture-tips {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 30px;
    padding: 20px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 12px;
}

.tip {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #888;
    font-size: 0.9rem;
}

.tip-icon {
    font-size: 1.2rem;
}

.btn-start-game {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    background: linear-gradient(135deg, #bf00ff, #8b00ff);
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

.btn-start-game:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(191, 0, 255, 0.5);
}

/* =============================================
   QUESTION CARD
   ============================================= */
.question-card {
    background: linear-gradient(145deg, #120b1a, #0a0510);
    border: 1px solid rgba(0, 212, 255, 0.2);
    border-radius: 24px;
    padding: 35px;
    animation: slideUp 0.5s ease-out;
}

.question-timer-container {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 20px;
    margin-bottom: 30px;
}

.timer-circle {
    width: 100px;
    height: 100px;
    border: 4px solid #00d4ff;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
}

#timer {
    font-family: 'Orbitron', sans-serif;
    font-size: 2.5rem;
    font-weight: 900;
    color: #00d4ff;
}

.timer-info {
    display: flex;
    flex-direction: column;
}

.timer-info span:first-child {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.2rem;
    color: #fff;
}

.timer-subtitle {
    color: #666;
    font-size: 0.85rem;
}

.question-box {
    background: rgba(0, 212, 255, 0.1);
    border: 1px solid rgba(0, 212, 255, 0.2);
    border-radius: 16px;
    padding: 25px;
    margin-bottom: 25px;
}

.question-text {
    font-family: 'Rajdhani', sans-serif;
    font-size: 1.4rem;
    color: #fff;
    text-align: center;
    line-height: 1.6;
}

.powerups-container {
    display: flex;
    justify-content: center;
    gap: 15px;
    margin-bottom: 25px;
}

.powerup-btn {
    display: flex;
    align-items: center;
    gap: 8px;
    background: rgba(18, 11, 26, 0.8);
    border: 1px solid rgba(191, 0, 255, 0.3);
    border-radius: 12px;
    padding: 12px 20px;
    color: #fff;
    font-family: 'Rajdhani', sans-serif;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s;
}

.powerup-btn:hover {
    background: rgba(191, 0, 255, 0.2);
    transform: translateY(-2px);
}

.powerup-btn:disabled {
    opacity: 0.5;
    cursor: not-allowed;
}

.pista-alert {
    display: flex;
    align-items: center;
    gap: 10px;
    background: rgba(255, 170, 0, 0.15);
    border: 1px solid rgba(255, 170, 0, 0.3);
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 25px;
    color: #ff8800;
    font-size: 0.95rem;
}

.pista-icon {
    font-size: 1.3rem;
}

/* =============================================
   OPTIONS
   ============================================= */
.options-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 25px;
}

.option-card {
    display: flex;
    align-items: center;
    gap: 15px;
    background: rgba(18, 11, 26, 0.8);
    border: 2px solid rgba(191, 0, 255, 0.2);
    border-radius: 14px;
    padding: 18px 20px;
    cursor: pointer;
    transition: all 0.3s;
}

.option-card:hover {
    border-color: #bf00ff;
    background: rgba(191, 0, 255, 0.1);
    transform: translateX(5px);
}

.option-card.selected {
    border-color: #00ff9f;
    background: rgba(0, 255, 159, 0.15);
}

.option-card input {
    display: none;
}

.option-letter {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, rgba(191, 0, 255, 0.3), rgba(191, 0, 255, 0.1));
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
    color: #bf00ff;
}

.option-text {
    flex: 1;
    font-family: 'Rajdhani', sans-serif;
    font-size: 1.05rem;
    color: #fff;
    line-height: 1.4;
}

.option-check {
    font-size: 1.5rem;
    color: #00ff9f;
    opacity: 0;
    transition: opacity 0.3s;
}

.option-card.selected .option-check {
    opacity: 1;
}

.btn-submit-answer {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 15px;
    background: linear-gradient(135deg, #bf00ff, #8b00ff);
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

.btn-submit-answer:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(191, 0, 255, 0.5);
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
    
    .lecture-card, .question-card {
        padding: 25px;
    }
    
    .lecture-header {
        flex-direction: column;
        text-align: center;
    }
    
    .lecture-tips {
        flex-direction: column;
    }
    
    .question-timer-container {
        flex-direction: column;
    }
    
    .powerups-container {
        flex-direction: column;
    }
}

@media (max-width: 480px) {
    .game-title-simple h1 {
        font-size: 1.4rem;
    }
    
    .question-text {
        font-size: 1.2rem;
    }
}
</style>

</body>
</html>