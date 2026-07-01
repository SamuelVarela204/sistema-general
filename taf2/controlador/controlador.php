<?php
// controlador/controlador.php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Iniciar el motor de sesiones de PHP
}

// Conexión a la base de datos subiendo correctamente a la raíz
require_once __DIR__ . '/../conexion.php';

// Protección de páginas
$pagina_actual = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['usuario_id']) && $pagina_actual !== 'login.php' && $pagina_actual !== 'procesar.php') {
    header("Location: login.php");
    exit();
}

// Rol sidebar
$rol_actual = isset($_SESSION['usuario_rol']) ? $_SESSION['usuario_rol'] : 'cliente';
?>