<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../functions.php';

$con = conectarBD();

$pagina_actual = basename($_SERVER['PHP_SELF']);
$logueado = !empty($_SESSION['usuario']) || !empty($_SESSION['correo']) || !empty($_SESSION['usuario_id']);
if (!$logueado && $pagina_actual !== 'login.php' && $pagina_actual !== 'procesar.php') {
    header('Location: index.php?page=login');
    exit();
}

$rol_actual = $_SESSION['usuario_rol'] ?? 'cliente';

function obtenerDatosTaf2($con) {
    $productos = [];
    $usuarios = [];
    $pedidos = [];

    if (tablaExiste($con, 'producto')) {
        $productos = mysqli_query($con, 'SELECT * FROM producto ORDER BY id_pro DESC');
        $productos = $productos ? mysqli_fetch_all($productos, MYSQLI_ASSOC) : [];
    }

    if (tablaExiste($con, 'usuarios')) {
        $usuarios = mysqli_query(
            $con,
            'SELECT u.id_usu, u.nom_com, u.correo, u.estado, u.id_rol, IFNULL(r.nombre_rol, "cliente") AS nombre_rol
             FROM usuarios u
             LEFT JOIN roles r ON u.id_rol = r.id_rol
             ORDER BY u.id_usu DESC'
        );
        $usuarios = $usuarios ? mysqli_fetch_all($usuarios, MYSQLI_ASSOC) : [];
    }

    if (tablaExiste($con, 'pedido') && tablaExiste($con, 'usuarios')) {
        $pedidos = mysqli_query($con, 'SELECT p.id_ped, u.nom_com, p.fecha_pedido, p.estado, p.total FROM pedido p JOIN usuarios u ON p.id_usu = u.id_usu ORDER BY p.id_ped DESC');
        $pedidos = $pedidos ? mysqli_fetch_all($pedidos, MYSQLI_ASSOC) : [];
    }

    return compact('productos', 'usuarios', 'pedidos');
}
