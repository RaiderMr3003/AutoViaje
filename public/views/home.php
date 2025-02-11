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

    <div class="main-container">
        <!-- Panel de Búsqueda -->
        <div class="container">
            <h2>Autorizaciones de Viaje</h2><br>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="nro-control">N° de Control:</label>
                    <input type="text" id="nro-control" name="nro-control">
                </div>

                <div class="form-group">
                    <label for="nro-crono">N° Cronológico:</label>
                    <input type="text" id="nro-crono" name="nro-crono">
                </div>

                <div class="form-group">
                    <label for="tipo-permiso">Tipo de Permiso:</label>
                    <select id="tipo-permiso" name="tipo-permiso">
                        <option value="">Seleccione</option>
                        
                    </select>
                </div>

                <div class="form-group">
                    <label for="nombre-participante">Nombre del Participante:</label>
                    <input type="text" id="nombre-participante" name="nombre-participante">
                </div>

                <div class="form-group">
                    <label for="nombre-responsable">Nombre del Responsable:</label>
                    <input type="text" id="nombre-responsable" name="nombre-responsable">
                </div>

                <div class="form-group">
                    <label>Búsqueda por Fechas:</label>
                    <div class="date-range">
                        <div class="date-field">
                            <label for="fecha-min">Fecha Min</label>
                            <input type="date" id="fecha-min" name="fecha-min">
                        </div>
                        <div class="date-field">
                            <label for="fecha-max">Fecha Max</label>
                            <input type="date" id="fecha-max" name="fecha-max">
                        </div>
                    </div>
                </div>

                <button type="submit">Buscar</button>
            </form>

        </div>

        <!-- Tabla de Resultados -->
        <div class="table-container">
            <table id="resultados">
                <thead>
                    <tr>
                        <th>N° Control</th>
                        <th>N° Cronológico</th>
                        <th>Encargado</th>
                        <th>Participantes</th>
                        <th>Tipo de Permiso</th>
                        <th>Fecha</th>
                        <th>Descripción</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($autorizaciones as $autorizacion) : ?>
                        <tr>
                            <td><?= htmlspecialchars($autorizacion['id_autorizacion']) ?></td>
                            <td><?= htmlspecialchars($autorizacion['nro_kardex']) ?></td>
                            <td><?= htmlspecialchars($autorizacion['encargado']) ?></td>
                            <td><?= htmlspecialchars($autorizacion['tipo_permiso']) ?></td>
                            <td><?= htmlspecialchars($autorizacion['fecha_ingreso']) ?></td>
                            <td><?= htmlspecialchars($autorizacion['observaciones'] ?? 'N/A') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php
    // Asegúrate de que la ruta de footer.php sea correcta
    require 'includes/footer.php';
    ?>
</body>
</html>