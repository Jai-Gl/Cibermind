<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();

if(isset($_POST["agregar"])) {
    $amigo = $_POST["buscar"];
    $buscar = $conn->query("SELECT id FROM usuarios WHERE username='$amigo' AND id != ".$_SESSION["id"])->fetch_assoc();
    if($buscar) {
        $check = $conn->query("SELECT id FROM amigos WHERE (usuario_id=".$_SESSION["id"]." AND amigo_id=".$buscar["id"].") OR (usuario_id=".$buscar["id"]." AND amigo_id=".$_SESSION["id"].")")->fetch_assoc();
        if(!$check) {
            $conn->query("INSERT INTO amigos (usuario_id, amigo_id, estado) VALUES (".$_SESSION["id"].", ".$buscar["id"].", 'pendiente')");
            $msg = "✅ Solicitud enviada!";
        } else {
            $msg = "⚠️ Ya son amigos o solicitud pendiente";
        }
    } else {
        $msg = "⚠️ Usuario no encontrado";
    }
}

$amigos = $conn->query("
    SELECT u.username, u.nivel, u.score, a.estado 
    FROM amigos a 
    JOIN usuarios u ON (a.amigo_id = u.id AND a.usuario_id = ".$_SESSION["id"].") 
    OR (a.usuario_id = u.id AND a.amigo_id = ".$_SESSION["id"].")
    WHERE a.estado = 'aceptado'
")->fetch_all(MYSQLI_ASSOC);

$pendientes = $conn->query("
    SELECT u.username, a.id as solicitud_id
    FROM amigos a
    JOIN usuarios u ON a.usuario_id = u.id
    WHERE a.amigo_id = ".$_SESSION["id"]." AND a.estado = 'pendiente'
")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
<title>Amigos - CiberMind</title>
</head>
<body>

<style>
:root {
    --primary: #bf00ff;
    --primary-dark: #8b00ff;
    --bg: #08020a;
    --bg-light: #18101a;
    --text: #ffffff;
    --text-dim: #999999;
}
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    background: radial-gradient(ellipse at 30% 20%, rgba(191,0,255,0.15) 0%, transparent 50%),
                radial-gradient(ellipse at 70% 80%, rgba(139,0,255,0.1) 0%, transparent 40%), #08020a;
    min-height: 100vh; font-family: system-ui, sans-serif; padding: 20px;
}
.page { max-width: 600px; margin: 0 auto; }
.back {
    position: fixed; top: 20px; left: 20px;
    width: 45px; height: 45px;
    background: var(--bg-light); border: 1px solid #2a1a35;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    color: var(--primary); text-decoration: none; font-size: 18px;
}
h1 { text-align: center; color: var(--primary); margin-bottom: 20px; }
.search-box {
    display: flex; gap: 10px; margin-bottom: 25px;
}
.search-box input {
    flex: 1;
    background: var(--bg-light); border: 1px solid #2a1a35;
    border-radius: 10px; padding: 14px 16px; font-size: 14px; color: var(--text);
}
.search-box button {
    background: var(--primary); border: none;
    padding: 14px 20px; border-radius: 10px; color: #fff; font-weight: bold; cursor: pointer;
}
.msg { text-align: center; padding: 12px; border-radius: 10px; margin-bottom: 15px;
    background: rgba(191,0,255,0.1); border: 1px solid var(--primary); color: var(--primary); font-size: 14px; }
h2 { font-size: 14px; color: var(--text-dim); margin-bottom: 12px; text-transform: uppercase; }
.list { display: flex; flex-direction: column; gap: 10px; margin-bottom: 25px; }
.amigo {
    display: flex; align-items: center; gap: 12px;
    background: var(--bg-light); border: 1px solid #2a1a35;
    border-radius: 12px; padding: 14px;
}
.amigo-avatar {
    width: 40px; height: 40px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-size: 16px; font-weight: bold; color: #fff;
}
.amigo-info { flex: 1; }
.amigo-name { font-weight: 600; }
.amigo-stats { font-size: 12px; color: var(--text-dim); }
</style>

<div class="page">
    <a href="menu.php" class="back">←</a>
    <h1>👥 Amigos Cyber</h1>
    
    <form method="POST" class="search-box">
        <input type="text" name="buscar" placeholder="Buscar usuario..." required>
        <button type="submit" name="agregar" value="1">🔍</button>
    </form>
    
    <?php if(isset($msg)): ?>
    <div class="msg"><?= $msg ?></div>
    <?php endif; ?>
    
    <?php if(count($pendientes) > 0): ?>
    <h2>📩 Solicitudes</h2>
    <div class="list">
        <?php foreach($pendientes as $p): ?>
        <div class="amigo">
            <div class="amigo-avatar"><?= strtoupper(substr($p["username"], 0, 1)) ?></div>
            <div class="amigo-info">
                <div class="amigo-name"><?= htmlspecialchars($p["username"]) ?></div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <h2>👤 Mis Amigos (<?= count($amigos) ?>)</h2>
    <div class="list">
        <?php if(count($amigos) == 0): ?>
        <p style="color: var(--text-dim); text-align: center;">Aún no tienes amigos agregados</p>
        <?php else: ?>
        <?php foreach($amigos as $a): ?>
        <div class="amigo">
            <div class="amigo-avatar"><?= strtoupper(substr($a["username"], 0, 1)) ?></div>
            <div class="amigo-info">
                <div class="amigo-name"><?= htmlspecialchars($a["username"]) ?></div>
                <div class="amigo-stats">Nivel <?= $a["nivel"] ?> • 💎 <?= $a["score"] ?></div>
            </div>
        </div>
        <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>