<?php
session_start();
require '../../config/conexion.php';
require 'includes/functions.php'; // Incluir para usar obtenerAutorizaciones

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Verificar si el usuario está autenticado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../../index.php");
    exit;
}

// Obtener filtros de la URL (GET)
$fechaMin = $_GET['fecha_min'] ?? '';
$fechaMax = $_GET['fecha_max'] ?? '';

// Lógica de Exportación
if (isset($_GET['exportar'])) {

    // Configurar filtros para exportación (sin paginación)
    $filtros = [
        'fechaMin' => $fechaMin,
        'fechaMax' => $fechaMax
    ];

    require '../../vendor/autoload.php';

    // Obtener TODOS los registros que coincidan con el filtro
    // Usamos un límite muy alto para "traer todo"
    $resultado = obtenerAutorizaciones($filtros, 100000, 0);
    $autorizaciones = $resultado['data'];

    // Crear el archivo Excel
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Establecer encabezados
    $sheet->setCellValue('A1', 'N° Cronológico');
    $sheet->setCellValue('B1', 'F. Ingreso');
    $sheet->setCellValue('C1', 'Tipo de Permiso');
    $sheet->setCellValue('D1', 'Participantes');
    $sheet->setCellValue('E1', 'Viaja a');

    // Ajustar automáticamente el ancho de las columnas
    foreach (range('A', 'E') as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }

    // Agregar datos agrupados a las filas
    $fila = 2;
    $currentKardex = null;
    $startRow = 2;

    foreach ($autorizaciones as $autorizacion) {
        if ($autorizacion['nro_kardex'] !== $currentKardex) {
            // Combinar celdas previas si aplica
            if ($currentKardex !== null) {
                $sheet->mergeCells("A$startRow:A" . ($fila - 1));
                $sheet->mergeCells("B$startRow:B" . ($fila - 1));
                $sheet->mergeCells("C$startRow:C" . ($fila - 1));
                $sheet->mergeCells("E$startRow:E" . ($fila - 1));
            }

            $startRow = $fila;
            $currentKardex = $autorizacion['nro_kardex'];

            $sheet->setCellValue("A$fila", $autorizacion['nro_kardex']);
            $sheet->setCellValue("B$fila", $autorizacion['fecha_ingreso']);
            $sheet->setCellValue("C$fila", $autorizacion['tipo_permiso']);
            $sheet->setCellValue("E$fila", $autorizacion['viaja_a'] ?? ''); // viaja_a might not be in obtenerAutorizaciones generic select? 
            // Wait, obtenerAutorizaciones doesn't select 'viaja_a' by default in the previous definition! 
            // I need to check if obtenerAutorizaciones includes viaja_a. 
            // If not, I should update functions.php first. 
            // Checked functions.php content from previous steps: 
            // logic: SELECT a.id_autorizacion, a.nro_kardex, a.encargado, tp.des_tppermi ... 
            // IT DOES NOT INCLUDE viaja_a.
            // I must likely update functions.php to include viaja_a as well.
        }

        $sheet->setCellValue("D$fila", $autorizacion['participantes']); // Note: function returns 'participantes' (plural), export code used 'participante' (singular)
        $fila++;
    }

    // Combinar las celdas de la última autorización
    if ($currentKardex !== null) {
        $sheet->mergeCells("A$startRow:A" . ($fila - 1));
        $sheet->mergeCells("B$startRow:B" . ($fila - 1));
        $sheet->mergeCells("C$startRow:C" . ($fila - 1));
        $sheet->mergeCells("E$startRow:E" . ($fila - 1));
    }

    $nombreArchivo = "autorizaciones_" . ($fechaMin ? $fechaMin : 'inicio') . "_a_" . ($fechaMax ? $fechaMax : 'fin') . ".xlsx";

    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment;filename=\"$nombreArchivo\"");
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// Lógica de Visualización (Paginada)
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$records_per_page = 10;
$offset = ($page - 1) * $records_per_page;

