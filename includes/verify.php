<?php
session_start();
include 'config/db.php';

$con = conectarBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inic'])) {
    $email = mysqli_real_escape_string($con, trim($_POST['correo'] ?? ''));
    $password = $_POST['contrasena'] ?? '';

    if (empty($email) || empty($password)) {
        header('Location: index.php?page=login&error=empty_fields');
        exit;
    } else {
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

                header('Location: index.php?page=perfil');
                exit;
            } else {
                header('Location: index.php?page=login&error=wrong_password');
                exit;
            }
        } else {
            header('Location: index.php?page=login&error=user_not_found');
            exit;
        }

        mysqli_stmt_close($stmt);
    }
}

mysqli_close($con);
?>