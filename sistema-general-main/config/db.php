<?php
// Definir la URL base del proyecto
if (!defined('BASE_URL')) {
    define('BASE_URL', '/sistema-general');
}

function conectarBD()
{
    $host = 'localhost';
    $dbname = 'taf2';
    $username = 'root';
    $password = '';
    $con = mysqli_connect($host, $username, $password, $dbname);
    if (!$con) {
        error_log('Error de conexión a la base de datos: ' . mysqli_connect_error());
        die('Error de conexión a la base de datos: ' . mysqli_connect_error());
    }
    mysqli_set_charset($con, 'utf8mb4');
    return $con;
}
