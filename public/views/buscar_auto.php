<?php
require '../../config/conexion.php';

// Obtener los parámetros de búsqueda
$tipoPermiso = $_POST['tipoPermiso'] ?? '';
$nroCrono = $_POST['nroCrono'] ?? '';
$encargado = $_POST['encargado'] ?? '';
$nombreParticipante = $_POST['nombreParticipante'] ?? '';
$fechaMin = $_POST['fechaMin'] ?? '';
$fechaMax = $_POST['fechaMax'] ?? '';


// Construir la consulta SQL dinámicamente
$query = "
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

// Aplicar filtros si los campos tienen valores
$params = [];
if ($tipoPermiso) {
    $query .= " AND tp.id_tppermi = ?";
    $params[] = $tipoPermiso;
}

if ($nroCrono) {
    $query .= " AND a.nro_kardex LIKE ?";
    $params[] = "%$nroCrono%";
}
if ($encargado) {
    $query .= " AND a.encargado LIKE ?";
    $params[] = "%$encargado%";
}
if ($nombreParticipante) {
    $query .= " AND (p.nombres LIKE ? OR p.apellidos LIKE ?)";
    $params[] = "%$nombreParticipante%";
    $params[] = "%$nombreParticipante%";
}
if ($fechaMin) {
    $query .= " AND a.fecha_ingreso >= ?";
    $params[] = $fechaMin;
}
if ($fechaMax) {
    $query .= " AND a.fecha_ingreso <= ?";
    $params[] = $fechaMax;
}

$query .= " GROUP BY a.id_autorizacion
    ORDER BY CAST(a.nro_kardex AS UNSIGNED) ASC, a.nro_kardex ASC ";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$autorizaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<?php if (count($autorizaciones) > 0): ?>
<?php foreach ($autorizaciones as $autorizacion): ?>
<tr>
    <td class="text-center"><?= htmlspecialchars($autorizacion['nro_kardex']) ?></td>
    <td class="text-center"><?= htmlspecialchars($autorizacion['encargado']) ?></td>
    <td><?= nl2br(htmlspecialchars($autorizacion['participantes'] ?? 'No existen participantes')) ?></td>
    <td class="text-center"><?= htmlspecialchars($autorizacion['tipo_permiso']) ?></td>
    <td><?= htmlspecialchars($autorizacion['fecha_ingreso']) ?></td>
    <td><?= htmlspecialchars(mb_strimwidth($autorizacion['observaciones'] ?? 'N/A', 0, 10, '...')) ?></td>
    <td>
        <a href="edit_auto.php?id=<?= $autorizacion['id_autorizacion'] ?>" class="btn btn-sm btn-warning"><svg
                xmlns="http://www.w3.org/2000/svg" width="15" height="20" fill="white" class="bi bi-pencil-fill"
                viewBox="0 0 16 16">
                <path
                    d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z" />
            </svg></a>
    </td>
</tr>
<?php endforeach; ?>
<?php else: ?>
<tr>
    <td colspan="7" class="text-center">No se encontraron resultados</td>
</tr>
<?php endif; ?>