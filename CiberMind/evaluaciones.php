<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();
$hoy = date("Y-m-d");

$categorias = $conn->query("SELECT DISTINCT categoria FROM evaluaciones")->fetch_all(MYSQLI_ASSOC);

$stats_generales = $conn->query("
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN correcta = 1 THEN 1 ELSE 0 END) as correctas
    FROM evaluaciones_usuario 
    WHERE usuario_id = ".$_SESSION["id"]
)->fetch_assoc();

$porcentaje_acierto = $stats_generales['total'] > 0 
    ? round(($stats_generales['correctas'] / $stats_generales['total']) * 100) 
    : 0;

$por_categoria = $conn->query("
    SELECT e.categoria, 
        COUNT(*) as total,
        SUM(CASE WHEN eu.correcta = 1 THEN 1 ELSE 0 END) as correctas
    FROM evaluaciones e
    LEFT JOIN evaluaciones_usuario eu ON e.id = eu.evaluacion_id AND eu.usuario_id = ".$_SESSION["id"]."
    GROUP BY e.categoria
")->fetch_all(MYSQLI_ASSOC);

if(isset($_GET["categoria"])) {
    $cat = $_GET["categoria"];
    $preguntas = $conn->query("SELECT * FROM evaluaciones WHERE categoria='$cat' ORDER BY RAND() LIMIT 5")->fetch_all(MYSQLI_ASSOC);
} elseif(isset($_GET["test"])) {
    $preguntas = $conn->query("SELECT * FROM evaluaciones ORDER BY RAND() LIMIT 10")->fetch_all(MYSQLI_ASSOC);
} else {
    $preguntas = [];
}

if(isset($_POST["respuesta"])) {
    $eval_id = (int)$_POST["eval_id"];
    $respuesta = $_POST["respuesta"];
    
    $eval = $conn->query("SELECT * FROM evaluaciones WHERE id=$eval_id")->fetch_assoc();
    $correcta = ($respuesta == $eval["respuesta_correcta"]);
    
    $conn->query("INSERT INTO evaluaciones_usuario (usuario_id, evaluacion_id, correcta, fecha) 
        VALUES (".$_SESSION["id"].", $eval_id, ".($correcta ? 1 : 0).", NOW())");
    
    if($correcta) {
        $conn->query("UPDATE usuarios SET xp = xp + 5 WHERE id = ".$_SESSION["id"]);
    }
    
    $resultado = [
        "correcta" => $correcta,
        "respuesta_correcta" => $eval["respuesta_correcta"],
        "tu_respuesta" => $respuesta,
        "explicacion" => $eval["explicacion"]
    ];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Evaluaciones - CiberMind</title>
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
            <span class="game-icon-sm">📝</span>
            <h1>Evaluaciones</h1>
        </div>
        <div class="game-stats-mini">
            <span class="mini-stat">📊 <?= $user["nivel"] ?></span>
            <span class="mini-stat">❤️ <?= $user["vidas"] ?></span>
        </div>
    </header>

    <?php if(isset($resultado)): ?>
    <!-- Result Feedback -->
    <div class="result-card <?= $resultado['correcta'] ? 'correct' : 'incorrect' ?> mb-4">
        <div class="result-icon"><?= $resultado['correcta'] ? '✓' : '✗' ?></div>
        <div class="result-content">
            <h3><?= $resultado['correcta'] ? '¡Correcto! +5 XP' : 'Incorrecto' ?></h3>
            <?php if(!$resultado['correcta']): ?>
            <p>La respuesta correcta era: <strong><?= $resultado['respuesta_correcta'] ?></strong></p>
            <?php endif; ?>
            <p class="explanation"><strong>Explicación:</strong> <?= $resultado['explicacion'] ?></p>
        </div>
        <a href="evaluaciones.php" class="btn-next">Siguiente →</a>
    </div>
    <?php endif; ?>

    <?php if(empty($preguntas)): ?>
    <!-- Selection Screen -->
    <div class="evaluation-stats mb-4">
        <div class="stat-card">
            <span class="stat-number"><?= $stats_generales['total'] ?></span>
            <span class="stat-label">Preguntas</span>
        </div>
        <div class="stat-card highlight">
            <span class="stat-number"><?= $porcentaje_acierto ?>%</span>
            <span class="stat-label">Aciertos</span>
        </div>
        <div class="stat-card">
            <span class="stat-number"><?= $stats_generales['correctas'] ?></span>
            <span class="stat-label">Correctas</span>
        </div>
    </div>

    <div class="test-options mb-4">
        <a href="evaluaciones.php?test=1" class="test-option-card">
            <div class="test-icon">🎯</div>
            <h3>Test Completo</h3>
            <p>10 preguntas aleatorias de todas las categorías</p>
            <span class="xp-reward">+50 XP al completar</span>
        </a>
    </div>

    <h3 class="section-title mb-3">Categorías</h3>
    <div class="categories-grid">
        <?php foreach($por_categoria as $cat): ?>
        <?php 
        $cat_porcentaje = $cat['total'] > 0 ? round(($cat['correctas'] / $cat['total']) * 100) : 0;
        $cat_iconos = [
            "Firewalls" => "🛡️",
            "Contraseñas" => "🔑",
            "Malware" => "🦠",
            "Phishing" => "🎣",
            "Cifrado" => "🔐",
            "Redes" => "🌐",
            "Backup" => "💾",
            "Ataques" => "⚔️",
            "OSI" => "📊",
            "OWASP" => "🏆",
            "Herramientas" => "🔧"
        ];
        ?>
        <a href="evaluaciones.php?categoria=<?= urlencode($cat['categoria']) ?>" class="category-card">
            <div class="cat-icon"><?= $cat_iconos[$cat['categoria']] ?? '📚' ?></div>
            <div class="cat-info">
                <h4><?= $cat['categoria'] ?></h4>
                <div class="cat-stats">
                    <span><?= $cat['correctas'] ?>/<?= $cat['total'] ?></span>
                    <span class="cat-percentage <?= $cat_porcentaje >= 70 ? 'good' : ($cat_porcentaje >= 40 ? 'medium' : 'low') ?>">
                        <?= $cat_porcentaje ?>%
                    </span>
                </div>
            </div>
            <div class="progress-bar-mini">
                <div class="progress-fill-mini" style="width: <?= $cat_porcentaje ?>%"></div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>

    <?php else: ?>
    <!-- Question Display -->
    <div class="question-counter mb-3">
        Pregunta <?= count($preguntas) > 1 ? '1 de ' . count($preguntas) : '' ?>
    </div>
    
    <?php foreach($preguntas as $i => $preg): ?>
    <?php if($i == 0): ?>
    <div class="question-card">
        <div class="question-header">
            <span class="category-badge"><?= $preg['categoria'] ?></span>
        </div>
        
        <h3 class="question-text"><?= $preg['pregunta'] ?></h3>
        
        <form method="POST" class="options-form">
            <input type="hidden" name="eval_id" value="<?= $preg['id'] ?>">
            
            <label class="option-label">
                <input type="radio" name="respuesta" value="A" required>
                <span class="option-letter">A</span>
                <span class="option-text"><?= $preg['opcion_a'] ?></span>
            </label>
            
            <label class="option-label">
                <input type="radio" name="respuesta" value="B" required>
                <span class="option-letter">B</span>
                <span class="option-text"><?= $preg['opcion_b'] ?></span>
            </label>
            
            <label class="option-label">
                <input type="radio" name="respuesta" value="C" required>
                <span class="option-letter">C</span>
                <span class="option-text"><?= $preg['opcion_c'] ?></span>
            </label>
            
            <label class="option-label">
                <input type="radio" name="respuesta" value="D" required>
                <span class="option-letter">D</span>
                <span class="option-text"><?= $preg['opcion_d'] ?></span>
            </label>
            
            <button type="submit" class="btn-submit">Responder</button>
        </form>
    </div>
    <?php endif; ?>
    <?php endforeach; ?>
    
    <div class="text-center mt-4">
        <a href="evaluaciones.php" class="btn btn-ghost-outline">← Volver</a>
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

document.querySelectorAll('.option-label').forEach(label => {
    label.addEventListener('click', () => {
        document.querySelectorAll('.option-label').forEach(l => l.classList.remove('selected'));
        label.classList.add('selected');
    });
});
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

.evaluation-stats { display: flex; justify-content: center; gap: 20px; }
.stat-card { display: flex; flex-direction: column; align-items: center; background: rgba(18, 11, 26, 0.9); border: 1px solid rgba(191, 0, 255, 0.2); border-radius: 16px; padding: 20px 35px; }
.stat-card.highlight { border-color: #00ff9f; }
.stat-card.highlight .stat-number { color: #00ff9f; }
.stat-number { font-family: 'Orbitron', sans-serif; font-size: 2rem; font-weight: 900; color: #bf00ff; }
.stat-label { color: #666; font-size: 0.85rem; }

.test-options { margin-bottom: 30px; }
.test-option-card { display: flex; flex-direction: column; align-items: center; background: linear-gradient(135deg, rgba(191, 0, 255, 0.2), rgba(191, 0, 255, 0.05)); border: 2px solid #bf00ff; border-radius: 20px; padding: 30px; text-decoration: none; transition: all 0.3s; }
.test-option-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(191, 0, 255, 0.3); }
.test-icon { font-size: 3rem; margin-bottom: 15px; }
.test-option-card h3 { font-family: 'Orbitron', sans-serif; font-size: 1.3rem; color: #fff; margin-bottom: 8px; }
.test-option-card p { color: #888; font-size: 0.9rem; margin-bottom: 15px; }
.xp-reward { background: rgba(255, 215, 0, 0.2); color: #ffd700; padding: 6px 15px; border-radius: 20px; font-size: 0.85rem; }

.section-title { font-family: 'Orbitron', sans-serif; font-size: 1.2rem; color: #fff; }
.categories-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px; }
.category-card { display: flex; align-items: center; gap: 15px; background: rgba(18, 11, 26, 0.8); border: 1px solid rgba(191, 0, 255, 0.2); border-radius: 16px; padding: 20px; text-decoration: none; transition: all 0.3s; }
.category-card:hover { border-color: #bf00ff; transform: translateX(5px); }
.cat-icon { font-size: 2rem; }
.cat-info { flex: 1; }
.cat-info h4 { font-family: 'Rajdhani', sans-serif; font-size: 1.1rem; color: #fff; margin-bottom: 5px; }
.cat-stats { display: flex; gap: 10px; font-size: 0.85rem; color: #888; }
.cat-percentage { font-weight: 700; }
.cat-percentage.good { color: #00ff9f; }
.cat-percentage.medium { color: #ff8800; }
.cat-percentage.low { color: #ff4444; }
.progress-bar-mini { width: 100%; height: 4px; background: rgba(0,0,0,0.3); border-radius: 4px; margin-top: 8px; }
.progress-fill-mini { height: 100%; background: linear-gradient(90deg, #bf00ff, #00d4ff); border-radius: 4px; transition: width 0.3s; }

.question-card { background: linear-gradient(145deg, #120b1a, #0a0510); border: 1px solid rgba(0, 212, 255, 0.2); border-radius: 24px; padding: 35px; }
.question-header { margin-bottom: 20px; }
.category-badge { display: inline-block; background: linear-gradient(135deg, #00d4ff, #0088ff); padding: 8px 20px; border-radius: 30px; font-size: 0.85rem; font-weight: 600; }
.question-text { font-family: 'Rajdhani', sans-serif; font-size: 1.4rem; color: #fff; margin-bottom: 30px; line-height: 1.5; }
.question-counter { text-align: center; font-family: 'Orbitron', sans-serif; color: #666; font-size: 0.9rem; }

.options-form { display: flex; flex-direction: column; gap: 12px; }
.option-label { display: flex; align-items: center; gap: 15px; background: rgba(18, 11, 26, 0.8); border: 2px solid rgba(191, 0, 255, 0.2); border-radius: 14px; padding: 18px 20px; cursor: pointer; transition: all 0.3s; }
.option-label:hover { border-color: #bf00ff; background: rgba(191, 0, 255, 0.1); }
.option-label.selected { border-color: #00ff9f; background: rgba(0, 255, 159, 0.1); }
.option-label input { display: none; }
.option-letter { width: 40px; height: 40px; background: linear-gradient(135deg, rgba(191, 0, 255, 0.3), rgba(191, 0, 255, 0.1)); border-radius: 10px; display: flex; align-items: center; justify-content: center; font-family: 'Orbitron', sans-serif; font-weight: 700; color: #bf00ff; }
.option-label.selected .option-letter { background: #00ff9f; color: #000; }
.option-text { flex: 1; font-family: 'Rajdhani', sans-serif; font-size: 1rem; color: #fff; }
.btn-submit { width: 100%; margin-top: 20px; padding: 18px; background: linear-gradient(135deg, #00ff9f, #00cc7a); border: none; border-radius: 14px; font-family: 'Orbitron', sans-serif; font-size: 1.1rem; font-weight: 700; color: #000; cursor: pointer; transition: all 0.3s; }
.btn-submit:hover { transform: translateY(-3px); box-shadow: 0 10px 30px rgba(0, 255, 159, 0.4); }

.result-card { display: flex; align-items: center; gap: 20px; border-radius: 20px; padding: 25px; animation: slideUp 0.5s ease-out; }
.result-card.correct { background: linear-gradient(135deg, rgba(0, 255, 159, 0.2), rgba(0, 255, 159, 0.05)); border: 2px solid #00ff9f; }
.result-card.incorrect { background: linear-gradient(135deg, rgba(255, 68, 68, 0.2), rgba(255, 68, 68, 0.05)); border: 2px solid #ff4444; }
.result-icon { font-size: 3rem; }
.result-content { flex: 1; }
.result-content h3 { font-family: 'Orbitron', sans-serif; font-size: 1.3rem; margin-bottom: 8px; }
.result-card.correct .result-content h3 { color: #00ff9f; }
.result-card.incorrect .result-content h3 { color: #ff4444; }
.result-content p { color: #888; font-size: 0.9rem; margin-bottom: 5px; }
.explanation { color: #aaa; margin-top: 10px; }
.btn-next { padding: 12px 25px; background: linear-gradient(135deg, #bf00ff, #8b00ff); border-radius: 12px; color: #fff; text-decoration: none; font-family: 'Rajdhani', sans-serif; font-weight: 600; transition: all 0.3s; }
.btn-next:hover { transform: scale(1.05); }

.particles-bg { position: fixed; top: 0; left: 0; right: 0; bottom: 0; pointer-events: none; z-index: -1; overflow: hidden; }
.particle { position: absolute; width: 4px; height: 4px; border-radius: 50%; opacity: 0.5; animation: floatParticle 10s linear infinite; }
@keyframes floatParticle { 0% { transform: translateY(100vh); opacity: 0; } 10% { opacity: 0.5; } 90% { opacity: 0.5; } 100% { transform: translateY(-100px); opacity: 0; } }
@keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

.btn.btn-ghost-outline { background: transparent; border: 1px solid rgba(191, 0, 255, 0.3); color: #bf00ff; padding: 12px 30px; border-radius: 12px; font-family: 'Rajdhani', sans-serif; font-weight: 600; text-decoration: none; transition: all 0.3s; display: inline-flex; align-items: center; gap: 8px; }
.btn.btn-ghost-outline:hover { background: rgba(191, 0, 255, 0.1); border-color: #bf00ff; }
.mt-4 { margin-top: 20px; }
.text-center { text-align: center; }
.mb-3 { margin-bottom: 15px; }
.mb-4 { margin-bottom: 20px; }

@media (max-width: 768px) {
    .evaluation-stats { gap: 10px; flex-wrap: wrap; }
    .stat-card { padding: 15px 20px; }
    .stat-number { font-size: 1.5rem; }
    .categories-grid { grid-template-columns: 1fr; }
    .result-card { flex-direction: column; text-align: center; }
}
</style>

</body>
</html>