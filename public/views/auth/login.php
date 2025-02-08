<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
</head>

<body>
    <div class="login-box">
        <h2>Autorizaciones de Viaje</h2>
        
        <?php
        // Mostrar mensaje de error si existe
        if (isset($_SESSION['error_message'])) {
            echo '<p class="error-message">' . $_SESSION['error_message'] . '</p>';
            // Limpiar el mensaje de error después de mostrarlo
            unset($_SESSION['error_message']);
        }
        ?>
        
        <form method="post" action="public/views/auth/validar_usuario.php">
            <div class="user-box">
                <input type="text" name="usuario" autocomplete="off" required>
                <label>Usuario</label>
            </div>
            <div class="user-box">
                <input type="password" name="contraseña" autocomplete="off" required>
                <label>Contraseña</label>
            </div>
            <button type="submit">Iniciar Sesión</button>
        </form>
    </div>
</body>

</html>
