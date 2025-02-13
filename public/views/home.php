<?php
// Iniciar sesión (debe ser lo primero)
session_start();
require '../../config/conexion.php';

// Verificar si el usuario no está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    // Redirigir al usuario a la página de login si no está logueado
    header("Location: ../../index.php");
    exit;
}

// Obtener datos de la tabla autorizaciones
try {
    $stmt = $pdo->query("
        SELECT 
            a.id_autorizacion, 
            a.nro_kardex, 
            a.encargado, 
            tp.des_tppermi AS tipo_permiso, 
            a.fecha_ingreso, 
            a.observaciones,
            GROUP_CONCAT(
                CONCAT(
                    tr.descripcion, ': ', p.apellidos, ', ', p.nombres
                ) SEPARATOR '\n'
            ) AS participantes
        FROM autorizaciones a
        JOIN tp_permiso tp ON a.id_tppermi = tp.id_tppermi
        LEFT JOIN personas_autorizaciones pa ON a.id_autorizacion = pa.id_autorizacion
        LEFT JOIN personas p ON pa.id_persona = p.id_persona
        LEFT JOIN tp_relacion tr ON pa.id_tp_relacion = tr.id_tp_relacion
        GROUP BY a.id_autorizacion
        ORDER BY a.id_autorizacion ASC
    ");
    $autorizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error al obtener autorizaciones: " . $e->getMessage());
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

    <div class="container-fluid my-3" style="padding-left: 7rem; padding-right: 7rem;">
        <div class="row">
            <!-- Panel de Búsqueda - Ocupa 4 columnas -->
            <div class="col-md-3">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white text-center">
                        <h6 class="mb-1 text-center">Buscar Autorizaciones</h6>
                    </div>
                    <form method="POST" action="" class="p-3">
                        <div class="row g-3">
                            <div class="col-12">
                                <label for="tipo-permiso" class="form-label">Tipo de Permiso</label>
                                <select id="tipo-permiso" name="tipo-permiso" class="form-select">
                                    <option value="">Seleccione...</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="nro-crono" class="form-label">N° Cronológico</label>
                                <input type="text" id="nro-crono" name="nro-crono" class="form-control">
                            </div>

                            <div class="col-12">
                                <label for="encargado" class="form-label">Usuario Encargado</label>
                                <input type="text" id="encargado" name="encargado" class="form-control">
                            </div>

                            <div class="col-12">
                                <label for="nombre-participante" class="form-label">Nombre del Participante</label>
                                <input type="text" id="nombre-participante" name="nombre-participante"
                                    class="form-control">
                            </div>

                            <div class="col-12">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label for="fecha-min" class="form-label">Fecha Min</label>
                                        <input type="date" id="fecha-min" name="fecha-min" class="form-control">
                                    </div>
                                    <div class="col-6">
                                        <label for="fecha-max" class="form-label">Fecha Max</label>
                                        <input type="date" id="fecha-max" name="fecha-max" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary w-100">Buscar</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Tabla de Resultados - Ocupa 8 columnas -->
            <div class="col-md-9 mb-4">
                <div class="card shadow">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-dark text-center">
                                <tr class="">
                                    <th>N° Crono.</th>
                                    <th>Encargado</th>
                                    <th>Participantes</th>
                                    <th>Tipo de Permiso</th>
                                    <th>F. Ingreso</th>
                                    <th>Observaciones</th>
                                    <th class="px-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($autorizaciones as $autorizacion) : ?>
                                    <tr style="text-transform: uppercase;">
                                        <td class="text-center"><?= htmlspecialchars($autorizacion['nro_kardex']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($autorizacion['encargado']) ?></td>
                                        <td><?= nl2br(htmlspecialchars($autorizacion['participantes'])) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($autorizacion['tipo_permiso']) ?></td>
                                        <td><?= htmlspecialchars($autorizacion['fecha_ingreso']) ?></td>
                                        <td><?= htmlspecialchars(mb_strimwidth($autorizacion['observaciones'] ?? 'N/A', 0, 10, '...')) ?></td>
                                        <td>
                                            <a href="edit_auto.php?id=<?= $autorizacion['id_autorizacion'] ?>"
                                                class="btn btn-sm btn-warning"><svg
                                                    xmlns="http://www.w3.org/2000/svg" width="15" height="20"
                                                    fill="white" class="bi bi-pencil-fill" viewBox="0 0 16 16">
                                                    <path
                                                        d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z" />
                                                </svg></a>
                                            <a href="ver-pdf.php?id=<?= $autorizacion['id_autorizacion'] ?>"
                                                class="btn btn-sm btn-primary"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-file-earmark-pdf" viewBox="0 0 16 16">
                                                    <path d="M14 14V4.5L9.5 0H4a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2M9.5 3A1.5 1.5 0 0 0 11 4.5h2V14a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1h5.5z" />
                                                    <path d="M4.603 14.087a.8.8 0 0 1-.438-.42c-.195-.388-.13-.776.08-1.102.198-.307.526-.568.897-.787a7.7 7.7 0 0 1 1.482-.645 20 20 0 0 0 1.062-2.227 7.3 7.3 0 0 1-.43-1.295c-.086-.4-.119-.796-.046-1.136.075-.354.274-.672.65-.823.192-.077.4-.12.602-.077a.7.7 0 0 1 .477.365c.088.164.12.356.127.538.007.188-.012.396-.047.614-.084.51-.27 1.134-.52 1.794a11 11 0 0 0 .98 1.686 5.8 5.8 0 0 1 1.334.05c.364.066.734.195.96.465.12.144.193.32.2.518.007.192-.047.382-.138.563a1.04 1.04 0 0 1-.354.416.86.86 0 0 1-.51.138c-.331-.014-.654-.196-.933-.417a5.7 5.7 0 0 1-.911-.95 11.7 11.7 0 0 0-1.997.406 11.3 11.3 0 0 1-1.02 1.51c-.292.35-.609.656-.927.787a.8.8 0 0 1-.58.029m1.379-1.901q-.25.115-.459.238c-.328.194-.541.383-.647.547-.094.145-.096.25-.04.361q.016.032.026.044l.035-.012c.137-.056.355-.235.635-.572a8 8 0 0 0 .45-.606m1.64-1.33a13 13 0 0 1 1.01-.193 12 12 0 0 1-.51-.858 21 21 0 0 1-.5 1.05zm2.446.45q.226.245.435.41c.24.19.407.253.498.256a.1.1 0 0 0 .07-.015.3.3 0 0 0 .094-.125.44.44 0 0 0 .059-.2.1.1 0 0 0-.026-.063c-.052-.062-.2-.152-.518-.209a4 4 0 0 0-.612-.053zM8.078 7.8a7 7 0 0 0 .2-.828q.046-.282.038-.465a.6.6 0 0 0-.032-.198.5.5 0 0 0-.145.04c-.087.035-.158.106-.196.283-.04.192-.03.469.046.822q.036.167.09.346z" />
                                                </svg></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php
    // Asegúrate de que la ruta de footer.php sea correcta
    require 'includes/footer.php';
    ?>
</body>

</html>