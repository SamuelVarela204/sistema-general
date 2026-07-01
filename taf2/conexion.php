<?php
// conexion.php
$host = 'localhost';
$db   = 'taf2';
$user = 'root';
$pass = ''; // Cambia esto si tu MySQL tiene contraseña
$charset = 'utf8mb4';

// Usamos las variables definidas arriba
$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    // Aquí aplicamos $dsn, $user, $pass y $options correctamente
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>