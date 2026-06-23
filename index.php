<?php
// index.php
require_once 'app/config/settings.php';

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