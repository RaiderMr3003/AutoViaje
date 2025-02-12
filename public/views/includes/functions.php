<?php

require '../../config/conexion.php';

function getTpPermisos() {
    global $pdo; 

    try {
        $query = "SELECT id_tppermi, des_tppermi FROM tp_permiso";
        $stmt = $pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        die("Error en getTpPermisos: " . $e->getMessage());
    }
}

function getTpDoc() {
    global $pdo;
    
    try {
        $query = "SELECT id_tpdoc, des_tpdoc FROM tp_documento";
        $stmt = $pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        die("Error en getTpDoc: " . $e->getMessage());
    }
}

function getTpTransportes() {
    global $pdo;

    try {
        $query = "SELECT id_tptrans, des_tptrans FROM tp_transporte";
        $stmt = $pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        die("Error en getTpTransportes: " . $e->getMessage());
    }
}

function getCondicion() {
    global $pdo;

    try {
        $query = "SELECT id_tp_relacion, descripcion FROM tp_relacion";
        $stmt = $pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        die("Error en getCondicion: " . $e->getMessage());
    }
}

function getFirma() {
    return ['SI', 'NO', 'HUELLA'];
}

function getParticipantes($id_autorizacion) {
    global $pdo;

    try {
        $query = "SELECT CONCAT(p.apellidos, ', ', p.nombres) AS nombre_completo
                  FROM personas p
                  INNER JOIN personas_autorizaciones pa ON p.id_persona = pa.id_persona
                  INNER JOIN tp_relacion tr ON pa.id_tp_relacion = tr.id_tp_relacion
                  WHERE pa.id_autorizacion = :id_autorizacion
                  AND tr.id_tp_relacion IN (2, 3)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':id_autorizacion', $id_autorizacion, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        die("Error en getParticipantes: " . $e->getMessage());
    }
}
?>