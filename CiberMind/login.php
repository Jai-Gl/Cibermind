<?php
session_start(); 
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

include "config.php";

$error = "";

if($_SERVER["REQUEST_METHOD"] == "POST"){
    $user = $_POST["user"];
    $pass = $_POST["pass"];

    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username=?");
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();
    $u = $result->fetch_assoc();

    if($u && password_verify($pass, $u["password"])){
        $_SESSION["id"] = $u["id"];
        $_SESSION["user"] = $u["username"];
        header("Location: menu.php");
        exit();
    }else{
        $error = "Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>CiberMind - Login</title>
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
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-8px); }
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
    <div class="logo">⚡</div>
    <h1>CYBERMIND</h1>
    <p class="sub">Inicia sesión</p>

    <form method="POST">
        <input type="text" name="user" placeholder="Usuario" required>
        <input type="password" name="pass" placeholder="Contraseña" required>
        
        <?php if($error): ?>
        <div class="error">⚠️ <?= $error ?></div>
        <?php endif; ?>

        <button type="submit">INGRESAR</button>
    </form>

    <p class="link">¿No tienes cuenta? <a href="register.php">Regístrate</a></p>
</div>

</body>
</html>