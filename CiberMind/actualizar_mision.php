<?php
include "config.php";
if(!isset($_SESSION["id"])) exit;

$tipo = $_GET["tipo"] ?? "";
$hoy = date("Y-m-d");

$todas_misiones = [
    "trivia" => ["objetivo" => 5, "recompensa" => 15],
    "parejas" => ["objetivo" => 2, "recompensa" => 20],
    "ordenar" => ["objetivo" => 1, "recompensa" => 25],
    "snake" => ["objetivo" => 5, "recompensa" => 30],
    "evaluacion" => ["objetivo" => 3, "recompensa" => 35],
    "racha" => ["objetivo" => 3, "recompensa" => 40],
    "conceptos" => ["objetivo" => 10, "recompensa" => 50],
    "nivel" => ["objetivo" => 1, "recompensa" => 60],
    "score" => ["objetivo" => 500, "recompensa" => 45],
    "juego_total" => ["objetivo" => 5, "recompensa" => 35]
];

if(!isset($todas_misiones[$tipo])) {
    echo json_encode(["error" => "Tipo inválido"]);
    exit;
}

$mision_info = $todas_misiones[$tipo];

// Buscar la misión activa de este tipo
$mision = $conn->query("
    SELECT * FROM misiones_diarias 
    WHERE usuario_id = ".$_SESSION["id"]." AND tipo = '$tipo' AND fecha = '$hoy' AND completado = 0
")->fetch_assoc();

if($mision) {
    $nuevo_progreso = $mision["progreso"] + 1;
    
    if($nuevo_progreso >= $mision["objetivo"]) {
        // Completar misión
        $conn->query("UPDATE misiones_diarias SET progreso = $nuevo_progreso, completado = 1 WHERE id = ".$mision["id"]);
        
        // Dar recompensa
        $conn->query("UPDATE usuarios SET score = score + ".$mision["recompensa"]." WHERE id = ".$_SESSION["id"]);
        
        // Crear nueva misión disponible
        $completadas = $conn->query("
            SELECT tipo FROM misiones_diarias 
            WHERE usuario_id = ".$_SESSION["id"]." AND fecha = '$hoy' AND completado = 1
        ")->fetch_all(MYSQLI_ASSOC);
        $completadas_tipos = array_column($completadas, 'tipo');
        
        foreach($todas_misiones as $t => $info) {
            if(!in_array($t, $completadas_tipos)) {
                $conn->query("INSERT INTO misiones_diarias (usuario_id, tipo, objetivo, progreso, recompensa, fecha) VALUES 
                    (".$_SESSION["id"].", '$t', ".$info["objetivo"].", 0, ".$info["recompensa"].", '$hoy')");
                echo json_encode([
                    "completado" => true,
                    "recompensa" => $mision["recompensa"],
                    "nueva_mision" => $t
                ]);
                break;
            }
        }
    } else {
        // Solo actualizar progreso
        $conn->query("UPDATE misiones_diarias SET progreso = $nuevo_progreso WHERE id = ".$mision["id"]);
        echo json_encode([
            "progreso" => $nuevo_progreso,
            "objetivo" => $mision["objetivo"]
        ]);
    }
} else {
    // No existe esta misión activa, crearla
    $conn->query("INSERT INTO misiones_diarias (usuario_id, tipo, objetivo, progreso, recompensa, fecha) VALUES 
        (".$_SESSION["id"].", '$tipo', ".$mision_info["objetivo"].", 1, ".$mision_info["recompensa"].", '$hoy')");
    
    $mision_creada = $conn->query("SELECT * FROM misiones_diarias WHERE usuario_id = ".$_SESSION["id"]." AND tipo = '$tipo' AND fecha = '$hoy' ORDER BY id DESC LIMIT 1")->fetch_assoc();
    
    if($mision_creada["progreso"] >= $mision_creada["objetivo"]) {
        $conn->query("UPDATE misiones_diarias SET completado = 1 WHERE id = ".$mision_creada["id"]);
        $conn->query("UPDATE usuarios SET score = score + ".$mision_info["recompensa"]." WHERE id = ".$_SESSION["id"]);
        echo json_encode([
            "completado" => true,
            "recompensa" => $mision_info["recompensa"]
        ]);
    } else {
        echo json_encode([
            "progreso" => 1,
            "objetivo" => $mision_info["objetivo"]
        ]);
    }
}
?>