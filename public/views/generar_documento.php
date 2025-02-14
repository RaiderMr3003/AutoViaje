<?php
require '../../vendor/autoload.php';
use PhpOffice\PhpWord\TemplateProcessor;

$id_autorizacion = $_POST['id'] ?? null;

if ($id_autorizacion) {
    require '../../config/conexion.php';

    // Consulta para obtener los datos de la autorización
    $query = "SELECT * FROM autorizaciones WHERE id_autorizacion = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id_autorizacion]);
    $autorizacion = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($autorizacion) {
        // Obtener la descripción del tipo de permiso y seleccionar plantilla
        $queryPermiso = "SELECT id_tppermi, des_tppermi FROM tp_permiso WHERE id_tppermi = ?";
        $stmtPermiso = $pdo->prepare($queryPermiso);
        $stmtPermiso->execute([$autorizacion['id_tppermi']]);
        $permiso = $stmtPermiso->fetch(PDO::FETCH_ASSOC);
        $nombrePermiso = $permiso ? $permiso['des_tppermi'] : 'Desconocido';

        // Selección de plantilla según el tipo de permiso
        if ($permiso['id_tppermi'] == 1) {
            $plantilla = '../../Documentos/plantilla1.docx';
        } elseif ($permiso['id_tppermi'] == 2) {
            $plantilla = '../../Documentos/plantilla2.docx';
        } else {
            die("Tipo de permiso no reconocido.");
        }

        // Cargar la plantilla seleccionada
        $templateProcessor = new TemplateProcessor($plantilla);

        // Reemplazar valores en la plantilla
        $templateProcessor->setValue('kardex', $autorizacion['nro_kardex']);
        $templateProcessor->setValue('encargado', $autorizacion['encargado']);
        $templateProcessor->setValue('tipo_permiso', $nombrePermiso);
        $templateProcessor->setValue('fecha_ingreso', $autorizacion['fecha_ingreso']);
        $templateProcessor->setValue('viaja_a', $autorizacion['viaja_a']);
        $templateProcessor->setValue('observaciones', $autorizacion['observaciones']);

        // Guardar y descargar el documento
        $outputFile = "Autorizacion_{$id_autorizacion}.docx";
        $templateProcessor->saveAs($outputFile);

        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename={$outputFile}");
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document");
        readfile($outputFile);
        unlink($outputFile);
        exit;
    } else {
        echo "No se encontró la autorización.";
    }
} else {
    echo "ID no válida.";
}
?>
