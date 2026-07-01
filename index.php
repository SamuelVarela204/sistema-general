<?php
session_start();
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$con = conectarBD();
$page = $_GET['page'] ?? 'home';
$view = $_GET['view'] ?? 'index';
$action = $_GET['action'] ?? '';

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

if (estaLogueado()) {
    $rolUsuario = $_SESSION['rol_id'] ?? null;
    if ($rolUsuario === null && !empty($_SESSION['correo'])) {
        $stmtRol = mysqli_prepare($con, 'SELECT id_rol FROM usuarios WHERE correo = ? LIMIT 1');
        mysqli_stmt_bind_param($stmtRol, 's', $_SESSION['correo']);
        mysqli_stmt_execute($stmtRol);
        $resultRol = mysqli_stmt_get_result($stmtRol);
        if ($resultRol && mysqli_num_rows($resultRol) === 1) {
            $rowRol = mysqli_fetch_assoc($resultRol);
            $rolUsuario = (int)($rowRol['id_rol'] ?? 0);
            $_SESSION['rol_id'] = $rolUsuario;
            $_SESSION['usuario_rol'] = $rolUsuario === 1 ? 'admin' : ($_SESSION['usuario_rol'] ?? 'cliente');
        }
        mysqli_stmt_close($stmtRol);
    }

    if ((int)$rolUsuario === 1 && $page !== 'taf2') {
        header('Location: index.php?page=taf2');
        exit;
    }
}

