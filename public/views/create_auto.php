<?php
// Iniciar sesión (debe ser lo primero)
session_start();

// Verificar si el usuario no está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirigir al usuario a la página de login si no está logueado
    header("Location: ../../index.php");
    exit;
}

require 'includes/functions.php';
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
            <?php
                if (isset($_SESSION['mensaje'])) {
                    echo "<p style='color: green;'>" . $_SESSION['mensaje'] . "</p>";
                    unset($_SESSION['mensaje']); // Elimina el mensaje después de mostrarlo
                }
            ?>
            <form action="guardar_auto.php" method="post">
                <ul>
                    <li>
                        <label for="nro-control">N° Control:</label>
                        <input type="text" id="id_control" name="id_control">
                    </li>

                    <li>
                        <label for="tipo-permiso">Tipo de Permiso:</label>
                        <select id="tipo-permiso" name="tipo-permiso">
                            <option value="">Seleccione</option>
                            <?php
                            $permisos = getTpPermisos();
                            
                            foreach ($permisos as $permiso) {
                                echo "<option value='" . $permiso->id_tppermi . "'>" . $permiso->des_tppermi . "</option>";
                            }
                            ?>
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
                            <option value="">Seleccione Tipo de documento</option>
                            <?php
                                $tpdoc = getTpDoc();
                                
                                foreach ($tpdoc as $tpdoc) {
                                    echo "<option value='" . $tpdoc->id_tpdoc . "'>" . $tpdoc->des_tpdoc . "</option>";
                                }
                            ?>
                        </select>
                        <input type="text" id="numdoc_acompañante" name="numdoc_acompañante"
                            placeholder="Nro. Documento">
                    </li>
                    <li>
                        <input type="text" id="nombre-acompañante" name="nombre-acompañante" placeholder="Nombres">
                    </li>
                    <li>
                        <input type="text" id="apellido-acompañante" name="apellido-acompañante"
                            placeholder="Apellidos">
                    </li>
                    <li>
                        <label for="documento_responsable">Datos del responsable:</label>
                        <select name="documento_responsable" id="documento_responsable">
                            <option value="">Seleccione Tipo de documento</option>
                            <?php
                                $tpdoc = getTpDoc();
                                
                                foreach ($tpdoc as $tpdoc) {
                                    echo "<option value='" . $tpdoc->id_tpdoc . "'>" . $tpdoc->des_tpdoc . "</option>";
                                }
                            ?>
                        </select>
                        <input type="text" id="numdoc_responsable" name="numdoc_responsable"
                            placeholder="Nro. Documento">
                    </li>
                    <li>
                        <input type="text" id="nombre-responsable" name="nombre-responsable" placeholder="Nombres">
                    </li>
                    <li>
                        <input type="text" id="apellido-responsable" name="apellido-responsable"
                            placeholder="Apellidos">
                    </li>

                    <li>
                        <label for="tipo_transporte">Medio de transporte:</label>
                        <select name="tipo_transporte" id="tipo_transporte">
                            <option value="">Seleccione</option>
                            <?php
                                $tptrans = getTpTransportes();
                                
                                foreach ($tptrans as $tptrans) {
                                    echo "<option value='" . $tptrans->id_tptrans . "'>" . $tptrans->des_tptrans . "</option>";
                                }
                            ?>
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
                    <li>
                        <input type="submit" value="Guardar">
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

    document.querySelector("form").addEventListener("submit", function(event) {
    let errores = [];

    // Obtener valores de los campos
    let tipoPermiso = document.getElementById("tipo-permiso").value;
    let viajaA = document.getElementById("viaja-a").value;
    let medioTransporte = document.getElementById("tipo_transporte").value;
    let agenciaTransporte = document.getElementById("agencia-de-transporte").value;
    let desde = document.getElementById("desde").value;
    let hasta = document.getElementById("hasta").value;

    let docAcomp = document.getElementById("documento_acompañante").value;
    let numDocAcomp = document.getElementById("numdoc_acompañante").value;
    let nombreAcomp = document.getElementById("nombre-acompañante").value;
    let apellidoAcomp = document.getElementById("apellido-acompañante").value;

    let docResp = document.getElementById("documento_responsable").value;
    let numDocResp = document.getElementById("numdoc_responsable").value;
    let nombreResp = document.getElementById("nombre-responsable").value;
    let apellidoResp = document.getElementById("apellido-responsable").value;

    // Validaciones generales
    if (tipoPermiso === "") errores.push("Debe seleccionar un tipo de permiso.");
    if (viajaA.trim() === "") errores.push("Debe llenar la casilla Viaja a.");

    // Validar que haya al menos un acompañante o un responsable con todos los datos completos
    let acompananteCompleto = docAcomp !== "" && numDocAcomp.trim() !== "" && nombreAcomp.trim() !== "" && apellidoAcomp.trim() !== "";
    let responsableCompleto = docResp !== "" && numDocResp.trim() !== "" && nombreResp.trim() !== "" && apellidoResp.trim() !== "";

    if (!acompananteCompleto && !responsableCompleto) {
        errores.push("Debe ingresar al menos un acompañante o un responsable con todos sus datos.");
    }


    if (medioTransporte === "") errores.push("Debe seleccionar un medio de transporte.");
    if (agenciaTransporte.trim() === "") errores.push("Debe ingresar la agencia de transporte.");
    if (desde === "") errores.push("Debe ingresar la fecha de inicio.");
    if (hasta === "") errores.push("Debe ingresar la fecha de finalización.");

    // Mostrar errores y evitar el envío si hay problemas
    if (errores.length > 0) {
        alert("Errores encontrados:\n- " + errores.join("\n- "));
        event.preventDefault(); // Evita que el formulario se envíe
    }
});

    </script>

</body>

</html>