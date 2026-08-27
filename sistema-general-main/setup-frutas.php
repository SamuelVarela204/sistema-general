<?php
require_once __DIR__ . '/config/db.php';

$con = conectarBD();

$frutas = [
    'Manzana',
    'Plátano',
    'Naranja',
    'Fresas',
    'Piña',
    'Sandía',
    'Mango',
    'Papaya',
    'Uva',
    'Durazno',
    'Cereza',
    'Limón'
];

foreach ($frutas as $fruta) {
    $stmt = mysqli_prepare($con, 'INSERT IGNORE INTO frutas (nom_fru) VALUES (?)');
    mysqli_stmt_bind_param($stmt, 's', $fruta);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}

echo "✅ Frutas insertadas correctamente.";

// Mostrar frutas
$result = mysqli_query($con, 'SELECT * FROM frutas');
echo "\n\nFrutas en la BD:\n";
while ($row = mysqli_fetch_assoc($result)) {
    echo $row['id_fru'] . " - " . $row['nom_fru'] . "\n";
}

mysqli_close($con);
