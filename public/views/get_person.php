<?php
require '../../config/conexion.php';

if (!isset($_GET['id_autorizacion'])) {
    echo json_encode(["error" => "ID de autorización no proporcionado"]);
    exit;
}

$id_autorizacion = $_GET['id_autorizacion'];

try {
    $stmt = $pdo->prepare("
        SELECT p.id_persona, p.num_doc, p.apellidos, p.nombres, p.fecha_nacimiento 
        FROM personas p
        INNER JOIN personas_autorizaciones pa ON p.id_persona = pa.id_persona
        WHERE pa.id_autorizacion = :id_autorizacion
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
?>