<?php
require '../../config/conexion.php';
require 'includes/functions.php';

// Obtener los parámetros de búsqueda
$filtros = [
    'tipoPermiso' => $_POST['tipoPermiso'] ?? '',
    'nroCrono' => $_POST['nroCrono'] ?? '',
    'encargado' => $_POST['encargado'] ?? '',
    'nombreParticipante' => $_POST['nombreParticipante'] ?? '',
    'fechaMin' => $_POST['fechaMin'] ?? '',
    'fechaMax' => $_POST['fechaMax'] ?? ''
];

// Usar la función centralizada
// Nota: Se establece un límite alto (100) temporalmente para la búsqueda AJAX
// Idealmente, esto debería implementarse con paginación completa.
$resultado = obtenerAutorizaciones($filtros, 100, 0);
$autorizaciones = $resultado['data'];
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