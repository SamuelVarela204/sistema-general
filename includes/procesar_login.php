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

    $stmt = mysqli_prepare($con, 'SELECT nom_com, usu_con, imagen FROM usuarios WHERE correo = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($result && mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['usu_con'])) {
            $_SESSION['usuario'] = $user['nom_com'];
            $_SESSION['correo'] = $email;
            $_SESSION['imagen'] = $user['imagen'];

            if (!empty($_POST['recordar'])) {
                setcookie('correo', $email, time() + 86400 * 30, '/');
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