<?php
// app/config/database.php
define('DB_HOST', 'localhost');
define('DB_NAME', 'taf2');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Ruta base de la aplicación
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = dirname($scriptName);
define('BASE_URL', rtrim($basePath, '/'));