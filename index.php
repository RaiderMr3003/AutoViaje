<?php
// Iniciar sesión (si no lo has hecho en login.php)
session_start();

// Verificar si el usuario ya está logueado
if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true) {
    // Redirigir al usuario a la página principal o dashboard
    header("Location: public/views/home.php"); // Cambia "autoviaje.php" por la página a la que quieres redirigir
    exit;
}

// Incluir el archivo de login solo si el usuario no está logueado
require_once 'public/views/auth/login.php';
?>