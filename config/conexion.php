<?php
$host = 'localhost';
$db = 'autoviaje';
$user = 'root';
$pass = '';
$port = 3306; 

try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log("Error de conexión: " . $e->getMessage(), 3, "errors.log");
    die("Error en la conexión. Contacte al administrador.");
}
?>
