<?php
// app/config/settings.php
session_start();

// Configuración de errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Codificación
mb_internal_encoding('UTF-8');
header('Content-Type: text/html; charset=utf-8');

// Incluir archivos de configuración
require_once __DIR__ . '/database.php';
require_once __DIR__ . '/paths.php';

// Verificar sesión activa para páginas protegidas
$protectedPages = ['perfil', 'ajustes'];
$currentScript = basename($_SERVER['SCRIPT_NAME'], '.php');

if (in_array($currentScript, $protectedPages) && !isset($_SESSION['usuario'])) {
    header('Location: ' . BASE_URL . '/login');
    exit;
}