<?php
require '../../config/conexion.php';

if (!isset($_GET['id_autorizacion'])) {
    echo json_encode(["error" => "ID de autorización no proporcionado"]);
    exit;
}

$id_autorizacion = $_GET['id_autorizacion'];

try {
    $stmt = $pdo->prepare("
    SELECT 
            p.id_persona, 
            p.num_doc, 
            CONCAT(p.apellidos, ', ', p.nombres) AS nombre_completo, 
            p.fecha_nacimiento, 
            tr.descripcion AS tipo_relacion, 
            pa.firma, 
            CASE 
                WHEN r.id_representante IS NOT NULL 
                THEN CONCAT('En representación de ', pr.apellidos, ', ', pr.nombres) 
                ELSE '' 
            END AS en_representacion
        FROM personas p
        INNER JOIN personas_autorizaciones pa ON p.id_persona = pa.id_persona
        INNER JOIN tp_relacion tr ON pa.id_tp_relacion = tr.id_tp_relacion
        LEFT JOIN representantes r ON p.id_persona = r.id_representante
        LEFT JOIN personas pr ON r.id_representado = pr.id_persona
        WHERE pa.id_autorizacion = :id_autorizacion
        ORDER BY p.id_persona
    ");
    $stmt->execute([':id_autorizacion' => $id_autorizacion]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);


    if (empty($result)) {
        echo json_encode(["mensaje" => "No hay participantes"]);
    } else {
        echo json_encode($result, JSON_UNESCAPED_UNICODE);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
