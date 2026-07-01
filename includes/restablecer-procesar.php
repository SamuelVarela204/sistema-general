<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/TokenRecuperacion.php';

$con = conectarBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['restablecer'])) {
    $token = trim($_POST['token'] ?? '');
    $nueva = $_POST['nueva_contrasena'] ?? '';
    $confirmar = $_POST['confirmar_contrasena'] ?? '';

    // Validaciones
    if (empty($token) || empty($nueva) || empty($confirmar)) {
        redirigir('index.php?page=restablecer&token=' . urlencode($token) . '&error=empty_fields');
    }

    if ($nueva !== $confirmar) {
        redirigir('index.php?page=restablecer&token=' . urlencode($token) . '&error=password_mismatch');
    }

    if (strlen($nueva) < 6) {
        redirigir('index.php?page=restablecer&token=' . urlencode($token) . '&error=weak_password');
    }

    // Verificar token
    $tokenManager = new TokenRecuperacion($con);
    $correo = $tokenManager->verificarToken($token);

    if ($correo === false) {
        redirigir('index.php?page=recuperacion&error=invalid_token');
    }

    // Actualizar la contraseña del usuario
    $hash = password_hash($nueva, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($con, 'UPDATE usuarios SET usu_con = ? WHERE correo = ?');
    mysqli_stmt_bind_param($stmt, 'ss', $hash, $correo);

    if (mysqli_stmt_execute($stmt)) {
        // Marcar token como usado
        $tokenManager->marcarUsado($token);
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        // Redirigir al login con mensaje de éxito
        header('Location: ' . dirname(dirname($_SERVER['SCRIPT_NAME'])) . '/index.php?page=login&status=password_reset');
        exit;
    } else {
        mysqli_stmt_close($stmt);
        redirigir('index.php?page=restablecer&token=' . urlencode($token) . '&error=update_failed');
    }
}

mysqli_close($con);