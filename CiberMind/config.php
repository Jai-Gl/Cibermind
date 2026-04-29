<?php
if(session_status() === PHP_SESSION_NONE){
    session_start();
}

$host = "localhost";
$user = "root";
$pass = "";
$db = "cibermind";

$conn = new mysqli($host,$user,$pass,$db);

if($conn->connect_error){
    die("Error de conexión: ".$conn->connect_error);
}
?>