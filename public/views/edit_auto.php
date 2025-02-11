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

    <div class="main-container">
        <div class="container">
            <h1>Editar Autorización</h1>

            <?php if (isset($_SESSION['mensaje'])): ?>
                <p style='color: green;'><?= $_SESSION['mensaje']; ?></p>
                <?php unset($_SESSION['mensaje']); ?>
            <?php endif; ?>

            <form action="guardar_auto.php" method="post">
                <input type="hidden" name="id" value="<?= $id_autorizacion ?>">

                <ul>
                    <li>
                        <label for="nro-control">N° Control:</label>
                        <input type="text" id="id_control" name="id_control"
                            value="<?= htmlspecialchars($autorizacion['nro_kardex']) ?>">
                    </li>

                    <li>
                        <label for="tipo-permiso">Tipo de Permiso:</label>
                        <select id="tipo-permiso" name="tipo-permiso">
                            <option value="">Seleccione</option>
                            <?php
                            $permisos = getTpPermisos();
                            foreach ($permisos as $permiso) {
                                $selected = ($permiso->id_tppermi == $autorizacion['id_tppermi']) ? 'selected' : '';
                                echo "<option value='{$permiso->id_tppermi}' $selected>{$permiso->des_tppermi}</option>";
                            }
                            ?>
                        </select>
                    </li>

                    <li>
                        <label for="fecha-ingreso">Fecha de ingreso:</label>
                        <input type="date" id="fecha-ingreso" name="fecha-ingreso"
                            value="<?= $autorizacion['fecha_ingreso'] ?>" disabled>
                    </li>

                    <li>
                        <label for="viaja-a">Viaja a:</label>
                        <input type="text" id="viaja-a" name="viaja-a"
                            value="<?= htmlspecialchars($autorizacion['viaja_a']) ?>">
                    </li>

                    <li>
                        <label for="documento_acompañante">Tipo de documento del acompañante:</label>
                        <select name="documento_acompañante" id="documento_acompañante">
                            <option value="">Seleccione</option>
                            <?php
                            $tpdoc = getTpDoc();
                            foreach ($tpdoc as $doc) {
                                $selected = ($doc->id_tpdoc == $autorizacion['id_tpdoc_acomp']) ? 'selected' : '';
                                echo "<option value='{$doc->id_tpdoc}' $selected>{$doc->des_tpdoc}</option>";
                            }
                            ?>
                        </select>
                        <input type="text" id="numdoc_acompañante" name="numdoc_acompañante"
                            value="<?= htmlspecialchars($autorizacion['num_doc_acomp']) ?>"
                            placeholder="Nro. Documento">
                    </li>

                    <li>
                        <input type="text" id="nombre-acompañante" name="nombre-acompañante"
                            value="<?= htmlspecialchars($autorizacion['nombres_acomp']) ?>" placeholder="Nombres">
                    </li>

                    <li>
                        <input type="text" id="apellido-acompañante" name="apellido-acompañante"
                            value="<?= htmlspecialchars($autorizacion['apellidos_acomp']) ?>" placeholder="Apellidos">
                    </li>

                    <li>
                        <label for="documento_responsable">Tipo de documento del responsable:</label>
                        <select name="documento_responsable" id="documento_responsable">
                            <option value="">Seleccione</option>
                            <?php
                            $tpdoc = getTpDoc();
                            foreach ($tpdoc as $doc) {
                                $selected = ($doc->id_tpdoc == $autorizacion['id_tpdoc_resp']) ? 'selected' : '';
                                echo "<option value='{$doc->id_tpdoc}' $selected>{$doc->des_tpdoc}</option>";
                            }
                            ?>
                        </select>
                        <input type="text" id="numdoc_responsable" name="numdoc_responsable"
                            value="<?= htmlspecialchars($autorizacion['num_doc_resp']) ?>" placeholder="Nro. Documento">
                    </li>

                    <li>
                        <input type="text" id="nombre-responsable" name="nombre-responsable"
                            value="<?= htmlspecialchars($autorizacion['nombres_resp']) ?>" placeholder="Nombres">
                    </li>

                    <li>
                        <input type="text" id="apellido-responsable" name="apellido-responsable"
                            value="<?= htmlspecialchars($autorizacion['apellidos_resp']) ?>" placeholder="Apellidos">
                    </li>

                    <li>
                        <label for="tipo_transporte">Medio de transporte:</label>
                        <select name="tipo_transporte" id="tipo_transporte">
                            <option value="">Seleccione</option>
                            <?php
                            $tptrans = getTpTransportes();
                            foreach ($tptrans as $trans) {
                                $selected = ($trans->id_tptrans == $autorizacion['id_tptrans']) ? 'selected' : '';
                                echo "<option value='{$trans->id_tptrans}' $selected>{$trans->des_tptrans}</option>";
                            }
                            ?>
                        </select>
                    </li>

                    <li>
                        <label for="agencia-de-transporte">Agencia de transporte:</label>
                        <input type="text" id="agencia-de-transporte" name="agencia-de-transporte"
                            value="<?= htmlspecialchars($autorizacion['agencia_transporte']) ?>">
                    </li>

                    <li>
                        <label for="desde">Desde:</label>
                        <input type="date" id="desde" name="desde" value="<?= $autorizacion['fecha_inicio'] ?>">
                    </li>

                    <li>
                        <label for="hasta">Hasta:</label>
                        <input type="date" id="hasta" name="hasta" value="<?= $autorizacion['fecha_fin'] ?>">
                    </li>

                    <li>
                        <label for="observaciones">Observaciones:</label>
                        <textarea name="observaciones"
                            id="observaciones"><?= htmlspecialchars($autorizacion['observaciones']) ?></textarea>
                    </li>

                    <li>
                        <input type="submit" value="Guardar">
                    </li>
                </ul>
            </form>
        </div>
        <div>
            <!--, Boton de ver participantes -->
            <button id="ver-participantes">Ver participantes</button>
            <!-- modal para ver participantes -->
            <div id="modal-ver-participantes" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2>Lista de Personas</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Num. Doc.</th>
                                <th>Apellidos</th>
                                <th>Nombres</th>
                                <th>Fecha Nacimiento</th>
                            </tr>
                        </thead>
                        <tbody id="tabla-personas">
                            <!-- Aquí se insertarán los datos -->
                        </tbody>
                    </table>
                    <button>Añadir Participantes</button>
                </div>
            </div>
        </div>
    </div>

    <?php require 'includes/footer.php'; ?>

    <script>
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
                        <td>${persona.apellidos}</td>
                        <td>${persona.nombres}</td>
                        <td>${persona.fecha_nacimiento}</td>
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