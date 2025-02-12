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

    <title>AutoViaje</title>
</head>

<body>
    <header>
        <?php
        // Asegúrate de que la ruta de header.php sea correcta
        require 'includes/header.php';
        ?>
    </header>

    <div class="container mt-3 mb-5">
        <div class="card shadow-lg">
            <div class="card-header bg-dark text-white text-center">
                <h2>Registrar Autorizaciones de Viaje</h2>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['mensaje'])) : ?>
                <div class="alert alert-success">
                    <?= $_SESSION['mensaje']; ?>
                </div>
                <?php unset($_SESSION['mensaje']); ?>
                <?php endif; ?>

                <form action="guardar_auto.php" method="post" class="row g-3">

                    <div class="col-md-4">
                        <label for="id_control" class="form-label">N° Control:</label>
                        <input type="text" class="form-control" id="id_control" name="id_control">
                    </div>

                    <div class="col-md-4">
                        <label for="tipo-permiso" class="form-label">Tipo de Permiso:</label>
                        <select class="form-select" id="tipo-permiso" name="tipo-permiso">
                            <option value="">Seleccione</option>
                            <?php foreach (getTpPermisos() as $permiso) : ?>
                            <option value="<?= $permiso->id_tppermi ?>"><?= $permiso->des_tppermi ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="fecha-ingreso" class="form-label">Fecha de ingreso:</label>
                        <input type="date" class="form-control" id="fecha-ingreso" name="fecha-ingreso" disabled>
                    </div>

                    <div class="col-md-12">
                        <label for="viaja-a" class="form-label">Viaja a:</label>
                        <input type="text" class="form-control" id="viaja-a" name="viaja-a">
                    </div>

                    <!-- Datos del Acompañante -->
                    <div class="col-md-3">
                        <label for="documento_acompañante" class="form-label">Tipo de documento (Acompañante):</label>
                        <select class="form-select" name="documento_acompañante" id="documento_acompañante">
                            <option value="">Seleccione</option>
                            <?php foreach (getTpDoc() as $tpdoc) : ?>
                            <option value="<?= $tpdoc->id_tpdoc ?>"><?= $tpdoc->des_tpdoc ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="numdoc_acompañante" class="form-label">Nro. Documento:</label>
                        <input type="text" class="form-control" id="numdoc_acompañante" name="numdoc_acompañante">
                    </div>

                    <div class="col-md-3">
                        <label for="nombre-acompañante" class="form-label">Nombres (Acompañante):</label>
                        <input type="text" class="form-control" id="nombre-acompañante" name="nombre-acompañante">
                    </div>

                    <div class="col-md-3">
                        <label for="apellido-acompañante" class="form-label">Apellidos (Acompañante):</label>
                        <input type="text" class="form-control" id="apellido-acompañante" name="apellido-acompañante">
                    </div>

                    <!-- Datos del Responsable -->
                    <div class="col-md-3">
                        <label for="documento_responsable" class="form-label">Tipo de documento (Responsable):</label>
                        <select class="form-select" name="documento_responsable" id="documento_responsable">
                            <option value="">Seleccione</option>
                            <?php foreach (getTpDoc() as $tpdoc) : ?>
                            <option value="<?= $tpdoc->id_tpdoc ?>"><?= $tpdoc->des_tpdoc ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="numdoc_responsable" class="form-label">Nro. Documento:</label>
                        <input type="text" class="form-control" id="numdoc_responsable" name="numdoc_responsable">
                    </div>

                    <div class="col-md-3">
                        <label for="nombre-responsable" class="form-label">Nombres (Responsable):</label>
                        <input type="text" class="form-control" id="nombre-responsable" name="nombre-responsable">
                    </div>

                    <div class="col-md-3">
                        <label for="apellido-responsable" class="form-label">Apellidos (Responsable):</label>
                        <input type="text" class="form-control" id="apellido-responsable" name="apellido-responsable">
                    </div>

                    <!-- Transporte -->
                    <div class="col-md-3">
                        <label for="tipo_transporte" class="form-label">Medio de transporte:</label>
                        <select class="form-select" name="tipo_transporte" id="tipo_transporte">
                            <option value="">Seleccione</option>
                            <?php foreach (getTpTransportes() as $tptrans) : ?>
                            <option value="<?= $tptrans->id_tptrans ?>"><?= $tptrans->des_tptrans ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="agencia-de-transporte" class="form-label">Agencia de transporte:</label>
                        <input type="text" class="form-control" id="agencia-de-transporte" name="agencia-de-transporte">
                    </div>

                    <!-- Fechas -->
                    <div class="col-md-3">
                        <label for="desde" class="form-label">Desde:</label>
                        <input type="date" class="form-control" id="desde" name="desde">
                    </div>

                    <div class="col-md-3">
                        <label for="hasta" class="form-label">Hasta:</label>
                        <input type="date" class="form-control" id="hasta" name="hasta">
                    </div>

                    <!-- Observaciones -->
                    <div class="col-12">
                        <label for="observaciones" class="form-label">Observaciones:</label>
                        <textarea class="form-control" name="observaciones" id="observaciones"></textarea>
                    </div>

                    <!-- Botón de Guardar -->
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
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
        let acompananteCompleto = docAcomp !== "" && numDocAcomp.trim() !== "" && nombreAcomp.trim() !== "" &&
            apellidoAcomp.trim() !== "";
        let responsableCompleto = docResp !== "" && numDocResp.trim() !== "" && nombreResp.trim() !== "" &&
            apellidoResp.trim() !== "";

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