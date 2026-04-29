<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();
$hoy = date("Y-m-d");

$xp_actual = $user["xp"];
$xp_necesaria = 100;
$porcentaje_xp = min(($xp_actual / $xp_necesaria) * 100, 100);

$avatar_id = isset($user["avatar_id"]) ? $user["avatar_id"] : 1;
$avatar_actual = $conn->query("SELECT * FROM avatares WHERE id=$avatar_id")->fetch_assoc();
if(!$avatar_actual) {
    $avatar_actual = $conn->query("SELECT * FROM avatares ORDER BY nivel_req LIMIT 1")->fetch_assoc();
}

$juegos = [
    [
        "id" => "trivia", 
        "nombre" => "Trivia", 
        "desc" => "Lee textos de ciberseguridad y responde preguntas de comprensión",
        "xp" => "+20", 
        "time" => "20s", 
        "icon" => "🎯",
        "color" => "#bf00ff",
        "link" => "juego.php",
        "nivel_req" => 1
    ],
    [
        "id" => "parejas", 
        "nombre" => "Parejas", 
        "desc" => "Empareja conceptos de ciberseguridad con sus definiciones",
        "xp" => "+25", 
        "time" => "60s", 
        "icon" => "🃏",
        "color" => "#00d4ff",
        "link" => "juego_parejas.php",
        "nivel_req" => 2
    ],
    [
        "id" => "ordenar", 
        "nombre" => "Ordenar", 
        "desc" => "Ordena secuencias tecnológicas de forma correcta",
        "xp" => "+30", 
        "time" => "45s", 
        "icon" => "📊",
        "color" => "#00ff9f",
        "link" => "juego_ordenar.php",
        "nivel_req" => 3
    ],
    [
        "id" => "snake", 
        "nombre" => "Snake", 
        "desc" => "Juego clásico Snake con temática cyberpunk",
        "xp" => "+40", 
        "time" => "∞", 
        "icon" => "🐍",
        "color" => "#ff8800",
        "link" => "snake.php",
        "nivel_req" => 1
    ]
];

$menu_items = [
    ["icon" => "🏪", "nombre" => "Tienda", "desc" => "Compra power-ups y recompensas", "link" => "tienda.php", "color" => "#ffd700"],
    ["icon" => "🎁", "nombre" => "Diario", "desc" => "Bonus diario por iniciar sesión", "link" => "diario.php", "color" => "#ff4444"],
    ["icon" => "🎯", "nombre" => "Misiones", "desc" => "Tareas diarias con recompensas", "link" => "misiones.php", "color" => "#00ff9f"],
    ["icon" => "📝", "nombre" => "Evaluaciones", "desc" => "Pon a prueba tus conocimientos", "link" => "evaluaciones.php", "color" => "#00d4ff"],
    ["icon" => "🏆", "nombre" => "Rangos", "desc" => "Tu progreso como cibernauta", "link" => "avatares.php", "color" => "#ff00ff"],
    ["icon" => "👥", "nombre" => "Amigos", "desc" => "Conecta con otros jugadores", "link" => "amigos.php", "color" => "#bf00ff"],
    ["icon" => "📊", "nombre" => "Stats", "desc" => "Tu progreso detallado", "link" => "stats.php", "color" => "#bf00ff"],
    ["icon" => "🏅", "nombre" => "Ranking", "desc" => "Clasificación global", "link" => "ranking.php", "color" => "#ffd700"],
    ["icon" => "🏆", "nombre" => "Logros", "desc" => "Insignias desbloqueadas", "link" => "logros.php", "color" => "#ff8800"],
    ["icon" => "👤", "nombre" => "Perfil", "desc" => "Tu perfil y configuración", "link" => "perfil.php", "color" => "#00ffff"]
];
?>

<!DOCTYPE html>
<html>
<head>
<title>CiberMind - Cyber Training</title>
<link rel="stylesheet" href="style.css">
<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Rajdhani:wght@400;500;600;700&display=swap" rel="stylesheet">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>

<div class="particles-bg" id="particles"></div>
<div class="grid-bg"></div>

