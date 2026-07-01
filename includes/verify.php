<?php
session_start();
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/db.php';

$con = conectarBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inic'])) {
    $email = mysqli_real_escape_string($con, trim($_POST['correo'] ?? ''));
    $password = $_POST['contrasena'] ?? '';

    if (empty($email) || empty($password)) {
        redirigir('index.php?page=login&error=empty_fields');
    } else {
        if (!tablaExiste($con, 'usuarios')) {
            $sqlUsuarios = "
                CREATE TABLE IF NOT EXISTS usuarios (
                    id_usu INT NOT NULL AUTO_INCREMENT,
                    id_rol INT NOT NULL DEFAULT 2,
                    nom_com VARCHAR(225) NOT NULL,
                    usu_con VARCHAR(225) NOT NULL,
                    imagen MEDIUMBLOB DEFAULT NULL,
                    telefono VARCHAR(15) DEFAULT NULL,
                    correo VARCHAR(225) NOT NULL,
                    direccion VARCHAR(225) DEFAULT NULL,
                    descripcion VARCHAR(225) DEFAULT NULL,
                    estado VARCHAR(20) NOT NULL DEFAULT 'activo',
                    PRIMARY KEY (id_usu),
                    UNIQUE KEY correo (correo)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci";
            mysqli_query($con, $sqlUsuarios);
        }

        $stmt = mysqli_prepare($con, 'SELECT id_usu, id_rol, nom_com, usu_con, imagen FROM usuarios WHERE correo = ? LIMIT 1');
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
                $_SESSION['usuario_id'] = (int)($user['id_usu'] ?? 0);
                $_SESSION['rol_id'] = $rolUsuario;
                $_SESSION['usuario_rol'] = $rolUsuario === 1 ? 'admin' : 'cliente';

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
    }
}

mysqli_close($con);