if ($page === 'taf2') {
    $usuarioRolActual = (int)($_SESSION['rol_id'] ?? 0);
    if (!estaLogueado() || $usuarioRolActual !== 1) {
        header('Location: index.php');
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($action === 'nuevo_usuario') {
            $nom_com = trim($_POST['nom_com'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $contrasena = $_POST['contrasena'] ?? '';
            $id_rol = (int)($_POST['id_rol'] ?? 2);
            $estado = trim($_POST['estado'] ?? 'activo');

            if (
                $nom_com !== '' &&
                filter_var($correo, FILTER_VALIDATE_EMAIL) &&
                strlen($contrasena) >= 6 &&
                $id_rol > 0 &&
                in_array($estado, ['activo', 'inactivo'], true)
            ) {
                $stmtCheckRol = mysqli_prepare($con, 'SELECT id_rol FROM roles WHERE id_rol = ? LIMIT 1');
                mysqli_stmt_bind_param($stmtCheckRol, 'i', $id_rol);
                mysqli_stmt_execute($stmtCheckRol);
                $resultRol = mysqli_stmt_get_result($stmtCheckRol);
                if ($resultRol && mysqli_num_rows($resultRol) === 1) {
                    $passwordHash = password_hash($contrasena, PASSWORD_DEFAULT);
                    $stmt = mysqli_prepare($con, 'INSERT INTO usuarios (id_rol, nom_com, correo, usu_con, estado) VALUES (?, ?, ?, ?, ?)');
                    mysqli_stmt_bind_param($stmt, 'issss', $id_rol, $nom_com, $correo, $passwordHash, $estado);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
                mysqli_stmt_close($stmtCheckRol);
            }

            header('Location: index.php?page=taf2&view=usuarios');
            exit;
        }

        if ($action === 'nuevo_producto') {
            $nom_pro = trim($_POST['nom_pro'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio = (float)($_POST['precio'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);
            $categoria = trim($_POST['categoria'] ?? 'General');

            if ($nom_pro !== '' && $precio >= 0 && $stock >= 0) {
                $stmt = mysqli_prepare($con, 'INSERT INTO producto (nom_pro, descripcion, precio, stock, categoria) VALUES (?, ?, ?, ?, ?)');
                mysqli_stmt_bind_param($stmt, 'ssdss', $nom_pro, $descripcion, $precio, $stock, $categoria);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            header('Location: index.php?page=taf2&view=productos');
            exit;
        }

        if ($action === 'editar_producto') {
            $id_pro = (int)($_POST['id_pro'] ?? 0);
            $nom_pro = trim($_POST['nom_pro'] ?? '');
            $descripcion = trim($_POST['descripcion'] ?? '');
            $precio = (float)($_POST['precio'] ?? 0);
            $stock = (int)($_POST['stock'] ?? 0);
            $categoria = trim($_POST['categoria'] ?? 'General');

            if ($id_pro > 0 && $nom_pro !== '' && $precio >= 0 && $stock >= 0) {
                $stmt = mysqli_prepare($con, 'UPDATE producto SET nom_pro = ?, descripcion = ?, precio = ?, stock = ?, categoria = ? WHERE id_pro = ?');
                mysqli_stmt_bind_param($stmt, 'ssdssi', $nom_pro, $descripcion, $precio, $stock, $categoria, $id_pro);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            header('Location: index.php?page=taf2&view=productos');
            exit;
        }

        if ($action === 'eliminar_producto') {
            $id_pro = (int)($_POST['id_pro'] ?? 0);
            if ($id_pro > 0) {
                $stmt = mysqli_prepare($con, 'DELETE FROM producto WHERE id_pro = ?');
                mysqli_stmt_bind_param($stmt, 'i', $id_pro);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            header('Location: index.php?page=taf2&view=productos');
            exit;
        }

        if ($action === 'actualizar_usuario') {
            $id_usu = (int)($_POST['id_usu'] ?? 0);
            $nom_com = trim($_POST['nom_com'] ?? '');
            $correo = trim($_POST['correo'] ?? '');
            $id_rol = (int)($_POST['id_rol'] ?? 2);
            $estado = trim($_POST['estado'] ?? 'activo');

            if (
                $id_usu > 0 &&
                $nom_com !== '' &&
                filter_var($correo, FILTER_VALIDATE_EMAIL) &&
                $id_rol > 0 &&
                in_array($estado, ['activo', 'inactivo'], true)
            ) {
                $stmtRole = mysqli_prepare($con, 'SELECT id_rol FROM roles WHERE id_rol = ? LIMIT 1');
                mysqli_stmt_bind_param($stmtRole, 'i', $id_rol);
                mysqli_stmt_execute($stmtRole);
                $resultRole = mysqli_stmt_get_result($stmtRole);
                if ($resultRole && mysqli_num_rows($resultRole) === 1) {
                    $stmt = mysqli_prepare($con, 'UPDATE usuarios SET nom_com = ?, correo = ?, id_rol = ?, estado = ? WHERE id_usu = ?');
                    mysqli_stmt_bind_param($stmt, 'ssisi', $nom_com, $correo, $id_rol, $estado, $id_usu);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
                mysqli_stmt_close($stmtRole);
            }

            header('Location: index.php?page=taf2&view=usuarios');
            exit;
        }

        if ($action === 'eliminar_usuario') {
            $id_usu = (int)($_POST['id_usu'] ?? 0);
            if ($id_usu > 0) {
                $stmt = mysqli_prepare($con, 'DELETE FROM usuarios WHERE id_usu = ?');
                mysqli_stmt_bind_param($stmt, 'i', $id_usu);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            header('Location: index.php?page=taf2&view=usuarios');
            exit;
        }

        if ($action === 'actualizar_pedido') {
            $id_ped = (int)($_POST['id_ped'] ?? 0);
            $estado = trim($_POST['estado'] ?? 'pendiente');
            $total = (float)($_POST['total'] ?? 0);

            if ($id_ped > 0 && $total >= 0) {
                $stmt = mysqli_prepare($con, 'UPDATE pedido SET estado = ?, total = ? WHERE id_ped = ?');
                mysqli_stmt_bind_param($stmt, 'sdi', $estado, $total, $id_ped);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }

            header('Location: index.php?page=taf2&view=pedidos');
            exit;
        }

        if ($action === 'eliminar_pedido') {
            $id_ped = (int)($_POST['id_ped'] ?? 0);
            if ($id_ped > 0) {
                $stmt = mysqli_prepare($con, 'DELETE FROM pedido WHERE id_ped = ?');
                mysqli_stmt_bind_param($stmt, 'i', $id_ped);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
            }
            header('Location: index.php?page=taf2&view=pedidos');
            exit;
        }
    }

    $vistaTaf2 = match ($view) {
        'productos' => 'taf2/productos.php',
        'usuarios' => 'taf2/usuarios.php',
        'pedidos' => 'taf2/pedidos.php',
        default => 'taf2/index.php'
    };

    ob_start();
    require_once __DIR__ . '/views/' . $vistaTaf2;
    $contenido = ob_get_clean();
    require_once __DIR__ . '/includes/layout.php';
    exit;
}

switch ($page) {
    case 'login':
        $vista = 'login.php';
        break;
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
    case 'recuperacion':
        $vista = 'recuperacion-cuenta.php';
        break;
    case 'restablecer':   // <--- NUEVO CASO
        $vista = 'restablecer.php';
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