<div class="container">
    
    <!-- Header Logo -->
    <header class="main-header">
        <div class="logo-container">
            <div class="logo-icon">⚡</div>
            <div class="logo-text">
                <h1>CIBERMIND</h1>
                <p>Centro de Entrenamiento Ciberseguridad</p>
            </div>
        </div>
        
        <!-- User Avatar -->
        <div class="user-avatar">
            <div class="avatar-ring"></div>
            <div class="avatar-inner"><?= $avatar_actual["icono"] ?></div>
        </div>
    </header>

    <!-- Player Stats Panel -->
    <section class="stats-panel">
        <div class="stats-header">
            <div class="player-info">
                <span class="player-name"><?= htmlspecialchars($user["username"]) ?></span>
                <span class="player-badge">
                    <span class="badge-icon">⚡</span>
                    Usuario Activo
                </span>
            </div>
            <div class="xp-container">
                <div class="xp-label">
                    <span>Nivel <?= $user["nivel"] ?></span>
                    <span class="xp-value"><?= $xp_actual ?>/100 XP</span>
                </div>
                <div class="xp-bar-outer">
                    <div class="xp-bar-inner" style="width: <?= $porcentaje_xp ?>%"></div>
                </div>
            </div>
        </div>
        
        <div class="stats-icons">
            <div class="stat-icon-item" title="Nivel">
                <div class="stat-icon-wrapper level">
                    <span>📊</span>
                </div>
                <span class="stat-number"><?= $user["nivel"] ?></span>
                <span class="stat-label">Nivel</span>
            </div>
            <div class="stat-icon-item" title="XP">
                <div class="stat-icon-wrapper xp">
                    <span>✨</span>
                </div>
                <span class="stat-number"><?= $user["xp"] ?>%</span>
                <span class="stat-label">XP</span>
            </div>
            <div class="stat-icon-item" title="Vidas">
                <div class="stat-icon-wrapper lives">
                    <span>❤️</span>
                </div>
                <span class="stat-number"><?= $user["vidas"] ?></span>
                <span class="stat-label">Vidas</span>
            </div>
            <div class="stat-icon-item" title="Racha">
                <div class="stat-icon-wrapper streak">
                    <span>🔥</span>
                </div>
                <span class="stat-number"><?= $user["racha"] ?></span>
                <span class="stat-label">Racha</span>
            </div>
            <div class="stat-icon-item" title="Puntos">
                <div class="stat-icon-wrapper points">
                    <span>💎</span>
                </div>
                <span class="stat-number"><?= $user["score"] ?></span>
                <span class="stat-label">Puntos</span>
            </div>
        </div>
    </section>

    <!-- Games Section -->
    <section class="games-section">
        <div class="section-header">
            <div class="section-title-group">
                <span class="section-icon">🎮</span>
                <h2>Juegos Disponibles</h2>
            </div>
            <p class="section-subtitle">Selecciona un juego para comenzar tu entrenamiento</p>
        </div>
        
        <div class="games-grid">
            <?php foreach($juegos as $i => $j): ?>
            <a href="<?= $j["link"] ?>" class="game-card" style="--accent: <?= $j["color"] ?>; animation-delay: <?= $i * 0.1 ?>s">
                <div class="game-card-glow"></div>
                <div class="game-card-content">
                    <div class="game-card-header">
                        <div class="game-icon-large"><?= $j["icon"] ?></div>
                        <div class="game-level-badge">
                            <span class="badge-label">Nivel</span>
                            <span class="badge-number"><?= $j["nivel_req"] ?></span>
                        </div>
                    </div>
                    
                    <h3 class="game-name"><?= $j["nombre"] ?></h3>
                    <p class="game-description"><?= $j["desc"] ?></p>
                    
                    <div class="game-meta">
                        <div class="meta-item">
                            <span class="meta-icon">⚡</span>
                            <span class="meta-value xp-color"><?= $j["xp"] ?></span>
                        </div>
                        <div class="meta-item">
                            <span class="meta-icon">⏱️</span>
                            <span class="meta-value"><?= $j["time"] ?></span>
                        </div>
                    </div>
                    
                    <div class="game-play-btn">
                        <span>JUGAR</span>
                        <span class="arrow">→</span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Quick Menu Section -->
    <section class="menu-section">
        <div class="section-header">
            <div class="section-title-group">
                <span class="section-icon">⚙️</span>
                <h2>Menú Principal</h2>
            </div>
            <p class="section-subtitle">Accede a todas las funciones del juego</p>
        </div>
        
        <div class="menu-grid">
            <?php foreach($menu_items as $item): ?>
            <a href="<?= $item["link"] ?>" class="menu-item" style="--item-color: <?= $item["color"] ?>">
                <div class="menu-item-icon"><?= $item["icon"] ?></div>
                <div class="menu-item-info">
                    <h4><?= $item["nombre"] ?></h4>
                    <p><?= $item["desc"] ?></p>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- How to Play -->
    <section class="howto-section">
        <div class="section-header">
            <div class="section-title-group">
                <span class="section-icon">📖</span>
                <h2>¿Cómo Jugar?</h2>
            </div>
        </div>
        
        <div class="howto-cards">
            <div class="howto-card">
                <div class="howto-number">01</div>
                <div class="howto-content">
                    <h4>Elige un Juego</h4>
                    <p>Selecciona cualquiera de los 4 juegos disponibles desde la sección de arriba.</p>
                </div>
            </div>
            <div class="howto-card">
                <div class="howto-number">02</div>
                <div class="howto-content">
                    <h4>Lee las Instrucciones</h4>
                    <p>Cada juego tiene instrucciones claras. Léeselas antes de comenzar.</p>
                </div>
            </div>
            <div class="howto-card">
                <div class="howto-number">03</div>
                <div class="howto-content">
                    <h4>Juega y Gana XP</h4>
                    <p>Completa los juegos para ganar XP, puntos y aumentar tu racha.</p>
                </div>
            </div>
            <div class="howto-card">
                <div class="howto-number">04</div>
                <div class="howto-content">
                    <h4>Sube de Nivel</h4>
                    <p>Acumula 100 XP para subir de nivel y desbloquear más funciones.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Logout -->
    <footer class="main-footer">
        <a href="logout.php" class="logout-btn">
            <span>🚪</span>
            <span>Cerrar Sesión</span>
        </a>
        <p class="footer-text">CiberMind v2.0 - Entrenamiento Ciberseguridad</p>
    </footer>

