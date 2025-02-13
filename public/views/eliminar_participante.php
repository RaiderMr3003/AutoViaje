<?php
require '../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id_persona = $_POST['id_persona'];
    $id_autorizacion = $_POST['id_autorizacion'];

    try {
        $pdo->beginTransaction(); // Inicia la transacción

        // 1. Eliminar relaciones en 'representantes' si existen
        $sql_representantes = "DELETE FROM representantes WHERE id_representante = :id_persona OR id_representado = :id_persona";
        $stmt1 = $pdo->prepare($sql_representantes);
        $stmt1->bindParam(":id_persona", $id_persona, PDO::PARAM_INT);
        $stmt1->execute();

        // 2. Eliminar la relación en 'personas_autorizaciones'
        $sql_personas_autorizaciones = "DELETE FROM personas_autorizaciones WHERE id_persona = :id_persona AND id_autorizacion = :id_autorizacion";
        $stmt2 = $pdo->prepare($sql_personas_autorizaciones);
        $stmt2->bindParam(":id_persona", $id_persona, PDO::PARAM_INT);
        $stmt2->bindParam(":id_autorizacion", $id_autorizacion, PDO::PARAM_INT);
        $stmt2->execute();

        $pdo->commit(); // Confirma la transacción

        echo json_encode(["success" => true]);
    } catch (PDOException $e) {
        $pdo->rollBack(); // Revierte cambios si hay error
        echo json_encode(["success" => false, "mensaje" => "Error: " . $e->getMessage()]);
    }
}
?>
