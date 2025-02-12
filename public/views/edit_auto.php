<?php
session_start();
require '../../config/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../index.php");
    exit;
}

// Obtener ID de la autorización
if (!isset($_GET['id'])) {
    die("ID no proporcionado.");
}
$id_autorizacion = $_GET['id'];

// Consultar datos de la autorización
$sql = "SELECT * FROM autorizaciones WHERE id_autorizacion = :id_autorizacion";
$stmt = $pdo->prepare($sql);
$stmt->execute([':id_autorizacion' => $id_autorizacion]);
$autorizacion = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$autorizacion) {
    die("Autorización no encontrada.");
}

require 'includes/functions.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Autorización</title>
</head>

<body>
    <header>
        <?php require 'includes/header.php'; ?>
    </header>

    <div class="container mt-3 mb-5">
        <div class="card shadow-lg">
            <div class="card-header bg-dark text-white text-center">
                <h2>Editar Autorización</h2>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['mensaje'])) : ?>
                    <div class="alert alert-success">
                        <?= $_SESSION['mensaje']; ?>
                    </div>
                    <?php unset($_SESSION['mensaje']); ?>
                <?php endif; ?>

                <form action="guardar_auto.php" method="post" class="row g-3" autocomplete="off">
                    <input type="hidden" name="id" value="<?= $id_autorizacion ?>">

                    <div class="col-md-4">
                        <label for="nro-control" class="form-label">N° Control:</label>
                        <input type="text" class="form-control" id="id_control" name="id_control"
                            value="<?= htmlspecialchars($autorizacion['nro_kardex']) ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="tipo-permiso" class="form-label">Tipo de Permiso:</label>
                        <select class="form-select" id="tipo-permiso" name="tipo-permiso">
                            <option value="">Seleccione</option>
                            <?php
                            $permisos = getTpPermisos();
                            foreach ($permisos as $permiso) {
                                $selected = ($permiso->id_tppermi == $autorizacion['id_tppermi']) ? 'selected' : '';
                                echo "<option value='{$permiso->id_tppermi}' $selected>{$permiso->des_tppermi}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="fecha-ingreso" class="form-label">Fecha de ingreso:</label>
                        <input type="date" class="form-control" id="fecha-ingreso" name="fecha-ingreso"
                            value="<?= $autorizacion['fecha_ingreso'] ?>" disabled>
                    </div>

                    <div class="col-md-12">
                        <label for="viaja-a" class="form-label">Viaja a:</label>
                        <input type="text" class="form-control" id="viaja-a" name="viaja-a"
                            value="<?= htmlspecialchars($autorizacion['viaja_a']) ?>">
                    </div>

                    <div class="col-md-3">
                        <label for="documento_acompañante" class="form-label">Tipo de documento (Acompañante):</label>
                        <select class="form-select" name="documento_acompañante" id="documento_acompañante">
                            <option value="">Seleccione</option>
                            <?php
                            $tpdoc = getTpDoc();
                            foreach ($tpdoc as $doc) {
                                $selected = ($doc->id_tpdoc == $autorizacion['id_tpdoc_acomp']) ? 'selected' : '';
                                echo "<option value='{$doc->id_tpdoc}' $selected>{$doc->des_tpdoc}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="numdoc_acompañante" class="form-label">Nro. Documento:</label>
                        <input class="form-control" type="text" id="numdoc_acompañante" name="numdoc_acompañante"
                            value="<?= htmlspecialchars($autorizacion['num_doc_acomp']) ?>"
                            placeholder="Nro. Documento">
                    </div>

                    <div class="col-md-3">
                        <label for="nombre-acompañante" class="form-label">Nombres (Acompañante):</label>
                        <input class="form-control" type="text" id="nombre-acompañante" name="nombre-acompañante"
                            value="<?= htmlspecialchars($autorizacion['nombres_acomp']) ?>" placeholder="Nombres">
                    </div>

                    <div class="col-md-3">
                        <label for="apellido-acompañante" class="form-label">Apellidos (Acompañante):</label>
                        <input class="form-control" type="text" id="apellido-acompañante" name="apellido-acompañante"
                            value="<?= htmlspecialchars($autorizacion['apellidos_acomp']) ?>" placeholder="Apellidos">
                    </div>


                    <div class="col-md-3">
                        <label for="documento_responsable" class="form-label">Tipo de documento (Responsable):</label>
                        <select class="form-select" name="documento_responsable" id="documento_responsable">
                            <option value="">Seleccione</option>
                            <?php
                            $tpdoc = getTpDoc();
                            foreach ($tpdoc as $doc) {
                                $selected = ($doc->id_tpdoc == $autorizacion['id_tpdoc_resp']) ? 'selected' : '';
                                echo "<option value='{$doc->id_tpdoc}' $selected>{$doc->des_tpdoc}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="numdoc_responsable" class="form-label">Nro. Documento:</label>
                        <input class="form-control" type="text" id="numdoc_responsable" name="numdoc_responsable"
                            value="<?= htmlspecialchars($autorizacion['num_doc_resp']) ?>" placeholder="Nro. Documento">
                    </div>

                    <div class="col-md-3">
                        <label for="nombre-responsable" class="form-label">Nombres (Responsable):</label>
                        <input class="form-control" type="text" id="nombre-responsable" name="nombre-responsable"
                            value="<?= htmlspecialchars($autorizacion['nombres_resp']) ?>" placeholder="Nombres">
                    </div>

                    <div class="col-md-3">
                        <label for="apellido-responsable" class="form-label">Apellidos (Responsable):</label>
                        <input class="form-control" type="text" id="apellido-responsable" name="apellido-responsable"
                            value="<?= htmlspecialchars($autorizacion['apellidos_resp']) ?>" placeholder="Apellidos">
                    </div>

                    <div class="col-md-3">
                        <label for="tipo_transporte" class="form-label">Medio de transporte:</label>
                        <select class="form-select" name="tipo_transporte" id="tipo_transporte">
                            <option value="">Seleccione</option>
                            <?php
                            $tptrans = getTpTransportes();
                            foreach ($tptrans as $trans) {
                                $selected = ($trans->id_tptrans == $autorizacion['id_tptrans']) ? 'selected' : '';
                                echo "<option value='{$trans->id_tptrans}' $selected>{$trans->des_tptrans}</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="agencia-de-transporte" class="form-label">Agencia de transporte:</label>
                        <input class="form-control" type="text" id="agencia-de-transporte" name="agencia-de-transporte"
                            value="<?= htmlspecialchars($autorizacion['agencia_transporte']) ?>">
                    </div>

                    <!-- Fechas -->
                    <div class="col-md-3">
                        <label for="desde" class="form-label">Desde:</label>
                        <input type="date" id="desde" class="form-control" name="desde"
                            value="<?= $autorizacion['fecha_inicio'] ?>">
                    </div>

                    <div class="col-md-3">
                        <label for="hasta" class="form-label">Hasta:</label>
                        <input type="date" id="hasta" class="form-control" name="hasta"
                            value="<?= $autorizacion['fecha_fin'] ?>">
                    </div>

                    <div class="col-12">
                        <label for="observaciones" class="form-label">Observaciones:</label>
                        <textarea class="form-control" name="observaciones"
                            id="observaciones"><?= htmlspecialchars($autorizacion['observaciones']) ?></textarea>
                    </div>

                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>

                <!-- Botón para abrir el modal -->
                <button id="ver-participantes" class="btn btn-primary" data-bs-toggle="modal"
                    data-bs-target="#modal-ver-participantes">Ver participantes</button>

                <!-- Modal para ver participantes -->
                <div id="modal-ver-participantes" class="modal fade" data-bs-backdrop="static" data-bs-keyboard="false"
                    tabindex="-1" aria-labelledby="modal-ver-participantes" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-xl">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5">Lista de Participantes</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>

                            <div class="modal-body">
                                <table class="table table-striped table-bordered">
                                    <thead class="table-dark">
                                        <tr class="text-center">
                                            <th>ID</th>
                                            <th>Num. Doc.</th>
                                            <th>Apellidos y Nombres</th>
                                            <th>F. Nacimiento</th>
                                            <th>Tipo de Relación</th>
                                            <th>Firma</th>
                                            <th>Representado</th>
                                        </tr>
                                    </thead>
                                    <tbody id="tabla-personas">
                                        <!-- Aquí se insertarán los datos -->
                                    </tbody>
                                </table>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                                <button class="btn btn-primary" data-bs-target="#modal-añadir-participantes"
                                    data-bs-toggle="modal">Añadir Participantes</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="modal-añadir-participantes" class="modal fade" data-bs-backdrop="static"
                    data-bs-keyboard="false" tabindex="-1" aria-labelledby="modal-añadir-participantes"
                    aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h1 class="modal-title fs-5" id="modal-añadir-participantes">Añadir Participantes</h1>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <form action="" method="post" class="row g-3" autocomplete="off">
                                    <input type="hidden" name="id" value="<?= $id_autorizacion ?>">

                                    <div class="col-md-3">
                                        <label for="documento_persona" class="form-label">Tipo de documento:</label>
                                        <select class="form-select" name="documento_persona" id="documento_persona">
                                            <option value="">Seleccione</option>
                                            <?php foreach (getTpDoc() as $tpdoc) : ?>
                                                <option value="<?= $tpdoc->id_tpdoc ?>"><?= $tpdoc->des_tpdoc ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="numdoc_persona" class="form-label">Nro. Documento:</label>
                                        <input class="form-control" type="text" id="numdoc_persona" name="numdoc_persona">
                                    </div>

                                    <div class="col-md-3">
                                        <label for="condicion" class="form-label">Condición:</label>
                                        <select class="form-select" name="condicion" id="condicion">
                                            <option value="">Seleccione</option>
                                            <?php foreach (getCondicion() as $condicion) : ?>
                                                <option value="<?= $condicion->id_tp_relacion ?>"><?= $condicion->descripcion ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="col-md-3">
                                        <label for="firma" class="form-label">Firma:</label>
                                        <select name="firma" id="firma" class="form-select">
                                            <option value="">Seleccione</option>
                                            <?php
                                            // Llamar a la función para obtener las opciones de firma
                                            foreach (getFirma() as $opcion) {
                                                echo '<option value="' . htmlspecialchars($opcion) . '">' . htmlspecialchars($opcion) . '</option>';
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-md-6">
                                        <label for="nombre-persona" class="form-label">Nombres:</label>
                                        <input class="form-control" type="text" id="nombre-persona" name="nombre-persona">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="apellido-persona" class="form-label">Apellidos:</label>
                                        <input class="form-control" type="text" id="apellido-persona" name="apellido-persona">
                                    </div>

                                    <div class="col-md-12">
                                        <label for="direccion-persona" class="form-label">Dirección:</label>
                                        <input class="form-control" type="text" id="direccion-persona" name="direccion-persona">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="Ubigeo-persona" class="form-label">Ubigeo:</label>
                                        <input class="form-control" type="text" id="Ubigeo-persona" name="Ubigeo-persona">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="nacionalidad" class="form-label">Nacionalidad:</label>
                                        <input class="form-control" type="text" id="nacionalidad" name="nacionalidad">
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" role="switch" id="en_representacion" name="en_representacion">
                                            <label class="form-check-label" for="en_representacion">En representación de:</label>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <div id="representantes" class="oculto">
                                            <div class="row g-2">
                                                <div class="col-md-12">
                                                    <label for="representante-persona" class="form-label">Representante de:</label>
                                                    <select name="representante-persona" id="representante-persona" class="form-select">
                                                        <option value="">Seleccione</option>
                                                        <?php
                                                        $participantes = getParticipantes($id_autorizacion); // Pasamos el ID de autorización
                                                        foreach ($participantes as $participante) {
                                                            echo '<option value="' . htmlspecialchars($participante->nombre_completo) . '">' . htmlspecialchars($participante->nombre_completo) . '</option>';
                                                        }
                                                        ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-secondary" data-bs-target="#modal-ver-participantes"
                                    data-bs-toggle="modal">Cerrar</button>
                                <button class="btn btn-primary">Añadir Participantes</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
    // Asegúrate de que la ruta de footer.php sea correcta
    require 'includes/footer.php';
    ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const enRepresentacion = document.getElementById("en_representacion");
            const representantes = document.getElementById("representantes");

            function toggleVisibility(checkbox, target) {
                target.style.display = checkbox.checked ? "block" : "none";
            }

            enRepresentacion.addEventListener("change", function() {
                toggleVisibility(enRepresentacion, representantes);
            });

            // Verificar el estado inicial al cargar la página
            toggleVisibility(enRepresentacion, representantes);
        });
        
        document.getElementById("ver-participantes").addEventListener("click", function() {
            let modal = document.getElementById("modal-ver-participantes");
            modal.style.display = "block";

            // Llamar a la función AJAX para cargar los datos
            cargarPersonas();
        });

        document.querySelector(".close").addEventListener("click", function() {
            document.getElementById("modal-ver-participantes").style.display = "none";
        });

        function cargarPersonas() {
            let id_autorizacion = <?= $id_autorizacion ?>; // Pasar la ID desde PHP
            let xhr = new XMLHttpRequest();
            xhr.open("GET", "get_person.php?id_autorizacion=" + id_autorizacion, true);
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    let datos = JSON.parse(xhr.responseText);
                    let tabla = document.getElementById("tabla-personas");
                    tabla.innerHTML = ""; // Limpiar tabla antes de insertar nuevos datos

                    if (datos.mensaje) {
                        tabla.innerHTML = `<tr><td colspan="5">${datos.mensaje}</td></tr>`;
                    } else {
                        datos.forEach(function(persona) {
                            let fila = `<tr>
                        <td>${persona.id_persona}</td>
                        <td>${persona.num_doc}</td>
                        <td>${persona.nombre_completo}</td>
                        <td>${persona.fecha_nacimiento}</td>
                        <td>${persona.tipo_relacion}</td>
                        <td>${persona.firma}</td>
                        <td>${persona.en_representacion}</td>
                    </tr>`;
                            tabla.innerHTML += fila;
                        });
                    }
                }
            };
            xhr.send();
        }
    </script>

</body>

</html>