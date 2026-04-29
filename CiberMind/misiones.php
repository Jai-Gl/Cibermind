<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();
$hoy = date("Y-m-d");

// Todas las misiones disponibles
$todas_misiones = [
    ["tipo" => "trivia", "nombre" => "Trivia Master", "desc" => "Responde 5 preguntas correctamente", "objetivo" => 5, "recompensa" => 15, "icono" => "🎯"],
    ["tipo" => "parejas", "nombre" => "Memoria Sharp", "desc" => "Gana 2 partidas de Parejas", "objetivo" => 2, "recompensa" => 20, "icono" => "🃏"],
    ["tipo" => "ordenar", "nombre" => "Orden Perfecto", "desc" => "Gana 1 partida de Ordenar", "objetivo" => 1, "recompensa" => 25, "icono" => "📊"],
    ["tipo" => "snake", "nombre" => "Snake Learner", "desc" => "Recoge 5 datos en Snake", "objetivo" => 5, "recompensa" => 30, "icono" => "🐍"],
    ["tipo" => "evaluacion", "nombre" => "Evaluación", "desc" => "Completa 3 evaluaciones", "objetivo" => 3, "recompensa" => 35, "icono" => "📝"],
    ["tipo" => "racha", "nombre" => "Racha Imparable", "desc" => "Consigue 3 de racha", "objetivo" => 3, "recompensa" => 40, "icono" => "🔥"],
    ["tipo" => "conceptos", "nombre" => "Bibliotecario", "desc" => "Aprende 10 conceptos nuevos", "objetivo" => 10, "recompensa" => 50, "icono" => "📚"],
    ["tipo" => "nivel", "nombre" => "Ascenso", "desc" => "Sube de nivel", "objetivo" => 1, "recompensa" => 60, "icono" => "⬆️"],
    ["tipo" => "score", "nombre" => "Coleccionista", "desc" => "Acumula 500 puntos", "objetivo" => 500, "recompensa" => 45, "icono" => "💎"],
    ["tipo" => "juego_total", "nombre" => "Veterano", "desc" => "Juega 5 partidas en total", "objetivo" => 5, "recompensa" => 35, "icono" => "🎮"]
];

