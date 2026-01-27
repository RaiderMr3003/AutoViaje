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

// Paginación: obtener página actual
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

require_once 'includes/functions.php';

// Obtener datos usando la función centralizada
$resultado = obtenerAutorizaciones([], $records_per_page, $offset);
$autorizaciones = $resultado['data'];
$total_rows = $resultado['total'];

// Calcular el número total de páginas
$total_pages = ceil($total_rows / $records_per_page);
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
                                    <option value="">Seleccione</option>
                                    <?php
                                    $permisos = getTpPermisos();
                                    foreach ($permisos as $permiso) {
                                        $selected = ($permiso->id_tppermi == ($autorizacion['id_tppermi'] ?? '')) ? 'selected' : '';
                                        echo "<option value='{$permiso->id_tppermi}' $selected>{$permiso->des_tppermi}</option>";
                                    }
                                    ?>
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
                                <button type="submit" class="btn btn-primary w-100">Buscar Todo</button>
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
                                    <th class="px-1">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($autorizaciones as $autorizacion): ?>
                                    <tr style="text-transform: uppercase;">
                                        <td class="text-center"><?= htmlspecialchars($autorizacion['nro_kardex']) ?></td>
                                        <td class="text-center"><?= htmlspecialchars($autorizacion['encargado']) ?></td>
                                        <td><?= nl2br(htmlspecialchars($autorizacion['participantes'] ?? 'No existen participantes')) ?>
                                        </td>
                                        <td class="text-center"><?= htmlspecialchars($autorizacion['tipo_permiso']) ?></td>
                                        <td><?= htmlspecialchars($autorizacion['fecha_ingreso']) ?></td>
                                        <td><?= htmlspecialchars(mb_strimwidth($autorizacion['observaciones'] ?? 'N/A', 0, 10, '...')) ?>
                                        </td>
                                        <td>
                                            <a href="edit_auto.php?id=<?= $autorizacion['id_autorizacion'] ?>"
                                                class="btn btn-sm btn-warning"><svg xmlns="http://www.w3.org/2000/svg"
                                                    width="15" height="20" fill="white" class="bi bi-pencil-fill"
                                                    viewBox="0 0 16 16">
                                                    <path
                                                        d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z" />
                                                </svg></a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        <!-- Paginación -->
                        <nav>
                            <ul class="pagination justify-content-center">
                                <?php
                                $range = 2; // Número de páginas a mostrar antes y después de la actual
                                $initial_num = $page - $range;
                                $condition_limit_num = ($page + $range) + 1;

                                if ($initial_num > 1) {
                                    echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
                                    if ($initial_num > 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }

                                for ($i = $initial_num; $i < $condition_limit_num; $i++) {
                                    if ($i > 0 && $i <= $total_pages) {
                                        $active = ($i === $page) ? 'active' : '';
                                        echo '<li class="page-item ' . $active . '"><a class="page-link" href="?page=' . $i . '">' . $i . '</a></li>';
                                    }
                                }

                                if ($condition_limit_num <= $total_pages) {
                                    if ($condition_limit_num < $total_pages) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="?page=' . $total_pages . '">' . $total_pages . '</a></li>';
                                }
                                ?>
                            </ul>
                        </nav>

                    </div>
                </div>
            </div>
        </div>
    </div>


    <?php
    // Asegúrate de que la ruta de footer.php sea correcta
    require 'includes/footer.php';
    ?>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $("form").on("submit", function (event) {
                event.preventDefault();

                let formData = {
                    tipoPermiso: $("#tipo-permiso").val(),
                    nroCrono: $("#nro-crono").val(),
                    encargado: $("#encargado").val(),
                    nombreParticipante: $("#nombre-participante").val(),
                    fechaMin: $("#fecha-min").val(),
                    fechaMax: $("#fecha-max").val()
                };

                $.ajax({
                    type: "POST",
                    url: "buscar_auto.php",
                    data: formData,
                    beforeSend: function () {
                        $("tbody").html(
                            "<tr><td colspan='7' class='text-center'>Buscando...</td></tr>");
                    },
                    success: function (response) {
                        $("tbody").html(response);
                    },
                    error: function () {
                        $("tbody").html(
                            "<tr><td colspan='7' class='text-center text-danger'>Error en la búsqueda</td></tr>"
                        );
                    }
                });
            });
        });

        $(document).ready(function () {
            $('.pagination a').on('click', function (e) {
                e.preventDefault();
                let page = $(this).attr('href').split('page=')[1];
                $.ajax({
                    url: 'home.php?page=' + page,
                    type: 'GET',
                    success: function (data) {
                        $('tbody').html($(data).find('tbody').html());
                        $('.pagination').html($(data).find('.pagination').html());
                    }
                });
            });
        });
    </script>

</body>

</html>