<?php
require_once __DIR__ . '/../config/db.php';

header('Content-Type: application/json; charset=utf-8');

$con = conectarBD();
$result = mysqli_query($con, 'SELECT id_fru, nom_fru FROM frutas ORDER BY nom_fru');

$frutas = [];
while ($row = mysqli_fetch_assoc($result)) {
    $frutas[] = [
        'id_fru' => intval($row['id_fru']),
        'nom_fru' => $row['nom_fru']
    ];
}

mysqli_close($con);

echo json_encode($frutas, JSON_UNESCAPED_UNICODE);
