<?php
// Iniciar sesión
session_start();

// Incluir la conexión a la base de datos
require_once '../../../config/conexion.php'; // Asegúrate de que este archivo esté en el lugar correcto

// Obtener los datos del formulario
$user = $_POST['usuario']; // El nombre de usuario ingresado
$pass = $_POST['contraseña']; // La contraseña ingresada

// Preparar la consulta SQL para verificar el usuario
$sql = "SELECT * FROM usuarios WHERE loginusuario = :usuario"; // Se usa el campo 'loginusuario' para el login
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':usuario', $user, PDO::PARAM_STR);
$stmt->execute();

// Verificar si el usuario existe
if ($stmt->rowCount() > 0) {
    // El usuario existe, ahora verificamos la contraseña
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $stored_password = $row['password']; // Recuperamos la contraseña almacenada de la base de datos

    // Comparar la contraseña ingresada con la almacenada en la base de datos
    if ($pass === $stored_password) {
        // Contraseña correcta, establecer sesión
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $user;
        $_SESSION['userid'] = $row['idusuario'];

        // Redirigir al usuario a la página principal
        header("Location: ../home.php");
        exit; // Detener la ejecución después de la redirección
    } else {
        // Contraseña incorrecta
        $_SESSION['error_message'] = "Contraseña incorrecta.";
        header("Location: ../../index.php");
        exit; // Detener la ejecución después de la redirección
    }
} else {
    // El usuario no existe
    $_SESSION['error_message'] = "Usuario no encontrado.";
    header("Location: ../../index.php");
    exit; // Detener la ejecución después de la redirección
}
?>
