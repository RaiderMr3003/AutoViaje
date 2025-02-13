<?php
require '../../config/conexion.php'; // Asegúrate de incluir tu archivo de conexión

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_autorizacion = $_POST['id_autorizacion'];
    $id_tpdoc = $_POST['documento_persona'];
    $num_doc = $_POST['numdoc_persona'];
    $nombres = $_POST['nombre_persona'];
    $apellidos = $_POST['apellido_persona'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $id_nacionalidad = $_POST['nacionalidad'];
    $id_tp_relacion = $_POST['condicion'];
    $firma = $_POST['firma'];
    $en_representacion = isset($_POST['en_representacion']) ? 1 : 0;
    $representante = $_POST['representante_persona'] ?? null;

    // 🔹 CORRECCIÓN: Manejo de id_ubigeo y dirección según si es menor de edad
    if ($id_tp_relacion == 1) {
        $id_ubigeo = null;
        $direccion = null;
    } else {
        $id_ubigeo = !empty($_POST['Ubigeo_persona']) ? $_POST['Ubigeo_persona'] : null;
        $direccion = !empty($_POST['direccion_persona']) ? $_POST['direccion_persona'] : null;
    }

    try {
        $pdo->beginTransaction();

        // Verificar si la persona ya existe
        $stmt = $pdo->prepare("SELECT id_persona FROM personas WHERE num_doc = ?");
        $stmt->execute([$num_doc]);
        $persona = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($persona) {
            $id_persona = $persona['id_persona'];
        } else {
            // Insertar nueva persona
            $stmt = $pdo->prepare("INSERT INTO personas (id_tpdoc, num_doc, apellidos, nombres, fecha_nacimiento, id_nacionalidad, id_ubigeo, direccion) 
                                   VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$id_tpdoc, $num_doc, $apellidos, $nombres, $fecha_nacimiento, $id_nacionalidad, $id_ubigeo, $direccion]);
            $id_persona = $pdo->lastInsertId();
        }

        // Insertar en personas_autorizaciones
        $stmt = $pdo->prepare("INSERT INTO personas_autorizaciones (id_autorizacion, id_persona, id_tp_relacion, firma) 
                               VALUES (?, ?, ?, ?)");
        $stmt->execute([$id_autorizacion, $id_persona, $id_tp_relacion, $firma]);

        // Si está en representación, registrar la relación
        if ($en_representacion && $representante) {
            $stmt = $pdo->prepare("INSERT INTO representantes (id_representante, id_representado) VALUES (?, ?)");
            $stmt->execute([$id_persona, $representante]);
        }

        $pdo->commit();
        echo json_encode(["status" => "success", "message" => "Participante añadido correctamente"]);
    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(["status" => "error", "message" => "Error: " . $e->getMessage()]);
    }
}
?>