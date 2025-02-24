<?php
require '../../vendor/autoload.php';


use PhpOffice\PhpWord\TemplateProcessor;
use PhpOffice\PhpWord\Element\TextRun;

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
        $templateProcessor->setValue('kardex', strtoupper($autorizacion['nro_kardex']));
        $templateProcessor->setValue('encargado', strtoupper($autorizacion['encargado']));
        $templateProcessor->setValue('tipo_permiso', strtoupper($nombrePermiso));
        $templateProcessor->setValue('fecha_ingreso', fechaEnLetras($autorizacion['fecha_ingreso']));
        $templateProcessor->setValue('viaja_a', strtoupper($autorizacion['viaja_a']));
        $templateProcessor->setValue('observaciones', strtoupper($autorizacion['observaciones']));
        $templateProcessor->setValue('tiempo_viaje', strtoupper($autorizacion['tiempo_viaje']));


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


        $textoPadreMadre = new TextRun();

        foreach ($padres as $padreMadre) {
            $descripcion = ($padreMadre['id_tp_relacion'] == 1) ? "MADRE" : "PADRE";

            if ($textoPadreMadre->countElements() > 0) {
                $textoPadreMadre->addText(" Y ");
            }

            // Añadir el nombre en negrita
            $textoPadreMadre->addText(strtoupper($padreMadre['nombre']), ['bold' => true]);
            $textoPadreMadre->addText(", CON ");

            // Añadir el tipo de documento en negrita
            $textoPadreMadre->addText($padreMadre['tipo_documento'], ['bold' => true]);
            $textoPadreMadre->addText(" N° ", ['bold' => true]);

            // Añadir el número de documento en negrita
            $textoPadreMadre->addText($padreMadre['num_documento'], ['bold' => true]);

            // Resto del texto normal
            $textoPadreMadre->addText(", DE NACIONALIDAD {$padreMadre['nacionalidad']}, DOMICILIADO(A) EN {$padreMadre['direccion']}, DISTRITO DE {$padreMadre['distrito']}, PROVINCIA DE {$padreMadre['provincia']}, DEPARTAMENTO DE {$padreMadre['departamento']}");
        }

        // Si no hay padres registrados
        if ($textoPadreMadre->countElements() === 0) {
            $textoPadreMadre->addText("No se han registrado padres o tutores.");
        }

        // Reemplazo en la plantilla
        $templateProcessor->setComplexValue('padres_info', $textoPadreMadre);

        // Obtener datos de los menores
        $queryMenores = "SELECT 
                            td.abrev_tpdoc AS tipo_documento,
                            p.num_doc AS num_documento,
                            CONCAT(p.nombres, ' ', p.apellidos) AS nombre,
                            p.edad AS edad,
                            p.tipo_edad AS tipo_edad
                        FROM personas_autorizaciones pa 
                        JOIN personas p ON pa.id_persona = p.id_persona 
                        JOIN tp_documento td ON p.id_tpdoc = td.id_tpdoc
                        WHERE pa.id_autorizacion = ? 
                        AND pa.id_tp_relacion = (SELECT id_tp_relacion FROM tp_relacion WHERE descripcion = 'Menor')";
        $stmtMenores = $pdo->prepare($queryMenores);
        $stmtMenores->execute([$id_autorizacion]);
        $menores = $stmtMenores->fetchAll(PDO::FETCH_ASSOC);

        $menorhijo = "";

        // Verificar la cantidad de menores
        if (count($menores) > 1) {
            $menorhijo = "MIS MENORES HIJOS()";
        } elseif (count($menores) === 1) {
            $menorhijo = "MI MENOR HIJO(A)";
        } else {
            $menorhijo = "No se han registrado menores.";
        }

        // Reemplazo en la plantilla para $menorhijo
        $templateProcessor->setValue('menorhijo', $menorhijo);

        // Construcción de la lista de menores
        $listaMenores = new TextRun();

        foreach ($menores as $menor) {
            if ($listaMenores->countElements() > 0) {
                $listaMenores->addText(" Y ");
            }

            // Añadir el nombre del menor en negrita
            $listaMenores->addText(strtoupper($menor['nombre']), ['bold' => true]);
            $listaMenores->addText(", IDENTIFICADO(A) CON "); // Texto normal

            // Añadir el tipo de documento en negrita
            $listaMenores->addText($menor['tipo_documento'], ['bold' => true]);
            $listaMenores->addText(" N° ", ['bold' => true]);

            // Añadir el número de documento en negrita
            $listaMenores->addText($menor['num_documento'], ['bold' => true]);
            $listaMenores->addTextBreak(); // Salto de línea

            // Resto del texto normal
            $listaMenores->addText("DE: "); // Texto normal
            $listaMenores->addText("{$menor['edad']} {$menor['tipo_edad']} DE EDAD.", ['bold' => true]);
        }

        // Reemplazo en la plantilla
        $templateProcessor->setComplexValue('lista_menores', $listaMenores);



        // Obtener los firmantes
        $queryFirmantes = "SELECT 
                                CONCAT(p.nombres, ' ', p.apellidos) AS nombre_completo,
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
            $listaFirmantes .= "{$firmante['nombre_completo']}\n \n"; // Salto de línea
        }

        // Si no hay firmantes registrados
        if (empty($listaFirmantes)) {
            $listaFirmantes = "No se han registrado firmantes.";
        }

        // Reemplazo en la plantilla
        $templateProcessor->setValue('firmantes', strtoupper($listaFirmantes));
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

