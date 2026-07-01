<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

$con = conectarBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inic'])) {
    $email = trim($_POST['correo'] ?? '');
    $password = $_POST['contrasena'] ?? '';

    if (empty($email) || empty($password)) {
        redirigir('index.php?page=login&error=empty_fields');
    }

    $stmt = mysqli_prepare($con, 'SELECT u.id_usu, u.id_rol, u.nom_com, u.usu_con, u.imagen, IFNULL(r.nombre_rol, "cliente") AS nombre_rol FROM usuarios u LEFT JOIN roles r ON u.id_rol = r.id_rol WHERE u.correo = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        $storedPassword = (string)($user['usu_con'] ?? '');
        $passwordValido = password_verify($password, $storedPassword) || $storedPassword === $password;

        if ($passwordValido) {
            $rolUsuario = (int)($user['id_rol'] ?? 0);
            $_SESSION['usuario'] = $user['nom_com'];
            $_SESSION['correo'] = $email;
            $_SESSION['imagen'] = $user['imagen'];
            $_SESSION['rol_id'] = $rolUsuario;
            $_SESSION['usuario_id'] = (int)$user['id_usu'];
            $_SESSION['usuario_rol'] = strtolower(trim((string)($user['nombre_rol'] ?? 'cliente')));

            if (!empty($_POST['recordar'])) {
                setcookie('correo', $email, time() + 86400 * 30, '/');
            }

            if ($rolUsuario === 1) {
                header('Location: ../taf2/index.php');
                exit;
            }

            redirigir('index.php');
        } else {
            redirigir('index.php?page=login&error=wrong_password');
        }
    } else {
        redirigir('index.php?page=login&error=user_not_found');
    }

    mysqli_stmt_close($stmt);
} else {
    redirigir('index.php?page=login');
}

mysqli_close($con);
