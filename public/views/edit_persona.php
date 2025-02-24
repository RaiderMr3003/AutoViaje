<?php
// Iniciar sesión (debe ser lo primero)
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../index.php");
    exit;
}

// Conexión a la base de datos
require 'includes/functions.php';
require '../../config/conexion.php'; // Ajusta la ruta según tu estructura

if (isset($_GET['id_persona']) && isset($_GET['id_autorizacion'])) {
    $id_persona = $_GET['id_persona'];
    $id_autorizacion = $_GET['id_autorizacion'];

    // Obtener los datos de la persona desde la base de datos
    $stmt = $pdo->prepare("SELECT * FROM personas WHERE id_persona = ?");
    $stmt->execute([$id_persona]);
    $persona = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$persona) {
        die("Error: Persona no encontrada.");
    }
} else {
    die("Error: No se recibieron los datos necesarios.");
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Persona</title>
</head>

<body>
    <header>
        <?php require 'includes/header.php'; ?>
    </header>

    <div class="container-sm mt-3 mb-5">
        <div class="card shadow-lg mb-3">
            <div class="card-header bg-dark text-white text-center">
                <h2>Editar Persona</h2>
            </div>
            <div class="card-body">
                <form class="row g-3" autocomplete="off" id="form-editar">
                    <input type="hidden" name="id_persona" value="<?= htmlspecialchars($id_persona); ?>">
                    <input type="hidden" name="id_autorizacion" value="<?= htmlspecialchars($id_autorizacion); ?>">

                    <div class="col-md-3">
                        <label for="documento_persona" class="form-label">Tipo de documento:</label>
                        <select class="form-select" name="documento_persona" id="documento_persona">
                            <option value="">Seleccione</option>
                            <?php foreach (getTpDoc() as $tpdoc) : ?>
                                <option value="<?= $tpdoc->id_tpdoc ?>" <?= ($persona['id_tpdoc'] == $tpdoc->id_tpdoc) ? 'selected' : ''; ?>>
                                    <?= $tpdoc->des_tpdoc ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label for="numdoc_persona" class="form-label">Nro. Documento:</label>
                        <input class="form-control" type="text" id="numdoc_persona" name="numdoc_persona" value="<?= htmlspecialchars($persona['num_doc']); ?>">
                    </div>

                    <div class="col-md-3">
                        <label for="edad" class="form-label">Edad:</label>
                        <input class="form-control" type="number" id="edad" name="edad" value="<?= htmlspecialchars($persona['edad']); ?>" min="0" max="130">
                    </div>

                    <div class="col-md-3">
                        <label for="tipo_edad" class="form-label">Tipo Edad:</label>
                        <select name="tipo_edad" id="tipo_edad" class="form-select">
                            <option value=""></option>
                            <option value="AÑO(S)" <?= ($persona['tipo_edad'] == "AÑO(S)") ? 'selected' : ''; ?>>AÑO(S)</option>
                            <option value="MES(ES)" <?= ($persona['tipo_edad'] == "MES(ES)") ? 'selected' : ''; ?>>MES(ES)</option>
                            <option value="SEMANAS" <?= ($persona['tipo_edad'] == "SEMANAS") ? 'selected' : ''; ?>>SEMANAS</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="nombre-persona" class="form-label">Nombres:</label>
                        <input class="form-control" type="text" id="nombre-persona" name="nombre-persona" value="<?= htmlspecialchars($persona['nombres']); ?>">
                    </div>

                    <div class="col-md-6">
                        <label for="apellido-persona" class="form-label">Apellidos:</label>
                        <input class="form-control" type="text" id="apellido-persona" name="apellido-persona" value="<?= htmlspecialchars($persona['apellidos']); ?>">
                    </div>

                    <div class="col-md-4">
                        <label for="direccion-persona" class="form-label">Dirección:</label>
                        <input class="form-control" type="text" id="direccion-persona" name="direccion-persona" value="<?= htmlspecialchars($persona['direccion'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">                        </div>

                    <div class="col-md-4">
                        <label for="Ubigeo-persona" class="form-label">Distrito:</label>
                        <select name="Ubigeo-persona" id="Ubigeo-persona" class="form-select">
                            <option value="">Seleccione</option>
                            <?php foreach (getUbigeo() as $ubigeo) : ?>
                                <option value="<?= $ubigeo->id_ubigeo ?>" <?= ($persona['id_ubigeo'] == $ubigeo->id_ubigeo) ? 'selected' : ''; ?>>
                                    <?= $ubigeo->nom_dis ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label for="nacionalidad" class="form-label">Nacionalidad:</label>
                        <select name="nacionalidad" id="nacionalidad" class="form-select">
                            <option value="">Seleccione</option>
                            <?php foreach (getNacionalidad() as $nacionalidad) : ?>
                                <option value="<?= $nacionalidad->id_nacionalidad ?>" <?= ($persona['id_nacionalidad'] == $nacionalidad->id_nacionalidad) ? 'selected' : ''; ?>>
                                    <?= $nacionalidad->desc_nacionalidad ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-12 text-center">
                    <button type="button" class="btn btn-primary" onclick="guardarCambios()">Guardar Cambios</button>
                    <a href="edit_auto.php?id=<?= $id_autorizacion ?>" class="btn btn-secondary">Cancelar</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php require 'includes/footer.php'; ?>

    <script>
        function guardarCambios() {
            // Crear un objeto FormData con los datos del formulario
            let formData = new FormData(document.getElementById('form-editar'));

            // Enviar los datos con fetch
            fetch('procesar_edit_persona.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json()) // Suponiendo que el servidor devuelve JSON
            .then(data => {
                if (data.success) {
                    alert('Datos guardados correctamente.');
                    window.location.href = 'edit_auto.php?id=' + formData.get('id_autorizacion'); // Redirigir
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Hubo un problema al guardar los datos.');
            });
        }
    </script>
</body>

</html>
