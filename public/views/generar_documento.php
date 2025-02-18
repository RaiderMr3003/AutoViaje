<?php
require '../../vendor/autoload.php';

use PhpOffice\PhpWord\TemplateProcessor;

$id_autorizacion = $_POST['id'] ?? null;

if ($id_autorizacion) {
    require '../../config/conexion.php';

    // Obtener los datos de la autorización
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

        // Cargar la plantilla
        $templateProcessor = new TemplateProcessor($plantilla);

        // Reemplazo de datos generales
        $templateProcessor->setValue('kardex', $autorizacion['nro_kardex']);
        $templateProcessor->setValue('encargado', $autorizacion['encargado']);
        $templateProcessor->setValue('tipo_permiso', $nombrePermiso);
        $templateProcessor->setValue('fecha_ingreso', $autorizacion['fecha_ingreso']);
        $templateProcessor->setValue('viaja_a', $autorizacion['viaja_a']);
        $templateProcessor->setValue('observaciones', $autorizacion['observaciones']);
        $templateProcessor->setValue('tiempo_viaje', $autorizacion['tiempo_viaje']);


        $queryTransporte = "SELECT des_tptrans FROM tp_transporte WHERE id_tptrans = ?";
        $stmtTransporte = $pdo->prepare($queryTransporte);
        $stmtTransporte->execute([$autorizacion['id_tptrans']]);
        $transporte = $stmtTransporte->fetch(PDO::FETCH_ASSOC);
        $nombreTransporte = $transporte ? $transporte['des_tptrans'] : 'Desconocido';

        // Reemplazo de datos en la plantilla
        $templateProcessor->setValue('tp_transporte', $nombreTransporte);

        // Obtener datos del padre y la madre
        $queryPadres = "SELECT 
                            pa.id_tp_relacion,
                            td.abrev_tpdoc AS tipo_documento,
                            p.num_doc AS num_documento,
                            CONCAT(p.nombres, ' ', p.apellidos) AS nombre,
                            p.direccion,
                            u.nom_dis AS distrito,
                            u.nom_prov AS provincia,
                            u.nom_dpto AS departamento,
                            n.gentilicio AS nacionalidad
                        FROM personas_autorizaciones pa 
                        JOIN personas p ON pa.id_persona = p.id_persona 
                        JOIN tp_documento td ON p.id_tpdoc = td.id_tpdoc
                        LEFT JOIN ubigeo u ON p.id_ubigeo = u.id_ubigeo
                        LEFT JOIN nacionalidades n ON p.id_nacionalidad = n.id_nacionalidad
                        WHERE pa.id_autorizacion = ?
                        AND (pa.id_tp_relacion = (SELECT id_tp_relacion FROM tp_relacion WHERE descripcion = 'Madre') 
                        OR pa.id_tp_relacion = (SELECT id_tp_relacion FROM tp_relacion WHERE descripcion = 'Padre'))";

        $stmtPadres = $pdo->prepare($queryPadres);
        $stmtPadres->execute([$id_autorizacion]);
        $padres = $stmtPadres->fetchAll(PDO::FETCH_ASSOC);


        // Construcción del texto de los padres
        $textoPadreMadre = "";
        foreach ($padres as $padreMadre) {
            $descripcion = ($padreMadre['id_tp_relacion'] == 1) ? "MADRE" : "PADRE";
            if (!empty($textoPadreMadre)) {
                $textoPadreMadre .= " Y ";
            }
            $textoPadreMadre .= "{$padreMadre['nombre']}, CON {$padreMadre['tipo_documento']} N° {$padreMadre['num_documento']}, DE NACIONALIDAD {$padreMadre['nacionalidad']}, DOMICILIADO(A) EN {$padreMadre['direccion']}, DISTRITO DE {$padreMadre['distrito']}, PROVINCIA DE {$padreMadre['provincia']}, DEPARTAMENTO DE {$padreMadre['departamento']}";
        }

        // Si no hay padres registrados
        if (empty($textoPadreMadre)) {
            $textoPadreMadre = "No se han registrado padres o tutores.";
        }

        // Reemplazo en la plantilla
        $templateProcessor->setValue('padres_info', $textoPadreMadre);

        // Obtener datos de los menores
        $queryMenores = "SELECT 
                            td.abrev_tpdoc AS tipo_documento,
                            p.num_doc AS num_documento,
                            CONCAT(p.apellidos, ' ', p.nombres) AS nombre,
                            p.edad AS edad
                        FROM personas_autorizaciones pa 
                        JOIN personas p ON pa.id_persona = p.id_persona 
                        JOIN tp_documento td ON p.id_tpdoc = td.id_tpdoc
                        WHERE pa.id_autorizacion = ? 
                        AND pa.id_tp_relacion = (SELECT id_tp_relacion FROM tp_relacion WHERE descripcion = 'Menor')";
        $stmtMenores = $pdo->prepare($queryMenores);
        $stmtMenores->execute([$id_autorizacion]);
        $menores = $stmtMenores->fetchAll(PDO::FETCH_ASSOC);

        // Construcción de la lista de menores
        $listaMenores = "";
        foreach ($menores as $menor) {
            $listaMenores .= "{$menor['nombre']}, IDENTIFICADO(A) CON {$menor['tipo_documento']}: {$menor['num_documento']}============== \nDE: {$menor['edad']} AÑOS DE EDAD.============================================================\n";
        }

        // Si no hay menores registrados
        if (empty($listaMenores)) {
            $listaMenores = "No hay menores registrados.";
        }

        // Reemplazo en la plantilla
        $templateProcessor->setValue('lista_menores', $listaMenores);


        // Obtener los firmantes
        $queryFirmantes = "SELECT 
                                CONCAT(p.apellidos, ' ', p.nombres) AS nombre_completo,
                                pa.firma
                            FROM personas_autorizaciones pa
                            JOIN personas p ON pa.id_persona = p.id_persona
                            WHERE pa.id_autorizacion = ?
                            AND pa.firma IN ('SI', 'HUELLA')";

        $stmtFirmantes = $pdo->prepare($queryFirmantes);
        $stmtFirmantes->execute([$id_autorizacion]);
        $firmantes = $stmtFirmantes->fetchAll(PDO::FETCH_ASSOC);

        // Construcción de la lista de firmantes
        $listaFirmantes = "";
        foreach ($firmantes as $firmante) {
            $listaFirmantes .= "{$firmante['nombre_completo']} ({$firmante['firma']})           ";
        }

        // Si no hay firmantes registrados
        if (empty($listaFirmantes)) {
            $listaFirmantes = "No se han registrado firmantes.";
        }

        // Reemplazo en la plantilla
        $templateProcessor->setValue('firmantes', $listaFirmantes);


        // Determinar el nombre del archivo de salida
        if (!empty($autorizacion['nro_kardex'])) {
            $outputFile = "Autorizacion_viaje_{$autorizacion['nro_kardex']}.docx";
        } else {
            $outputFile = "Autorizacion_ejemplo_{$id_autorizacion}.docx";
        }

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
