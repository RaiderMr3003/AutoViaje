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
            <form action="#">
                <ul>
                    <li>
                        <label for="nro-crono">N° Cronológico:</label>
                        <input type="text" id="nro-crono" name="nro-crono">
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
                        <input type="text" id="Viaja-a" name="Viaja-a">
                    </li>
                    <li>
                        <label for="documento_acompañante">Nro. Documento del acompañante:</label>
                        <select name="documento_acompañante" id="documento_acompañante">
                            <option value="">Seleccione</option>
                            
                        </select>
                        <input type="text" id="numdoc_acompañante" name="numdoc_acompañante" placeholder="Nro. Documento">
                    </li>
                    <li>
                        <label for="nombre-acompañante">Nombre:</label>
                        <input type="text" id="nombre-acompañante" name="nombre-acompañante">
                    </li>
                    <li>
                        <label for="apellido-acompañante">Apellido:</label>
                        <input type="text" id="apellido-acompañante" name="apellido-acompañante">
                    </li>
                    <li>
                        <label for="documento_responsable">Nro. Documento del responsable:</label>
                        <select name="documento_responsable" id="documento_responsable">
                            <option value="">Seleccione</option>
                            
                        </select>
                        <input type="text" id="numdoc_responsable" name="numdoc_responsable" placeholder="Nro. Documento">
                    </li>
                    <li>
                        <label for="nombre-responsable">Nombre:</label>
                        <input type="text" id="nombre-responsable" name="nombre-responsable">
                    </li>
                    <li>
                        <label for="apellido-responsable">Apellido:</label>
                        <input type="text" id="apellido-responsable" name="apellido-responsable">
                    </li>
                    <li>
                        <label for="medio-de-transporte">Medio de transporte:</label>
                        <select name="medio-de-transporte" id="medio-de-transporte">
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