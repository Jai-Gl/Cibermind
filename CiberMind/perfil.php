<?php
session_start(); 
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();

$msg = "";

if(isset($_POST["actualizar"])) {
    $nuevo_user = $_POST["usuario"];
    if($nuevo_user !== $user["username"]) {
        $check = $conn->query("SELECT id FROM usuarios WHERE username='$nuevo_user' AND id != ".$_SESSION["id"]);
        if($check->num_rows > 0) {
            $msg = "⚠️ Usuario ya existe";
        } else {
            $conn->query("UPDATE usuarios SET username='$nuevo_user' WHERE id=".$_SESSION["id"]);
            $_SESSION["user"] = $nuevo_user;
            $msg = "✅ Nombre actualizado!";
            $user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Perfil - CiberMind</title>
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
    --glow: rgba(191,0,255,0.15);
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
h1 { text-align: center; color: var(--primary); margin-bottom: 25px; }
.msg {
    text-align: center; padding: 12px; border-radius: 10px; margin-bottom: 20px;
    background: rgba(191,0,255,0.1); border: 1px solid var(--primary); color: var(--primary);
}
.perfil-card {
    background: var(--bg-light); border: 1px solid #2a1a35;
    border-radius: 20px; padding: 30px 20px; margin-bottom: 25px;
}
.avatar {
    width: 80px; height: 80px;
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
    font-size: 32px; font-weight: bold; color: #fff; margin: 0 auto 20px;
}
.perfil-form label { display: block; font-size: 12px; color: var(--text-dim); margin-bottom: 5px; margin-top: 15px; }
.perfil-form input {
    width: 100%; background: #120a15; border: 2px solid #2a1a35;
    border-radius: 10px; padding: 12px 15px; font-size: 15px; color: var(--text); margin-bottom: 10px;
}
.perfil-form input:focus { outline: none; border-color: var(--primary); }
.perfil-form input:disabled { opacity: 0.5; }
.perfil-form button {
    width: 100%; background: var(--primary); border: none;
    padding: 14px; border-radius: 10px; font-size: 15px; font-weight: bold; color: #fff;
    cursor: pointer; margin-top: 20px;
}
.opciones { display: flex; flex-direction: column; gap: 10px; }
.opcion-btn {
    background: var(--bg-light); border: 1px solid #2a1a35;
    border-radius: 12px; padding: 15px 20px; font-size: 15px; color: var(--text);
    text-align: left; cursor: pointer; transition: all 0.3s; text-decoration: none;
}
.opcion-btn:hover { border-color: var(--primary); }
.opcion-btn.logout { color: #ff4444; }
.opcion-btn.logout:hover { border-color: #ff4444; }
</style>

<div class="page">
    <a href="menu.php" class="back">←</a>
    <h1>👤 Mi Perfil</h1>
    
    <?php if($msg): ?>
    <div class="msg"><?= $msg ?></div>
    <?php endif; ?>
    
    <div class="perfil-card">
        <div class="avatar"><?= strtoupper(substr($user["username"], 0, 1)) ?></div>
        
        <form method="POST" class="perfil-form">
            <label>Nombre de usuario</label>
            <input type="text" name="usuario" value="<?= htmlspecialchars($user["username"]) ?>" required>
            
            <label>Nivel</label>
            <input type="text" value="<?= $user["nivel"] ?>" disabled>
            
            <button type="submit" name="actualizar" value="1">GUARDAR CAMBIOS</button>
        </form>
    </div>
    
    <div class="opciones">
        <button class="opcion-btn">🎵 Activar Sonido</button>
        <button class="opcion-btn">🔔 Notificaciones</button>
        <button class="opcion-btn">❓ Ayuda</button>
        <a href="logout.php" class="opcion-btn logout">🚪 Cerrar Sesión</a>
    </div>
</div>

</body>
</html>