</div>

<script>
// Create floating particles
function createParticles() {
    const container = document.getElementById('particles');
    const colors = ['#bf00ff', '#00d4ff', '#00ff9f', '#ff00ff'];
    
    for(let i = 0; i < 20; i++) {
        const particle = document.createElement('div');
        particle.className = 'particle';
        particle.style.cssText = `
            left: ${Math.random() * 100}%;
            top: ${Math.random() * 100}%;
            background: ${colors[Math.floor(Math.random() * colors.length)]};
            animation-delay: ${Math.random() * 5}s;
            animation-duration: ${5 + Math.random() * 10}s;
        `;
        container.appendChild(particle);
    }
}

createParticles();

// Add hover effects to game cards
document.querySelectorAll('.game-card').forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.transform = 'translateY(-10px) scale(1.02)';
    });
    card.addEventListener('mouseleave', () => {
        card.style.transform = 'translateY(0) scale(1)';
    });
});
</script>

<style>
/* =============================================
   HEADER & LOGO
   ============================================= */
.main-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 30px 0;
    margin-bottom: 30px;
    border-bottom: 1px solid rgba(191, 0, 255, 0.2);
}

.logo-container {
    display: flex;
    align-items: center;
    gap: 20px;
}

.logo-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, #bf00ff, #8b00ff);
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    animation: logoPulse 3s ease-in-out infinite;
    box-shadow: 0 0 30px rgba(191, 0, 255, 0.5);
}

@keyframes logoPulse {
    0%, 100% { transform: scale(1); box-shadow: 0 0 30px rgba(191, 0, 255, 0.5); }
    50% { transform: scale(1.05); box-shadow: 0 0 50px rgba(191, 0, 255, 0.8); }
}