function fechaEnLetras($fecha)
{
    $dias = [
        1 => 'PRIMERO',
        2 => 'DOS',
        3 => 'TRES',
        4 => 'CUATRO',
        5 => 'CINCO',
        6 => 'SEIS',
        7 => 'SIETE',
        8 => 'OCHO',
        9 => 'NUEVE',
        10 => 'DIEZ',
        11 => 'ONCE',
        12 => 'DOCE',
        13 => 'TRECE',
        14 => 'CATORCE',
        15 => 'QUINCE',
        16 => 'DIECISÉIS',
        17 => 'DIECISIETE',
        18 => 'DIECIOCHO',
        19 => 'DIECINUEVE',
        20 => 'VEINTE',
        21 => 'VEINTIUNO',
        22 => 'VEINTIDÓS',
        23 => 'VEINTITRÉS',
        24 => 'VEINTICUATRO',
        25 => 'VEINTICINCO',
        26 => 'VEINTISÉIS',
        27 => 'VEINTISIETE',
        28 => 'VEINTIOCHO',
        29 => 'VEINTINUEVE',
        30 => 'TREINTA',
        31 => 'TREINTA Y UNO'
    ];

    $meses = [
        1 => 'ENERO',
        2 => 'FEBRERO',
        3 => 'MARZO',
        4 => 'ABRIL',
        5 => 'MAYO',
        6 => 'JUNIO',
        7 => 'JULIO',
        8 => 'AGOSTO',
        9 => 'SEPTIEMBRE',
        10 => 'OCTUBRE',
        11 => 'NOVIEMBRE',
        12 => 'DICIEMBRE'
    ];

    $anios = [
        '2020' => 'DOS MIL VEINTE',
        '2021' => 'DOS MIL VEINTIUNO',
        '2022' => 'DOS MIL VEINTIDOS',
        '2023' => 'DOS MIL VEINTITRÉS',
        '2024' => 'DOS MIL VEINTICUATRO',
        '2025' => 'DOS MIL VEINTICINCO',
        '2026' => 'DOS MIL VEINTISÉIS',
        '2027' => 'DOS MIL VEINTISIETE',
        '2028' => 'DOS MIL VEINTIOCHO',
        '2029' => 'DOS MIL VEINTINUEVE',
        '2030' => 'DOS MIL TREINTA',
        '2031' => 'DOS MIL TREINTA Y UNO',
        '2032' => 'DOS MIL TREINTA Y DOS',
        '2033' => 'DOS MIL TREINTA Y TRES',
        '2034' => 'DOS MIL TREINTA Y CUATRO',
        '2035' => 'DOS MIL TREINTA Y CINCO',
        '2036' => 'DOS MIL TREINTA Y SEIS',
        '2037' => 'DOS MIL TREINTA Y SIETE',
        '2038' => 'DOS MIL TREINTA Y OCHO',
        '2039' => 'DOS MIL TREINTA Y NUEVE',
        '2040' => 'DOS MIL CUARENTA'
    ];

    $fechaPartes = explode('-', $fecha);
    if (count($fechaPartes) == 3) {
        $anio = $fechaPartes[0];
        $mes = (int)$fechaPartes[1];
        $dia = (int)$fechaPartes[2];

        // Verificar si el año está en el array, si no, convertirlo
        $anioEnLetras = isset($anios[$anio]) ? $anios[$anio] : convertirAnioEnLetras($anio);

        return "{$dias[$dia]} DE {$meses[$mes]} DE {$anioEnLetras}";
    }
    return $fecha;
}

// Función para convertir cualquier año a letras (opcional si quieres cubrir años futuros)
function convertirAnioEnLetras($anio)
{
    $unidades = ['CERO', 'UNO', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
    $miles = (int)($anio / 1000);
    $centenas = $anio % 1000;

    $anioLetras = ($miles > 0 ? $unidades[$miles] . ' MIL ' : '') . ($centenas > 0 ? $centenas : '');
    return strtoupper(trim($anioLetras));
}
