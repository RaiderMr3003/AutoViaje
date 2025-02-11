<?php
session_start();
require '../../config/conexion.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Obtener el username del usuario logueado
        $user = $_SESSION['username'];
        $encargado = $user; // Nombre del usuario logueado

        // Recibir datos del formulario
        $id_autorizacion = isset($_POST['id']) ? $_POST['id'] : null;
        $nro_kardex = $_POST['id_control'];
        $id_tppermi = $_POST['tipo-permiso'];
        $fecha_ingreso = date("Y-m-d"); // Fecha actual (se usa solo para nuevos registros)
        $viaja_a = $_POST['viaja-a'];

        // Datos del acompañante
        $id_tpdoc_acomp = isset($_POST['documento_acompañante']) && $_POST['documento_acompañante'] !== "" ? $_POST['documento_acompañante'] : 10;
        $num_doc_acomp = $_POST['numdoc_acompañante'];
        $nombres_acomp = $_POST['nombre-acompañante'];
        $apellidos_acomp = $_POST['apellido-acompañante'];

        // Datos del responsable
        $id_tpdoc_resp = isset($_POST['documento_responsable']) && $_POST['documento_responsable'] !== "" ? $_POST['documento_responsable'] : 10;
        $num_doc_resp = $_POST['numdoc_responsable'];
        $nombres_resp = $_POST['nombre-responsable'];
        $apellidos_resp = $_POST['apellido-responsable'];

        // Datos de transporte
        $id_tptrans = $_POST['tipo_transporte'];
        $agencia_transporte = $_POST['agencia-de-transporte'];
        $fecha_inicio = $_POST['desde'];
        $fecha_fin = $_POST['hasta'];
        $observaciones = $_POST['observaciones'];

        if ($id_autorizacion) {
            // Si existe ID, actualizar la autorización
            $sql = "UPDATE autorizaciones SET 
                nro_kardex = :nro_kardex, 
                encargado = :encargado, 
                id_tppermi = :id_tppermi, 
                viaja_a = :viaja_a, 
                id_tpdoc_acomp = :id_tpdoc_acomp, 
                num_doc_acomp = :num_doc_acomp, 
                nombres_acomp = :nombres_acomp, 
                apellidos_acomp = :apellidos_acomp, 
                id_tpdoc_resp = :id_tpdoc_resp, 
                num_doc_resp = :num_doc_resp, 
                nombres_resp = :nombres_resp, 
                apellidos_resp = :apellidos_resp, 
                id_tptrans = :id_tptrans, 
                agencia_transporte = :agencia_transporte, 
                fecha_inicio = :fecha_inicio, 
                fecha_fin = :fecha_fin, 
                observaciones = :observaciones
                WHERE id_autorizacion = :id_autorizacion";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':id_autorizacion' => $id_autorizacion,
                ':nro_kardex' => $nro_kardex,
                ':encargado' => $encargado,
                ':id_tppermi' => $id_tppermi,
                ':viaja_a' => $viaja_a,
                ':id_tpdoc_acomp' => $id_tpdoc_acomp,
                ':num_doc_acomp' => $num_doc_acomp,
                ':nombres_acomp' => $nombres_acomp,
                ':apellidos_acomp' => $apellidos_acomp,
                ':id_tpdoc_resp' => $id_tpdoc_resp,
                ':num_doc_resp' => $num_doc_resp,
                ':nombres_resp' => $nombres_resp,
                ':apellidos_resp' => $apellidos_resp,
                ':id_tptrans' => $id_tptrans,
                ':agencia_transporte' => $agencia_transporte,
                ':fecha_inicio' => $fecha_inicio,
                ':fecha_fin' => $fecha_fin,
                ':observaciones' => $observaciones
            ]);

            $_SESSION['mensaje'] = "Autorización actualizada correctamente.";
        } else {
            // Si no existe ID, insertar nueva autorización
            $sql = "INSERT INTO autorizaciones 
                (nro_kardex, encargado, id_tppermi, fecha_ingreso, viaja_a, 
                id_tpdoc_acomp, num_doc_acomp, nombres_acomp, apellidos_acomp, 
                id_tpdoc_resp, num_doc_resp, nombres_resp, apellidos_resp, 
                id_tptrans, agencia_transporte, fecha_inicio, fecha_fin, observaciones)
                VALUES 
                (:nro_kardex, :encargado, :id_tppermi, :fecha_ingreso, :viaja_a, 
                :id_tpdoc_acomp, :num_doc_acomp, :nombres_acomp, :apellidos_acomp, 
                :id_tpdoc_resp, :num_doc_resp, :nombres_resp, :apellidos_resp, 
                :id_tptrans, :agencia_transporte, :fecha_inicio, :fecha_fin, :observaciones)";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nro_kardex' => $nro_kardex,
                ':encargado' => $encargado,
                ':id_tppermi' => $id_tppermi,
                ':fecha_ingreso' => $fecha_ingreso,
                ':viaja_a' => $viaja_a,
                ':id_tpdoc_acomp' => $id_tpdoc_acomp,
                ':num_doc_acomp' => $num_doc_acomp,
                ':nombres_acomp' => $nombres_acomp,
                ':apellidos_acomp' => $apellidos_acomp,
                ':id_tpdoc_resp' => $id_tpdoc_resp,
                ':num_doc_resp' => $num_doc_resp,
                ':nombres_resp' => $nombres_resp,
                ':apellidos_resp' => $apellidos_resp,
                ':id_tptrans' => $id_tptrans,
                ':agencia_transporte' => $agencia_transporte,
                ':fecha_inicio' => $fecha_inicio,
                ':fecha_fin' => $fecha_fin,
                ':observaciones' => $observaciones
            ]);

            $id_autorizacion = $pdo->lastInsertId();
            $_SESSION['mensaje'] = "Autorización guardada correctamente.";
        }

        // Redirigir a la página de edición con el ID correspondiente
        header("Location: edit_auto.php?id=" . $id_autorizacion);
        exit();
    } catch (PDOException $e) {
        die("Error al guardar: " . $e->getMessage());
    }
} else {
    header("Location: create_auto.php");
    exit();
}
?>