.logo-text h1 {
    font-family: 'Orbitron', sans-serif;
    font-size: 2.5rem;
    background: linear-gradient(135deg, #bf00ff 0%, #00d4ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    margin-bottom: 5px;
}

.logo-text p {
    font-size: 0.85rem;
    color: #666;
    font-family: 'Rajdhani', sans-serif;
}

.user-avatar {
    position: relative;
    width: 60px;
    height: 60px;
}

.avatar-ring {
    position: absolute;
    inset: 0;
    border: 3px solid transparent;
    border-radius: 50%;
    background: linear-gradient(#120b1a, #120b1a) padding-box,
                linear-gradient(135deg, #bf00ff, #00d4ff) border-box;
    animation: avatarRing 3s linear infinite;
}

@keyframes avatarRing {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.avatar-inner {
    position: absolute;
    inset: 5px;
    background: linear-gradient(135deg, #bf00ff, #8b00ff);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Orbitron', sans-serif;
    font-size: 1.5rem;
    font-weight: 900;
}

/* =============================================
   STATS PANEL
   ============================================= */
.stats-panel {
    background: linear-gradient(135deg, rgba(18, 11, 26, 0.9), rgba(13, 10, 21, 0.95));
    border: 1px solid rgba(191, 0, 255, 0.3);
    border-radius: 24px;
    padding: 25px;
    margin-bottom: 40px;
    backdrop-filter: blur(10px);
}

.stats-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 20px;
}

.player-info {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.player-name {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.3rem;
    font-weight: 700;
    color: #fff;
}

.player-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    background: rgba(191, 0, 255, 0.2);
    padding: 4px 12px;
    border-radius: 20px;
    font-size: 0.75rem;
    color: #bf00ff;
    width: fit-content;
}

.badge-icon {
    font-size: 0.9rem;
}

.xp-container {
    flex: 1;
    max-width: 300px;
    min-width: 200px;
}

.xp-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    font-family: 'Rajdhani', sans-serif;
    font-size: 0.9rem;
}

.xp-value {
    color: #bf00ff;
    font-weight: 600;
}

.xp-bar-outer {
    height: 12px;
    background: rgba(0, 0, 0, 0.5);
    border-radius: 10px;
    overflow: hidden;
    border: 1px solid rgba(191, 0, 255, 0.3);
}

.xp-bar-inner {
    height: 100%;
    background: linear-gradient(90deg, #bf00ff, #00d4ff);
    border-radius: 10px;
    transition: width 0.5s ease-out;
    box-shadow: 0 0 20px rgba(191, 0, 255, 0.5);
    animation: xpShine 2s ease-in-out infinite;
}

@keyframes xpShine {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.stats-icons {
    display: flex;
    justify-content: space-around;
    flex-wrap: wrap;
    gap: 15px;
}

.stat-icon-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 15px 20px;
    background: rgba(0, 0, 0, 0.3);
    border-radius: 16px;
    transition: all 0.3s;
}

.stat-icon-item:hover {
    transform: translateY(-5px);
    background: rgba(191, 0, 255, 0.1);
}

.stat-icon-wrapper {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.stat-icon-wrapper.level { background: linear-gradient(135deg, #bf00ff, #8b00ff); }
.stat-icon-wrapper.xp { background: linear-gradient(135deg, #ffd700, #ff8800); }
.stat-icon-wrapper.lives { background: linear-gradient(135deg, #ff4444, #ff0000); }
.stat-icon-wrapper.streak { background: linear-gradient(135deg, #ff8800, #ff4400); }
.stat-icon-wrapper.points { background: linear-gradient(135deg, #00d4ff, #0088ff); }

.stat-number {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.4rem;
    font-weight: 700;
    color: #fff;
}

.stat-label {
    font-size: 0.75rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* =============================================
   SECTION HEADERS
   ============================================= */
.section-header {
    margin-bottom: 25px;
}

.section-title-group {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 8px;
}

.section-icon {
    font-size: 2rem;
}

.section-title-group h2 {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.5rem;
    color: #fff;
}

.section-subtitle {
    font-size: 0.9rem;
    color: #666;
    margin-left: 50px;
}

/* =============================================
   GAMES GRID
   ============================================= */
.games-section {
    margin-bottom: 50px;
}

.games-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 25px;
}

.game-card {
    position: relative;
    background: linear-gradient(145deg, #120b1a, #0a0510);
    border: 1px solid rgba(191, 0, 255, 0.2);
    border-radius: 24px;
    padding: 25px;
    text-decoration: none;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    animation: slideUp 0.6s ease-out backwards;
}

.game-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--accent);
    opacity: 0;
    transition: opacity 0.3s;
}

.game-card:hover::before {
    opacity: 1;
}

.game-card-glow {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, var(--accent) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.4s;
    pointer-events: none;
}

.game-card:hover .game-card-glow {
    opacity: 0.05;
}

.game-card-content {
    position: relative;
    z-index: 1;
}

.game-card-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 20px;
}

.game-icon-large {
    font-size: 3.5rem;
    filter: drop-shadow(0 0 10px var(--accent));
}

.game-level-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    background: rgba(0, 0, 0, 0.3);
    padding: 8px 12px;
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.badge-label {
    font-size: 0.65rem;
    color: #666;
    text-transform: uppercase;
}

.badge-number {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--accent);
}

.game-name {
    font-family: 'Orbitron', sans-serif;
    font-size: 1.3rem;
    color: #fff;
    margin-bottom: 10px;
}

.game-description {
    font-size: 0.9rem;
    color: #888;
    line-height: 1.6;
    margin-bottom: 20px;
    min-height: 45px;
}

.game-meta {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
}

.meta-icon {
    font-size: 1rem;
}

.meta-value {
    font-family: 'Rajdhani', sans-serif;
    font-size: 1rem;
    font-weight: 600;
    color: #fff;
}

.xp-color {
    color: #ffd700;
}

.game-play-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: var(--accent);
    padding: 14px 20px;
    border-radius: 12px;
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
    color: #000;
    transition: all 0.3s;
}

.game-card:hover .game-play-btn {
    background: #fff;
    transform: scale(1.02);
}

.arrow {
    font-size: 1.2rem;
    transition: transform 0.3s;
}

.game-card:hover .arrow {
    transform: translateX(5px);
}

/* =============================================
   MENU GRID
   ============================================= */
.menu-section {
    margin-bottom: 50px;
}

.menu-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 15px;
}

.menu-item {
    display: flex;
    align-items: center;
    gap: 20px;
    background: rgba(18, 11, 26, 0.8);
    border: 1px solid rgba(191, 0, 255, 0.15);
    border-radius: 16px;
    padding: 20px;
    text-decoration: none;
    transition: all 0.3s;
}

.menu-item:hover {
    border-color: var(--item-color);
    background: rgba(191, 0, 255, 0.1);
    transform: translateX(10px);
}

.menu-item-icon {
    width: 55px;
    height: 55px;
    background: linear-gradient(135deg, rgba(191, 0, 255, 0.2), rgba(191, 0, 255, 0.05));
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    transition: all 0.3s;
}

.menu-item:hover .menu-item-icon {
    background: var(--item-color);
    transform: scale(1.1);
}

.menu-item-info h4 {
    font-family: 'Rajdhani', sans-serif;
    font-size: 1.1rem;
    color: #fff;
    margin-bottom: 4px;
}

.menu-item-info p {
    font-size: 0.85rem;
    color: #666;
}

/* =============================================
   HOW TO PLAY
   ============================================= */
.howto-section {
    margin-bottom: 50px;
}

.howto-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 20px;
}

.howto-card {
    display: flex;
    gap: 20px;
    background: rgba(18, 11, 26, 0.6);
    border: 1px solid rgba(191, 0, 255, 0.1);
    border-radius: 16px;
    padding: 25px;
    transition: all 0.3s;
}

.howto-card:hover {
    border-color: rgba(191, 0, 255, 0.3);
    transform: translateY(-5px);
}

.howto-number {
    font-family: 'Orbitron', sans-serif;
    font-size: 2rem;
    font-weight: 900;
    color: rgba(191, 0, 255, 0.3);
    line-height: 1;
}

.howto-content h4 {
    font-family: 'Rajdhani', sans-serif;
    font-size: 1.1rem;
    color: #fff;
    margin-bottom: 8px;
}

.howto-content p {
    font-size: 0.85rem;
    color: #888;
    line-height: 1.5;
}

/* =============================================
   FOOTER
   ============================================= */
.main-footer {
    text-align: center;
    padding: 40px 0;
    border-top: 1px solid rgba(191, 0, 255, 0.1);
}

.logout-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: transparent;
    border: 1px solid rgba(255, 68, 68, 0.3);
    color: #ff4444;
    padding: 12px 30px;
    border-radius: 12px;
    font-family: 'Rajdhani', sans-serif;
    font-weight: 600;
    transition: all 0.3s;
    margin-bottom: 20px;
}

.logout-btn:hover {
    background: rgba(255, 68, 68, 0.1);
    border-color: #ff4444;
}

.footer-text {
    font-size: 0.8rem;
    color: #444;
}

/* =============================================
   BACKGROUND EFFECTS
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
    animation: floatParticle linear infinite;
}

@keyframes floatParticle {
    0% { transform: translateY(100vh) rotate(0deg); opacity: 0; }
    10% { opacity: 0.5; }
    90% { opacity: 0.5; }
    100% { transform: translateY(-100px) rotate(720deg); opacity: 0; }
}

/* =============================================
   RESPONSIVE
   ============================================= */
@media (max-width: 768px) {
    .main-header {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .logo-container {
        flex-direction: column;
    }
    
    .stats-header {
        flex-direction: column;
    }
    
    .xp-container {
        width: 100%;
        max-width: none;
    }
    
    .stats-icons {
        gap: 10px;
    }
    
    .stat-icon-item {
        padding: 10px 15px;
    }
    
    .section-subtitle {
        margin-left: 0;
    }
    
    .games-grid {
        grid-template-columns: 1fr;
    }
    
    .menu-grid {
        grid-template-columns: 1fr;
    }
    
    .howto-cards {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 480px) {
    .logo-text h1 {
        font-size: 1.8rem;
    }
    
    .game-card {
        padding: 20px;
    }
    
    .menu-item {
        padding: 15px;
    }
}
</style>

</body>
</html>