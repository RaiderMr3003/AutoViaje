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

function getUbigeo() {
    global $pdo;

    try {
        $query = "SELECT id_ubigeo, nom_dis FROM ubigeo ORDER BY nom_dis";
        $stmt = $pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        die("Error en getUbigeo: " . $e->getMessage());
    }
}

function getNacionalidad() {
    global $pdo;

    try {
        $query = "SELECT id_nacionalidad, desc_nacionalidad FROM nacionalidades";
        $stmt = $pdo->query($query);
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    } catch (PDOException $e) {
        die("Error en getNacionalidad: " . $e->getMessage());
    }
}

function getParticipantes($id_autorizacion) {
    global $pdo;

    try {
        $query = "SELECT p.id_persona, CONCAT(p.apellidos, ', ', p.nombres) AS nombre_completo
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

function obtenerAutorizaciones($filtros = [], $limit = 10, $offset = 0)
{
    global $pdo;

    $query = "
        SELECT 
            SQL_CALC_FOUND_ROWS
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

    if (!empty($filtros['tipoPermiso'])) {
        $query .= " AND tp.id_tppermi = ?";
        $params[] = $filtros['tipoPermiso'];
    }
    if (!empty($filtros['nroCrono'])) {
        $query .= " AND a.nro_kardex LIKE ?";
        $params[] = "%{$filtros['nroCrono']}%";
    }
    if (!empty($filtros['encargado'])) {
        $query .= " AND a.encargado LIKE ?";
        $params[] = "%{$filtros['encargado']}%";
    }
    if (!empty($filtros['nombreParticipante'])) {
        $query .= " AND (p.nombres LIKE ? OR p.apellidos LIKE ?)";
        $params[] = "%{$filtros['nombreParticipante']}%";
        $params[] = "%{$filtros['nombreParticipante']}%";
    }
    if (!empty($filtros['fechaMin'])) {
        $query .= " AND a.fecha_ingreso >= ?";
        $params[] = $filtros['fechaMin'];
    }
    if (!empty($filtros['fechaMax'])) {
        $query .= " AND a.fecha_ingreso <= ?";
        $params[] = $filtros['fechaMax'];
    }

    $query .= " GROUP BY a.id_autorizacion
        ORDER BY 
            CAST(a.nro_kardex AS UNSIGNED) ASC,
            a.nro_kardex REGEXP '[A-Za-z]' DESC,
            a.nro_kardex ASC
        LIMIT $limit OFFSET $offset
    ";

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $autorizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $stmtTotal = $pdo->query("SELECT FOUND_ROWS()");
        $totalRows = $stmtTotal->fetchColumn();

        return ['data' => $autorizaciones, 'total' => $totalRows];
    } catch (PDOException $e) {
        die("Error al obtener autorizaciones: " . $e->getMessage());
    }
}
?>