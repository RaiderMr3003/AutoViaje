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
    <link rel="stylesheet" href="css/create_permi.css">

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
        <!-- Panel de Creación de Permiso -->
        <div class="container">
            <h1>Autorizaciones de Viaje</h1>
            <form action="#" method="post">
                <ul>
                    <li>
                        <label for="nro-control">N° Control:</label>
                        <input type="text" id="id_autorizacion" name="id_autorizacion">
                    </li>

                    <li>
                        <label for="tipo-permiso">Tipo de Permiso:</label>
                        <select id="tipo-permiso" name="tipo-permiso">
                            <option value="">Seleccione</option>
                            
                        </select>
                    </li>

                    <li>
                        <label for="fecha-ingreso">Fecha de ingreso:</label>
                        <input type="date" id="fecha-ingreso" name="fecha-ingreso" disabled>
                    </li>

                    <li>
                        <label for="Viaja-a">Viaja a:</label>
                        <input type="text" id="viaja-a" name="viaja-a">
                    </li>

                    <li>
                        <label for="documento_acompañante">Datos del acompañante:</label>
                        <select name="documento_acompañante" id="documento_acompañante">
                            <option value="">Seleccione tipo de documento</option>
                            
                        </select>
                        <input type="text" id="numdoc_acompañante" name="numdoc_acompañante" placeholder="Nro. Documento">
                    </li>
                    <li>
                        <input type="text" id="nombre-acompañante" name="nombre-acompañante" placeholder="Nombres">
                    </li>
                    <li>
                        <input type="text" id="apellido-acompañante" name="apellido-acompañante" placeholder="Apellidos">
                    </li>
                    <li>
                        <label for="documento_responsable">Datos del responsable:</label>
                        <select name="documento_responsable" id="documento_responsable">
                            <option value="">Seleccione tipo de documento</option>
                            
                        </select>
                        <input type="text" id="numdoc_responsable" name="numdoc_responsable" placeholder="Nro. Documento">
                    </li>
                    <li>
                        <input type="text" id="nombre-responsable" name="nombre-responsable" placeholder="Nombres">
                    </li>
                    <li>
                        <input type="text" id="apellido-responsable" name="apellido-responsable" placeholder="Apellidos">
                    </li>
                    <li>
                        <label for="tipo_transporte">Medio de transporte:</label>
                        <select name="tipo_transporte" id="tipo_transporte">
                            <option value="">Seleccione</option>
                            
                        </select>
                    </li>
                    <li>
                        <label for="agencia-de-transporte">Agencia de transporte:</label>
                        <input type="text" id="agencia-de-transporte" name="agencia-de-transporte">
                    </li>
                    <li>
                        <label for="desde">Desde:</label>
                        <input type="date" id="desde" name="desde">
                    </li>
                    <li>
                        <label for="hasta">Hasta:</label>
                        <input type="date" id="hasta" name="hasta">
                    </li>
                    <li>
                        <label for="observaciones">Observaciones:</label>
                        <textarea name="observaciones" id="observaciones"></textarea>
                    </li>
                        <button type="button" id="add-participant">Agregar participante</button>
                        <ul id="buttons-container">
                            <li>
                                <button class="participant-btn" data-role="Madre">Madre</button>
                                <button class="participant-btn" data-role="Padre">Padre</button>
                                <button class="participant-btn" data-role="Apoderado">Apoderado</button>
                                <button class="participant-btn" data-role="Menor">Menor</button>
                            </li>
                        </ul>
                    <li>
                        <input type="submit" value="Crear">
                    </li>
                </ul>
            </form>
        </div>
    </div>

    <?php
    // Asegúrate de que la ruta de footer.php sea correcta
    require 'includes/footer.php';
    ?>


    <script>
        // Obtener la fecha actual en formato YYYY-MM-DD
        const today = new Date();
        const year = today.getFullYear();
        const month = String(today.getMonth() + 1).padStart(2, '0'); // Los meses son 0-11, por eso sumamos 1
        const day = String(today.getDate()).padStart(2, '0');

        // Establecer la fecha en el campo de entrada
        const currentDate = `${year}-${month}-${day}`;

        // Asignar la fecha al input
        document.getElementById('fecha-ingreso').value = currentDate;
    </script>

</body>

</html>