<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$con = conectarBD();
$page = $_GET['page'] ?? 'home';

$descripcionPerfil = '';
if (estaLogueado() && !empty($_SESSION['correo'])) {
    $descripcionPerfil = obtenerDescripcionUsuario($con, $_SESSION['correo']);
    if (empty($descripcionPerfil) && !empty($_SESSION['descripcion'])) {
        $descripcionPerfil = $_SESSION['descripcion'];
    }
}

if ($page === 'logout') {
    session_destroy();
    header('Location: index.php');
    exit;
}

switch ($page) {
    case 'login':
        $vista = 'login.php';
        break;  // ← Agregar este break
        case 'ajustes':
            $vista = 'ajustes.php';
            break;
    case 'register':
        $vista = 'register.php';
        break;
    case 'perfil':
        $vista = 'perfil.php';
        break;
    case 'buscar':
        $vista = 'buscar.php';
        break;
    default:
        $vista = 'home.php';
        $productos = obtenerProductos($con);
        break;
}

ob_start();
require_once __DIR__ . '/views/' . $vista;
$contenido = ob_get_clean();

require_once __DIR__ . '/includes/layout.php';