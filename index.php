<?php
// index.php
session_start();

// DEFINICIÓN DE RUTAS ABSOLUTAS (NO TOCAR) 
define('ROOT_PATH', __DIR__);
define('VIEWS_PATH', ROOT_PATH . '/app/views');
define('LAYOUTS_PATH', VIEWS_PATH . '/layouts');
define('CONTROLLERS_PATH', ROOT_PATH . '/app/controllers');
define('MODELS_PATH', ROOT_PATH . '/app/models');
$scriptName = $_SERVER['SCRIPT_NAME'];
$basePath = dirname($scriptName);
define('BASE_URL', rtrim($basePath, '/'));

// Determinar la página solicitada
$page = $_GET['page'] ?? 'home';

// Definir el controlador y método basado en la página
switch ($page) {
    case 'login':
        $controller = new AuthController();
        $controller->login();
        break;
    case 'register':
        $controller = new AuthController();
        $controller->register();
        break;
    case 'logout':
        $controller = new AuthController();
        $controller->logout();
        break;
    case 'perfil':
        $controller = new UserController();
        $controller->profile();
        break;
    case 'ajustes':
        $controller = new UserController();
        $controller->settings();
        break;
    case 'buscar':
        $controller = new ProductController();
        $controller->search();
        break;
    default:
        // Para la página home, simplemente cargar la vista
        $titulo = 'Inicio';
        require_once 'app/views/home.php';
        break;
}