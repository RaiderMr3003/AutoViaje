<?php
// Iniciar sesión (debe ser lo primero)
session_start();

// Verificar si el usuario no está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirigir al usuario a la página de login si no está logueado
    header("Location: ../../index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home.css">

    <title>AutoViaje</title>
</head>

<body>
    <header>
        <?php
        // Asegúrate de que la ruta de header.php sea correcta
        require 'includes/header.php';
        ?>
    </header>

    <div class="main-container">
        <!-- Panel de Búsqueda -->
        <div class="container">
            <h2>Autorizaciones de Viaje</h2><br>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="nro-crono">N° Cronológico:</label>
                    <input type="text" id="nro-crono" name="nro-crono">
                </div>

                <div class="form-group">
                    <label for="tipo-permiso">Tipo de Permiso:</label>
                    <select id="tipo-permiso" name="tipo-permiso">
                        <option value="">Seleccione</option>
                        
                    </select>
                </div>

                <div class="form-group">
                    <label for="nombre-participante">Nombre del Participante:</label>
                    <input type="text" id="nombre-participante" name="nombre-participante">
                </div>

                <div class="form-group">
                    <label for="nro-control">N° de Control:</label>
                    <input type="text" id="nro-control" name="nro-control">
                </div>

                <div class="form-group">
                    <label>Búsqueda por Fechas:</label>
                    <div class="date-range">
                        <div class="date-field">
                            <label for="fecha-min">Fecha Min</label>
                            <input type="date" id="fecha-min" name="fecha-min">
                        </div>
                        <div class="date-field">
                            <label for="fecha-max">Fecha Max</label>
                            <input type="date" id="fecha-max" name="fecha-max">
                        </div>
                    </div>
                </div>

                <button type="submit">Buscar</button>
            </form>

        </div>

        <!-- Tabla de Resultados -->
        <div class="table-container">
            <table id="resultados">
                <thead>
                    <tr>
                        <th>N° Control</th>
                        <th>N° Cronológico</th>
                        <th>Participantes</th>
                        <th>Tipo de Permiso</th>
                        <th>Fecha</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <?php
    // Asegúrate de que la ruta de footer.php sea correcta
    require 'includes/footer.php';
    ?>
</body>
