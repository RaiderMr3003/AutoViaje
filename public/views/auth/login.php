<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="RaiderMr - Máximo Mercado Racchumí">
    <meta name="generator" content="AutoViaje">
    <title>Iniciar Sesión</title>
    <link href="public/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-signin {
            max-width: 330px;
            padding: 1rem;
        }
    </style>
</head>

<body class="d-flex align-items-center vh-100 bg-light">
    <main class="form-signin w-100 m-auto">
        <form method="post" action="public/views/auth/validar_usuario.php">
            <h1 class="h3 mb-3 fw-normal text-center">Autorizaciones de Viaje</h1>
            <?php
            // Mostrar mensaje de error si existe
            if (isset($_SESSION['error_message'])) {
                echo '<p class="error-message">' . $_SESSION['error_message'] . '</p>';
                // Limpiar el mensaje de error después de mostrarlo
                unset($_SESSION['error_message']);
            }
            ?>

            <div class="form-floating mb-3">
                <input type="text" name="usuario" class="form-control" id="usuario" placeholder="Usuario" required autocomplete="off">
                <label for="usuario">Usuario</label>
            </div>
            <div class="form-floating mb-3">
                <input type="password" name="contraseña" class="form-control" id="contraseña" placeholder="Contraseña" required autocomplete="off">
                <label for="contraseña">Contraseña</label>
            </div>

            <button class="btn btn-primary w-100 py-2" type="submit">Iniciar Sesión</button>
            <p class="mt-5 mb-3 text-body-secondary text-center">&copy; 2025</p>
        </form>
    </main>

    <script src="public/bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>