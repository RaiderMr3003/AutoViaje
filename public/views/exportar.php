<?php
session_start();
require '../../config/conexion.php';

// Verificar si el usuario está autenticado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../index.php");
    exit;
}

    require '../../vendor/autoload.php'; // Cargar PhpSpreadsheet

    use PhpOffice\PhpSpreadsheet\Spreadsheet;
    use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

    if (isset($_GET['exportar'])) {
        $where = "";
        $fechaMin = $_GET['fecha_min'] ?? date('Y-m-d');
        $fechaMax = $_GET['fecha_max'] ?? date('Y-m-d');
    
        if (!empty($_GET['fecha_min']) && !empty($_GET['fecha_max'])) {
            $where = " WHERE a.fecha_ingreso BETWEEN :fecha_min AND :fecha_max ";
        }
    
        $query = "
            SELECT 
                a.id_autorizacion AS `N° Crono.`,
                a.nro_kardex,
                a.encargado,
                CONCAT(tr.descripcion, ': ', p.apellidos, ', ', p.nombres) AS participante,
                tp.des_tppermi AS `Tipo de Permiso`,
                a.fecha_ingreso AS `F. Ingreso`,
                a.observaciones
            FROM autorizaciones a
            JOIN tp_permiso tp ON a.id_tppermi = tp.id_tppermi
            LEFT JOIN personas_autorizaciones pa ON a.id_autorizacion = pa.id_autorizacion
            LEFT JOIN personas p ON pa.id_persona = p.id_persona
            LEFT JOIN tp_relacion tr ON pa.id_tp_relacion = tr.id_tp_relacion
            $where
            ORDER BY a.id_autorizacion ASC";
    
        $stmt = $pdo->prepare($query);
        if (!empty($_GET['fecha_min']) && !empty($_GET['fecha_max'])) {
            $stmt->bindParam(':fecha_min', $_GET['fecha_min']);
            $stmt->bindParam(':fecha_max', $_GET['fecha_max']);
        }
        $stmt->execute();
        $autorizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        // Crear el archivo Excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        // Establecer encabezados
        $columnas = array_keys($autorizaciones[0]);
        $col = 'A';
        foreach ($columnas as $columna) {
            $sheet->setCellValue($col . '1', $columna);
            $col++;
        }
    
        // Agregar datos a las filas
        $fila = 2;
        foreach ($autorizaciones as $autorizacion) {
            $col = 'A';
            foreach ($autorizacion as $valor) {
                $sheet->setCellValue($col . $fila, $valor);
                $col++;
            }
            $fila++;
        }
    
        // Nombre del archivo con fecha
        $nombreArchivo = "autorizaciones_{$fechaMin}_a_{$fechaMax}.xlsx";
    
        // Descargar el archivo
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header("Content-Disposition: attachment;filename=\"$nombreArchivo\"");
        header('Cache-Control: max-age=0');
    
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }

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
                CONCAT(tr.descripcion, ': ', p.apellidos, ', ', p.nombres) SEPARATOR '\n'
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
    die("Error al obtener autorizaciones: " . htmlspecialchars($e->getMessage()));
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home.css">
    <title>AutoViaje</title>
</head>

<body>
    <header>
        <?php require 'includes/header.php'; ?>
    </header>

    <div class="container-fluid my-3" style="padding-left: 7rem; padding-right: 7rem;">
        <div class="row">
            <!-- Panel de Búsqueda -->
            <div class="col-md-3">
                <div class="card shadow">
                    <div class="card-header bg-dark text-white text-center">
                        <h6 class="mb-1">Buscar Autorizaciones</h6>
                    </div>
                    <form id="form-buscar" class="p-3">
                        <div class="mb-3">
                            <label for="fecha-min" class="form-label">Fecha Min</label>
                            <input type="date" id="fecha-min" name="fecha_min" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="fecha-max" class="form-label">Fecha Max</label>
                            <input type="date" id="fecha-max" name="fecha_max" class="form-control">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-100">Buscar</button>
                        </div>
                    </form>
                    <div class="text-center">
                        <button onclick="exportarExcel()" class="btn btn-success w-100">Exportar</button>
                    </div>
                </div>
            </div>

            <!-- Tabla de Resultados -->
            <div class="col-md-9 mb-4">
                <div class="card shadow">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover table-bordered">
                            <thead class="table-dark text-center">
                                <tr>
                                    <th>N° Crono.</th>
                                    <th>Encargado</th>
                                    <th>Participantes</th>
                                    <th>Tipo de Permiso</th>
                                    <th>F. Ingreso</th>
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($autorizaciones)) : ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">No hay autorizaciones disponibles</td>
                                    </tr>
                                <?php else : ?>
                                    <?php foreach ($autorizaciones as $autorizacion) : ?>
                                        <tr style="text-transform: uppercase;">
                                            <td class="text-center"><?= htmlspecialchars($autorizacion['nro_kardex']) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($autorizacion['encargado']) ?></td>
                                            <td><?= nl2br(htmlspecialchars($autorizacion['participantes'] ?? 'N/A')) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($autorizacion['tipo_permiso']) ?></td>
                                            <td><?= htmlspecialchars($autorizacion['fecha_ingreso']) ?></td>
                                            <td><?= htmlspecialchars(mb_strimwidth($autorizacion['observaciones'] ?? 'N/A', 0, 10, '...')) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require 'includes/footer.php'; ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            document.getElementById("form-buscar").addEventListener("submit", function(event) {
                event.preventDefault();

                let fechaMin = document.getElementById("fecha-min").value;
                let fechaMax = document.getElementById("fecha-max").value;

                fetch("buscar_exp.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: new URLSearchParams({
                            fecha_min: fechaMin,
                            fecha_max: fechaMax
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        let tbody = document.querySelector("tbody");
                        tbody.innerHTML = "";

                        if (data.error) {
                            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">${data.error}</td></tr>`;
                            return;
                        }

                        if (data.length === 0) {
                            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-muted">No hay autorizaciones disponibles</td></tr>`;
                            return;
                        }

                        data.forEach(autorizacion => {
                            let fila = `<tr style="text-transform: uppercase;">
                            <td class="text-center">${autorizacion.nro_kardex}</td>
                            <td class="text-center">${autorizacion.encargado}</td>
                            <td>${autorizacion.participantes ? autorizacion.participantes.replace(/\n/g, "<br>") : "N/A"}</td>
                            <td class="text-center">${autorizacion.tipo_permiso}</td>
                            <td>${autorizacion.fecha_ingreso}</td>
                            <td>${autorizacion.observaciones ? autorizacion.observaciones.substring(0, 10) + '...' : 'N/A'}</td>
                        </tr>`;
                            tbody.innerHTML += fila;
                        });
                    })
                    .catch(error => {
                        console.error("Error:", error);
                        let tbody = document.querySelector("tbody");
                        tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">Error al buscar autorizaciones</td></tr>`;
                    });
            });
        });

        function exportarExcel() {
            let fechaMin = document.getElementById("fecha-min").value;
            let fechaMax = document.getElementById("fecha-max").value;

            let url = window.location.href.split('?')[0] + "?exportar=1";
            if (fechaMin && fechaMax) {
                url += `&fecha_min=${fechaMin}&fecha_max=${fechaMax}`;
            }

            window.location.href = url;
        }
    </script>

</body>

</html>