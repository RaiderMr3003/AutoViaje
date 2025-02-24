<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit;
}

// Conexión a la base de datos
require '../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json');

    // Obtener los datos del formulario
    $id_persona = $_POST['id_persona'] ?? null;
    $id_autorizacion = $_POST['id_autorizacion'] ?? null;
    $documento_persona = !empty($_POST['documento_persona']) ? $_POST['documento_persona'] : null;
    $numdoc_persona = !empty($_POST['numdoc_persona']) ? $_POST['numdoc_persona'] : null;
    $edad = !empty($_POST['edad']) ? $_POST['edad'] : null;
    $tipo_edad = !empty($_POST['tipo_edad']) ? $_POST['tipo_edad'] : null;
    $nombre = !empty($_POST['nombre-persona']) ? $_POST['nombre-persona'] : null;
    $apellido = !empty($_POST['apellido-persona']) ? $_POST['apellido-persona'] : null;
    $direccion = !empty($_POST['direccion-persona']) ? $_POST['direccion-persona'] : null;
    $ubigeo = !empty($_POST['Ubigeo-persona']) ? trim($_POST['Ubigeo-persona']) : null;
    $nacionalidad = !empty($_POST['nacionalidad']) ? $_POST['nacionalidad'] : null;

    try {
        // Verificar si la persona existe antes de actualizar
        $sql_check_persona = "SELECT COUNT(*) FROM personas WHERE id_persona = :id_persona";
        $stmt_check_persona = $pdo->prepare($sql_check_persona);
        $stmt_check_persona->bindParam(':id_persona', $id_persona, PDO::PARAM_INT);
        $stmt_check_persona->execute();
        $persona_exists = $stmt_check_persona->fetchColumn();

        if (!$persona_exists) {
            echo json_encode(['success' => false, 'message' => 'Error: La persona no existe en la base de datos.']);
            exit;
        }

        // Verificar si el id_ubigeo existe en la tabla ubigeo
        if ($ubigeo !== null) {
            $sql_check_ubigeo = "SELECT COUNT(*) FROM ubigeo WHERE id_ubigeo = :ubigeo";
            $stmt_check = $pdo->prepare($sql_check_ubigeo);
            $stmt_check->bindParam(':ubigeo', $ubigeo, PDO::PARAM_STR);
            $stmt_check->execute();
            $ubigeo_exists = $stmt_check->fetchColumn();

            if (!$ubigeo_exists) {
                echo json_encode(['success' => false, 'message' => 'Error: El código de ubigeo no existe en la base de datos.']);
                exit;
            }
        }

        // Preparar la consulta SQL para actualizar los datos
        $sql = "UPDATE personas SET 
                    id_tpdoc = :documento_persona,
                    num_doc = :numdoc_persona,
                    edad = :edad,
                    tipo_edad = :tipo_edad,
                    nombres = :nombre,
                    apellidos = :apellido,
                    direccion = :direccion,
                    id_ubigeo = :ubigeo,
                    id_nacionalidad = :nacionalidad
                WHERE id_persona = :id_persona";

        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':documento_persona', $documento_persona, $documento_persona !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindParam(':numdoc_persona', $numdoc_persona, $numdoc_persona !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':edad', $edad, $edad !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindParam(':tipo_edad', $tipo_edad, $tipo_edad !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':nombre', $nombre, $nombre !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':apellido', $apellido, $apellido !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':direccion', $direccion, $direccion !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':ubigeo', $ubigeo, $ubigeo !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindParam(':nacionalidad', $nacionalidad, $nacionalidad !== null ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindParam(':id_persona', $id_persona, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Datos actualizados correctamente.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al actualizar los datos.']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()]);
    }
    exit;
} else {
    echo json_encode(['success' => false, 'message' => 'Método de acceso inválido.']);
    exit;
}
