<?php
// app/config/db.php
require_once __DIR__ . '/database.php';

function conectarBD() {
    $host = getenv('DB_HOST') ?: DB_HOST;
    $user = getenv('DB_USER') ?: DB_USER;
    $pass = getenv('DB_PASS') ?: DB_PASS;
    $name = getenv('DB_NAME') ?: DB_NAME;
    $port = getenv('DB_PORT') ?: 3306;

    $con = new mysqli($host, $user, $pass, $name, $port);

    if ($con->connect_error) {
        http_response_code(500);
        exit('Error de conexión a la base de datos: ' . $con->connect_error);
    }

    $con->set_charset(DB_CHARSET);
    return $con;
}