// Obtener misión actual del usuario
$mision_actual = $conn->query("
    SELECT * FROM misiones_diarias 
    WHERE usuario_id = ".$_SESSION["id"]." AND fecha = '$hoy' AND completado = 0
    ORDER BY id ASC LIMIT 1
")->fetch_assoc();

// Si no hay misión activa, asignar una nueva
if(!$mision_actual) {
    // Limpiar misiones antiguas
    $conn->query("DELETE FROM misiones_diarias WHERE fecha < '$hoy'");
    
    // Buscar una misión que no esté completada hoy
    $completadas = $conn->query("
        SELECT tipo FROM misiones_diarias 
        WHERE usuario_id = ".$_SESSION["id"]." AND fecha = '$hoy' AND completado = 1
    ")->fetch_all(MYSQLI_ASSOC);
    $completadas_tipos = array_column($completadas, 'tipo');
    
    // Buscar misión disponible
    $disponible = null;
    foreach($todas_misiones as $m) {
        if(!in_array($m["tipo"], $completadas_tipos)) {
            $disponible = $m;
            break;
        }
    }
    
    // Si todas completadas, mostrar mensaje de éxito
    $todas_completadas = count($completadas_tipos) >= count($todas_misiones);
    
    if($disponible && !$todas_completadas) {
        $conn->query("INSERT INTO misiones_diarias (usuario_id, tipo, objetivo, progreso, recompensa, fecha) VALUES 
            (".$_SESSION["id"].", '{$disponible['tipo']}', {$disponible['objetivo']}, 0, {$disponible['recompensa']}, '$hoy')");
        $mision_actual = $conn->query("SELECT * FROM misiones_diarias WHERE usuario_id = ".$_SESSION["id"]." AND fecha = '$hoy' AND completado = 0 ORDER BY id DESC LIMIT 1")->fetch_assoc();
    }
}

// Obtener progreso general
$completadas_hoy = $conn->query("SELECT COUNT(*) as c FROM misiones_diarias WHERE usuario_id = ".$_SESSION["id"]." AND fecha = '$hoy' AND completado = 1")->fetch_assoc()['c'];
$total_misiones = count($todas_misiones);

// Info de la misión actual
$mision_info = null;
if($mision_actual) {
    foreach($todas_misiones as $m) {
        if($m["tipo"] == $mision_actual["tipo"]) {
            $mision_info = $m;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Misiones - CiberMind</title>
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
            <span class="game-icon-sm">🎯</span>
            <h1>Misiones Diarias</h1>
        </div>
        <div class="game-stats-mini">
            <span class="mini-stat">❤️ <?= $user["vidas"] ?></span>
            <span class="mini-stat">📊 <?= $user["nivel"] ?></span>
        </div>
    </header>

    <?php if($todas_completadas): ?>
    <!-- Todas Completadas -->
    <div class="all-complete mb-4">
        <div class="complete-icon">🎉</div>
        <h2>¡Felicidades!</h2>
        <p>Has completado todas las misiones del día</p>
        <div class="rewards-earned">
            <?php 
            $total_recompensa = $conn->query("SELECT SUM(recompensa) as t FROM misiones_diarias WHERE usuario_id = ".$_SESSION["id"]." AND fecha = '$hoy'")->fetch_assoc()['t'];
            ?>
            <span class="reward-total">+<?= $total_recompensa ?> 💎 ganados hoy</span>
        </div>
    </div>
    
    <?php else: ?>
    
    <!-- Progreso General -->
    <div class="daily-progress mb-4">
        <div class="progress-header">
            <span class="progress-title">Progreso de Hoy</span>
            <span class="progress-count"><?= $completadas_hoy ?>/<?= $total_misiones ?></span>
        </div>
        <div class="progress-bar-full">
            <div class="progress-fill-full" style="width: <?= ($completadas_hoy / $total_misiones) * 100 ?>%"></div>
        </div>
    </div>

    <!-- Misión Actual -->
    <?php if($mision_actual && $mision_info): ?>
    <div class="current-mission mb-4">
        <div class="mission-badge">MISIÓN ACTUAL</div>
        
        <div class="mission-card">
            <div class="mission-icon-large"><?= $mision_info["icono"] ?></div>
            
            <div class="mission-details">
                <h3><?= $mision_info["nombre"] ?></h3>
                <p><?= $mision_info["desc"] ?></p>
            </div>
            
            <div class="mission-progress-section">
                <div class="progress-info">
                    <span>Progreso</span>
                    <span><?= $mision_actual["progreso"] ?>/<?= $mision_actual["objetivo"] ?></span>
                </div>
                <div class="progress-bar-container">
                    <div class="progress-bar-fill" style="width: <?= ($mision_actual["progreso"] / $mision_actual["objetivo"]) * 100 ?>%"></div>
                </div>
            </div>
            
            <div class="mission-reward">
                <span class="reward-icon">💎</span>
                <span class="reward-value">+<?= $mision_info["recompensa"] ?></span>
            </div>
        </div>
        
        <p class="mission-hint">Completa esta misión para desbloquear la siguiente</p>
    </div>
    <?php endif; ?>
    
    <?php endif; ?>

    <!-- Historial de Completadas -->
    <?php if($completadas_hoy > 0): ?>
    <div class="completed-missions mb-4">
        <h3 class="section-subtitle">✅ Completadas</h3>
        <div class="completed-list">
            <?php 
            $completadas_lista = $conn->query("
                SELECT m.*, t.icono, t.nombre 
                FROM misiones_diarias m
                LEFT JOIN (
                    SELECT 'trivia' as tipo, '🎯' as icono, 'Trivia Master' as nombre UNION ALL
                    SELECT 'parejas', '🃏', 'Memoria Sharp' UNION ALL
                    SELECT 'ordenar', '📊', 'Orden Perfecto' UNION ALL
                    SELECT 'snake', '🐍', 'Snake Learner' UNION ALL
                    SELECT 'evaluacion', '📝', 'Evaluación' UNION ALL
                    SELECT 'racha', '🔥', 'Racha Imparable' UNION ALL
                    SELECT 'conceptos', '📚', 'Bibliotecario' UNION ALL
                    SELECT 'nivel', '⬆️', 'Ascenso' UNION ALL
                    SELECT 'score', '💎', 'Coleccionista' UNION ALL
                    SELECT 'juego_total', '🎮', 'Veterano'
                ) t ON m.tipo = t.tipo
                WHERE m.usuario_id = ".$_SESSION["id"]." AND m.fecha = '$hoy' AND m.completado = 1
            ")->fetch_all(MYSQLI_ASSOC);
            ?>
            <?php foreach($completadas_lista as $c): ?>
            <div class="completed-item">
                <span class="completed-icon"><?= $c["icono"] ?? "✅" ?></span>
                <span class="completed-name"><?= $c["nombre"] ?></span>
                <span class="completed-reward">+<?= $c["recompensa"] ?> 💎</span>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

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
.game-header { display: flex; align-items: center; justify-content: space-between; padding: 20px 0; margin-bottom: 30px; }
.back-btn { width: 50px; height: 50px; background: rgba(18, 11, 26, 0.8); border: 1px solid rgba(191, 0, 255, 0.3); border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.3rem; color: #bf00ff; transition: all 0.3s; }
.back-btn:hover { background: rgba(191, 0, 255, 0.2); transform: translateX(-5px); }
.game-title-simple { display: flex; align-items: center; gap: 15px; }
.game-icon-sm { font-size: 2.5rem; }
.game-title-simple h1 { font-family: 'Orbitron', sans-serif; font-size: 1.8rem; color: #fff; }
.game-stats-mini { display: flex; gap: 10px; }
.mini-stat { padding: 8px 14px; background: rgba(18, 11, 26, 0.8); border: 1px solid rgba(191, 0, 255, 0.2); border-radius: 10px; font-family: 'Rajdhani', sans-serif; font-size: 0.9rem; }

.all-complete { text-align: center; background: linear-gradient(135deg, rgba(0, 255, 159, 0.2), rgba(0, 255, 159, 0.05)); border: 2px solid #00ff9f; border-radius: 24px; padding: 40px; animation: slideUp 0.5s ease-out; }
.complete-icon { font-size: 5rem; margin-bottom: 20px; }
.all-complete h2 { font-family: 'Orbitron', sans-serif; font-size: 2rem; color: #00ff9f; margin-bottom: 10px; }
.all-complete p { color: #888; margin-bottom: 20px; }
.reward-total { background: rgba(255, 215, 0, 0.2); color: #ffd700; padding: 10px 25px; border-radius: 30px; font-family: 'Orbitron', sans-serif; font-size: 1.1rem; }

.daily-progress { background: rgba(18, 11, 26, 0.8); border: 1px solid rgba(191, 0, 255, 0.2); border-radius: 16px; padding: 20px; }
.progress-header { display: flex; justify-content: space-between; margin-bottom: 12px; }
.progress-title { font-family: 'Rajdhani', sans-serif; color: #888; }
.progress-count { font-family: 'Orbitron', sans-serif; color: #bf00ff; }
.progress-bar-full { height: 8px; background: rgba(0, 0, 0, 0.5); border-radius: 10px; overflow: hidden; }
.progress-fill-full { height: 100%; background: linear-gradient(90deg, #bf00ff, #00d4ff); border-radius: 10px; transition: width 0.5s; }

.current-mission { }
.mission-badge { display: inline-block; background: linear-gradient(135deg, #ffd700, #ff8800); color: #000; padding: 8px 20px; border-radius: 30px; font-family: 'Orbitron', sans-serif; font-size: 0.85rem; font-weight: 700; margin-bottom: 20px; }
.mission-card { background: linear-gradient(145deg, #120b1a, #0a0510); border: 2px solid #bf00ff; border-radius: 24px; padding: 30px; text-align: center; position: relative; overflow: hidden; }
.mission-card::before { content: ''; position: absolute; top: -50%; left: -50%; width: 200%; height: 200%; background: radial-gradient(circle, rgba(191, 0, 255, 0.1) 0%, transparent 70%); pointer-events: none; }
.mission-icon-large { font-size: 4rem; margin-bottom: 20px; }
.mission-details { margin-bottom: 25px; }
.mission-details h3 { font-family: 'Orbitron', sans-serif; font-size: 1.5rem; color: #fff; margin-bottom: 8px; }
.mission-details p { color: #888; font-size: 1rem; }
.mission-progress-section { margin-bottom: 25px; }
.progress-info { display: flex; justify-content: space-between; margin-bottom: 8px; font-size: 0.9rem; color: #888; }
.progress-bar-container { height: 12px; background: rgba(0, 0, 0, 0.5); border-radius: 10px; overflow: hidden; border: 1px solid rgba(191, 0, 255, 0.2); }
.progress-bar-fill { height: 100%; background: linear-gradient(90deg, #00ff9f, #00d4ff); border-radius: 10px; transition: width 0.5s; box-shadow: 0 0 15px rgba(0, 255, 159, 0.5); }
.mission-reward { display: inline-flex; align-items: center; gap: 10px; background: rgba(255, 215, 0, 0.2); padding: 12px 30px; border-radius: 30px; }
.reward-icon { font-size: 1.5rem; }
.reward-value { font-family: 'Orbitron', sans-serif; font-size: 1.3rem; font-weight: 700; color: #ffd700; }
.mission-hint { text-align: center; color: #666; font-size: 0.85rem; margin-top: 15px; }

.completed-missions { }
.section-subtitle { font-family: 'Rajdhani', sans-serif; font-size: 1rem; color: #00ff9f; margin-bottom: 15px; }
.completed-list { display: flex; flex-direction: column; gap: 10px; }
.completed-item { display: flex; align-items: center; gap: 15px; background: rgba(0, 255, 159, 0.1); border: 1px solid rgba(0, 255, 159, 0.2); border-radius: 12px; padding: 12px 18px; }
.completed-icon { font-size: 1.3rem; }
.completed-name { flex: 1; color: #fff; font-family: 'Rajdhani', sans-serif; }
.completed-reward { color: #ffd700; font-weight: 600; }

.particles-bg { position: fixed; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; z-index: -1; overflow: hidden; }
.particle { position: absolute; width: 4px; height: 4px; border-radius: 50%; opacity: 0.5; animation: floatParticle 10s linear infinite; }
@keyframes floatParticle { 0% { transform: translateY(100vh); opacity: 0; } 10% { opacity: 0.5; } 90% { opacity: 0.5; } 100% { transform: translateY(-100px); opacity: 0; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

.btn.btn-ghost-outline { background: transparent; border: 1px solid rgba(191, 0, 255, 0.3); color: #bf00ff; padding: 12px 30px; border-radius: 12px; font-family: 'Rajdhani', sans-serif; font-weight: 600; text-decoration: none; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
.btn.btn-ghost-outline:hover { background: rgba(191, 0, 255, 0.1); border-color: #bf00ff; }
.mt-4 { margin-top: 20px; }
.mb-4 { margin-bottom: 20px; }
.text-center { text-align: center; }

@media (max-width: 768px) {
    .game-header { flex-wrap: wrap; gap: 15px; }
    .game-stats-mini { order: 3; width: 100%; justify-content: center; }
}
</style>

</body>
</html>