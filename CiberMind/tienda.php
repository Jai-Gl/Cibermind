<?php
include "config.php";
if(!isset($_SESSION["id"])) header("Location: login.php");

$user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();

$items = [
    ["id" => "vida", "nombre" => "❤️ Vida Extra", "precio" => 50, "icono" => "❤️", "desc" => "+1 vida"],
    ["id" => "pista", "nombre" => "💡 Pista Triple", "precio" => 30, "icono" => "💡", "desc" => "3 pistas"],
    ["id" => "doble", "nombre" => "✨ XP Doble", "precio" => 80, "icono" => "✨", "desc" => "Duplica XP"],
    ["id" => "inmune", "nombre" => "🛡️ Escudo", "precio" => 40, "icono" => "🛡️", "desc" => "No pierdes racha"],
    ["id" => "temas", "nombre" => "🎨 Tema Neon", "precio" => 100, "icono" => "🎨", "desc" => "Cambia color"],
];

$msg = "";
if(isset($_POST["comprar"])) {
    $item_id = $_POST["comprar"];
    $item = array_filter($items, fn($i) => $i["id"] === $item_id)[0];
    if($user["score"] >= $item["precio"]) {
        if($item_id === "vida") {
            $conn->query("UPDATE usuarios SET vidas=vidas+1, score=score-".$item["precio"]." WHERE id=".$_SESSION["id"]);
            $msg = "✅ Vida comprado!";
        } else {
            $msg = "⚠️ No disponible aún";
        }
        $user = $conn->query("SELECT * FROM usuarios WHERE id=".$_SESSION["id"])->fetch_assoc();
    } else {
        $msg = "⚠️ Puntos insuficientes";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Tienda - CiberMind</title>
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
h1 { text-align: center; color: var(--primary); margin-bottom: 20px; }
.score-display {
    text-align: center; background: var(--bg-light); border: 1px solid #2a1a35;
    border-radius: 30px; padding: 15px 30px; margin-bottom: 20px; font-size: 16px;
}
.score-display strong { color: var(--primary); font-size: 22px; margin-left: 10px; }
.msg {
    text-align: center; padding: 12px; border-radius: 10px; margin-bottom: 20px;
    background: rgba(191,0,255,0.1); border: 1px solid var(--primary); color: var(--primary);
}
.shop-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
.shop-item {
    background: var(--bg-light); border: 1px solid #2a1a35;
    border-radius: 16px; padding: 20px; text-align: center; transition: all 0.3s;
}
.shop-item:hover { border-color: var(--primary); transform: translateY(-3px); }
.item-icon { font-size: 35px; margin-bottom: 10px; }
.item-name { font-weight: 600; margin-bottom: 5px; }
.item-desc { font-size: 12px; color: var(--text-dim); margin-bottom: 15px; }
.item-btn {
    background: var(--primary); border: none; padding: 10px 20px;
    border-radius: 20px; color: #fff; font-weight: bold; cursor: pointer; transition: all 0.3s;
}
.item-btn:hover:not(:disabled) { transform: scale(1.05); }
.item-btn:disabled { opacity: 0.4; cursor: not-allowed; }
</style>

<div class="page">
    <a href="menu.php" class="back">←</a>
    <h1>🏪 Tienda</h1>
    
    <div class="score-display">
        <span>💎 Tus Puntos:</span>
        <strong><?= $user["score"] ?></strong>
    </div>
    
    <?php if($msg): ?>
    <div class="msg"><?= $msg ?></div>
    <?php endif; ?>
    
    <div class="shop-grid">
        <?php foreach($items as $item): ?>
        <form method="POST" class="shop-item">
            <input type="hidden" name="comprar" value="<?= $item['id'] ?>">
            <div class="item-icon"><?= $item["icono"] ?></div>
            <div class="item-name"><?= $item["nombre"] ?></div>
            <div class="item-desc"><?= $item["desc"] ?></div>
            <button type="submit" class="item-btn" <?= $user["score"] < $item["precio"] ? "disabled" : "" ?>>
                💎 <?= $item["precio"] ?>
            </button>
        </form>
        <?php endforeach; ?>
    </div>
</div>

</body>
</html>