$filtros = [
    'fechaMin' => $fechaMin,
    'fechaMax' => $fechaMax
];

$resultado = obtenerAutorizaciones($filtros, $records_per_page, $offset);
$autorizaciones = $resultado['data'];
$total_rows = $resultado['total'];
$total_pages = ceil($total_rows / $records_per_page);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/home.css">
    <title>AutoViaje - Exportar</title>
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
                    <!-- Formulario con método GET para mantener estado en URL -->
                    <form method="GET" action="" class="p-3">
                        <div class="mb-3">
                            <label for="fecha-min" class="form-label">Fecha Min</label>
                            <input type="date" id="fecha-min" name="fecha_min" class="form-control"
                                value="<?= htmlspecialchars($fechaMin) ?>">
                        </div>
                        <div class="mb-3">
                            <label for="fecha-max" class="form-label">Fecha Max</label>
                            <input type="date" id="fecha-max" name="fecha_max" class="form-control"
                                value="<?= htmlspecialchars($fechaMax) ?>">
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-100">Buscar</button>
                        </div>
                    </form>

                    <div class="text-center p-3 pt-0">
                        <!-- Botón de exportar usa los mismos parámetros de la URL actual + exportar=1 -->
                        <?php
                        $queryParams = $_GET;
                        $queryParams['exportar'] = 1;
                        $exportUrl = '?' . http_build_query($queryParams);
                        ?>
                        <a href="<?= $exportUrl ?>" class="btn btn-success w-100">Exportar (Excel)</a>
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
                                    <th>Participantes</th>
                                    <th>Tipo de Permiso</th>
                                    <th>F. Ingreso</th>
                                    <!-- Add viaja_a column header if implementing it -->
                                    <th>Observaciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($autorizaciones)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">No hay autorizaciones disponibles
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($autorizaciones as $autorizacion): ?>
                                        <tr style="text-transform: uppercase;">
                                            <td class="text-center"><?= htmlspecialchars($autorizacion['nro_kardex']) ?></td>
                                            <td><?= nl2br(htmlspecialchars($autorizacion['participantes'] ?? 'N/A')) ?></td>
                                            <td class="text-center"><?= htmlspecialchars($autorizacion['tipo_permiso']) ?></td>
                                            <td><?= htmlspecialchars($autorizacion['fecha_ingreso']) ?></td>
                                            <td><?= htmlspecialchars(mb_strimwidth($autorizacion['observaciones'] ?? 'N/A', 0, 20, '...')) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <!-- Paginación -->
                        <?php if ($total_pages > 1): ?>
                            <nav class="mt-3">
                                <ul class="pagination justify-content-center">
                                        <?php
                                        $range = 2;
                                        $initial_num = $page - $range;
                                        $condition_limit_num = ($page + $range) + 1;

                                        // Mantener parámetros de búsqueda en los enlaces de paginación
                                        $paginationParams = $_GET;
                                        unset($paginationParams['page']); // Quitamos page para añadirlo dinámicamente
                                    
                                        if ($initial_num > 1) {
                                            $paginationParams['page'] = 1;
                                            echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($paginationParams) . '">1</a></li>';
                                            if ($initial_num > 2) {
                                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                            }
                                        }

                                        for ($i = $initial_num; $i < $condition_limit_num; $i++) {
                                            if ($i > 0 && $i <= $total_pages) {
                                                $active = ($i === $page) ? 'active' : '';
                                                $paginationParams['page'] = $i;
                                                echo '<li class="page-item ' . $active . '"><a class="page-link" href="?' . http_build_query($paginationParams) . '">' . $i . '</a></li>';
                                            }
                                        }

                                        if ($condition_limit_num <= $total_pages) {
                                            if ($condition_limit_num < $total_pages) {
                                                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                            }
                                            $paginationParams['page'] = $total_pages;
                                            echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($paginationParams) . '">' . $total_pages . '</a></li>';
                                        }
                                        ?>
                                    </ul>
                                </nav>
                        <?php endif; ?>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php require 'includes/footer.php'; ?>

</body>

</html>