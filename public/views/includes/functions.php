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

?>