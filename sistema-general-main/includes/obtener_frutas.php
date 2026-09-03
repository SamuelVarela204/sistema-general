<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

header('Content-Type: application/json; charset=utf-8');

$con = conectarBD();
$result = mysqli_query($con, 'SELECT id_fru, nom_fru FROM frutas ORDER BY nom_fru');

if (!$result) {
    error_log('Error al obtener frutas: ' . mysqli_error($con));
    mysqli_close($con);
    respuestaJSON(false, 'No se pudieron obtener las frutas', [], 500);
}

$frutas = [];
while ($row = mysqli_fetch_assoc($result)) {
    $frutas[] = [
        'id_fru' => intval($row['id_fru']),
        'nom_fru' => $row['nom_fru']
    ];
}

mysqli_close($con);

respuestaJSON(true, 'Frutas obtenidas correctamente', $frutas, 200);
