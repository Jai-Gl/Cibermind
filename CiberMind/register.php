<?php
session_start(); 
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

include "config.php";

$error = "";
$success = false;

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user = $_POST["user"];
    $pass = $_POST["pass"];

    if(strlen($user) < 3) {
        $error = "Mínimo 3 caracteres";
    } elseif(strlen($pass) < 4) {
        $error = "Mínimo 4 caracteres";
    } else {
        $check = $conn->query("SELECT id FROM usuarios WHERE username='$user'");
        if($check->num_rows > 0) {
            $error = "Usuario ya existe";
        } else {
            $pass_hash = password_hash($pass, PASSWORD_DEFAULT);
            $conn->query("INSERT INTO usuarios(username,password,xp,nivel,vidas,racha) VALUES('$user','$pass_hash',0,1,3,0)");
            $success = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>CiberMind - Registro</title>
</head>
<body>

<style>
:root {
    --primary: #bf00ff;
    --primary2: #8b00ff;
    --glow: rgba(191, 0, 255, 0.4);
}

* { margin: 0; padding: 0; box-sizing: border-box; }

body {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: 
        radial-gradient(ellipse at 30% 20%, rgba(191,0,255,0.15) 0%, transparent 50%),
        radial-gradient(ellipse at 70% 80%, rgba(139,0,255,0.1) 0%, transparent 40%),
        #08020a;
    font-family: system-ui, sans-serif;
    padding: 20px;
}

.box {
    background: linear-gradient(145deg, #18101a, #0d0812);
    border: 1px solid #2a1a35;
    border-radius: 20px;
    padding: 40px 30px;
    width: 100%;
    max-width: 340px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.6), 0 0 50px var(--glow);
    animation: fadeIn 0.5s ease-out;
}

@keyframes fadeIn {
    from { opacity: 0; transform: scale(0.95); }
    to { opacity: 1; transform: scale(1); }
}

.logo {
    font-size: 50px;
    text-align: center;
    margin-bottom: 10px;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0) rotate(0deg); }
    50% { transform: translateY(-8px) rotate(5deg); }
}

h1 {
    text-align: center;
    font-size: 26px;
    color: var(--primary);
    margin-bottom: 5px;
}

.sub {
    text-align: center;
    color: #999;
    font-size: 14px;
    margin-bottom: 30px;
}

input {
    width: 100%;
    background: #120a15;
    border: 2px solid #2a1a35;
    border-radius: 10px;
    padding: 14px 16px;
    font-size: 14px;
    color: #fff;
    margin-bottom: 14px;
    transition: all 0.3s;
}

input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 20px var(--glow);
}

input::placeholder { color: #555; }

.error {
    background: rgba(255,68,68,0.1);
    border: 1px solid #ff4444;
    color: #ff4444;
    padding: 10px;
    border-radius: 8px;
    font-size: 13px;
    text-align: center;
    margin-bottom: 14px;
}

.success {
    text-align: center;
    padding: 30px 20px;
    background: rgba(191,0,255,0.08);
    border-radius: 12px;
}

.success span {
    font-size: 50px;
    display: block;
    margin-bottom: 15px;
}

.success p {
    color: var(--primary);
    font-size: 18px;
    margin-bottom: 20px;
}

.btn {
    display: inline-block;
    background: linear-gradient(135deg, var(--primary), var(--primary2));
    padding: 14px 30px;
    border-radius: 10px;
    color: #fff;
    font-weight: bold;
    text-decoration: none;
    transition: all 0.3s;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px var(--glow);
}

button {
    width: 100%;
    background: linear-gradient(135deg, var(--primary), var(--primary2));
    border: none;
    padding: 14px;
    font-size: 15px;
    font-weight: bold;
    border-radius: 10px;
    color: #fff;
    cursor: pointer;
    margin-top: 8px;
    transition: all 0.3s;
}

button:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 30px var(--glow);
}

.link {
    text-align: center;
    margin-top: 25px;
    padding-top: 20px;
    border-top: 1px solid #2a1a35;
    color: #999;
    font-size: 14px;
}

.link a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
}

.link a:hover {
    text-decoration: underline;
}
</style>

<div class="box">
    <div class="logo">🚀</div>
    <h1>CYBERMIND</h1>
    <p class="sub">Crea tu cuenta</p>

    <?php if($success): ?>
    <div class="success">
        <span>✅</span>
        <p>¡Cuenta creada!</p>
        <a href="login.php" class="btn">IR AL LOGIN</a>
    </div>
    <?php else: ?>

    <form method="POST">
        <input type="text" name="user" placeholder="Usuario" required minlength="3">
        <input type="password" name="pass" placeholder="Contraseña" required minlength="4">
        
        <?php if($error): ?>
        <div class="error">⚠️ <?= $error ?></div>
        <?php endif; ?>

        <button type="submit">REGISTRARSE</button>
    </form>

    <p class="link">¿Ya tienes cuenta? <a href="login.php">Inicia sesión</a></p>
    <?php endif; ?>
</div>

</body>
</html>