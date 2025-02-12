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
            a.observaciones 
        FROM autorizaciones a
        JOIN tp_permiso tp ON a.id_tppermi = tp.id_tppermi
        order by a.id_autorizacion ASC
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
                                <label for="nro-control" class="form-label">N° de Control</label>
                                <input type="text" id="nro-control" name="nro-control" class="form-control">
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
                        <table class="table table-striped table-hover text-center table-bordered">
                            <thead class="table-dark">
                                <tr class="">
                                    <th>N° Control</th>
                                    <th>N° Cronológico</th>
                                    <th>Encargado</th>
                                    <th>Participantes</th>
                                    <th>Tipo de Permiso</th>
                                    <th>Fecha</th>
                                    <th>Observaciones</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($autorizaciones as $autorizacion) : ?>
                                <tr>
                                    <td><?= htmlspecialchars($autorizacion['id_autorizacion']) ?></td>
                                    <td><?= htmlspecialchars($autorizacion['nro_kardex']) ?></td>
                                    <td><?= htmlspecialchars($autorizacion['encargado']) ?></td>
                                    <td><?= htmlspecialchars($autorizacion['encargado']) ?></td>
                                    <td><?= htmlspecialchars($autorizacion['tipo_permiso']) ?></td>
                                    <td><?= htmlspecialchars($autorizacion['fecha_ingreso']) ?></td>
                                    <td><?= htmlspecialchars($autorizacion['observaciones'] ?? 'N/A') ?></td>
                                    <td>
                                        <a href="edit_auto.php?id=<?= $autorizacion['id_autorizacion'] ?>"
                                            class="btn btn-sm btn-warning">Editar</a>
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