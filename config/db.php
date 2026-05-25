<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'taf2');
define('DB_USER', 'root');
define('DB_PASS', '');

function conectarBD() {
    $con = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if (!$con) {
        die("Error de conexión: " . mysqli_connect_error());
    }
    mysqli_set_charset($con, 'utf8');
    return $con;
}
?>