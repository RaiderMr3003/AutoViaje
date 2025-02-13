<?php
session_start();
require '../../config/conexion.php';

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(["error" => "No autorizado"]);
    exit;
}

// Recibir fechas
$fechaMin = $_POST['fecha_min'] ?? null;
$fechaMax = $_POST['fecha_max'] ?? null;

try {
    // Construir la consulta con filtros
    $sql = "
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
        WHERE 1 = 1
    ";

    $params = [];

    if ($fechaMin) {
        $sql .= " AND a.fecha_ingreso >= ?";
        $params[] = $fechaMin;
    }
    if ($fechaMax) {
        $sql .= " AND a.fecha_ingreso <= ?";
        $params[] = $fechaMax;
    }

    $sql .= " GROUP BY a.id_autorizacion ORDER BY a.id_autorizacion ASC";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $autorizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($autorizaciones);
} catch (PDOException $e) {
    echo json_encode(["error" => "Error al obtener autorizaciones: " . $e->getMessage()]);
}
?>
