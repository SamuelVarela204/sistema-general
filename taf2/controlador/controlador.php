<?php
// controlador/controlador.php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Iniciar el motor de sesiones de PHP
}

require_once __DIR__ . '/../conexion.php';
require_once __DIR__ . '/../../includes/functions.php';

// Protección de páginas - redirigir usando TAF2_URL
$pagina_actual = basename($_SERVER['PHP_SELF']);
if (!isset($_SESSION['usuario_id']) && $pagina_actual !== 'login.php' && $pagina_actual !== 'procesar.php') {
    // Redirección manual para TAF2 ya que BASE_URL apunta a la raíz
    $basePath = TAF2_URL;
    $url = $basePath . '/login.php';
    header("Location: $url");
    exit;
}

// Rol sidebar
$rol_actual = isset($_SESSION['usuario_rol']) ? $_SESSION['usuario_rol'] : 'cliente';
?>