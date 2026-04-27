<?php
define('DB_HOST', 'localhost');
define('DB_NAME', 'taf');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8');
$con = mysqli_connect('localhost', 'root', DB_PASS, 'taf');
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}