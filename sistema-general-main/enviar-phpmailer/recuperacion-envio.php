<?php

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/TokenRecuperacion.php';

function redirigirAlInicio($query = '')
{
    $baseUrl = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
    $location = $baseUrl === '' || $baseUrl === '/' ? '/index.php' : $baseUrl . '/index.php';
    if ($query !== '') {
        $location .= '?' . ltrim($query, '?');
    }
    header('Location: ' . $location);
    exit;
}

$con = conectarBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['recuperar'])) {
    $correo = trim($_POST['correo'] ?? '');

    if ($correo === '') {
        redirigirAlInicio('page=recuperacion&error=empty_fields');
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        redirigirAlInicio('page=recuperacion&error=invalid_email');
    }

    // Verificar que el correo existe
    $stmt = mysqli_prepare($con, 'SELECT nom_com FROM usuarios WHERE correo = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $correo);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (!$result || mysqli_num_rows($result) !== 1) {
        mysqli_stmt_close($stmt);
        mysqli_close($con);
        redirigirAlInicio('page=recuperacion&error=user_not_found');
    }

    $usuario = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    $nombre = $usuario['nom_com'];

    // Generar token
    $tokenManager = new TokenRecuperacion($con);
    $token = $tokenManager->generarToken($correo);

    // Construir enlace de recuperación
    $baseUrl = rtrim(dirname(dirname($_SERVER['SCRIPT_NAME'])), '/\\');
    $enlace = $baseUrl . '/index.php?page=restablecer&token=' . $token;

    // Enviar correo
    $mail = new PHPMailer(true);
    try {
        $mailConfig = require __DIR__ . '/../config/mail.php';

        $mail->isSMTP();
        $mail->Host = $mailConfig['host'];
        $mail->SMTPAuth = true;
        $mail->Username = $mailConfig['username'];
        $mail->Password = $mailConfig['password'];
        $mail->SMTPSecure = ($mailConfig['encryption'] === 'ssl') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = $mailConfig['port'];
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = SMTP::DEBUG_OFF; // Cambiar a DEBUG_SERVER para depurar

        $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
        $mail->addAddress($correo, $nombre);
        $mail->isHTML(true);
        $mail->Subject = 'Recuperación de cuenta - Tropical & Fresh';
        $mail->Body = "
            <p>Hola <strong>" . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . "</strong>,</p>
            <p>Hemos recibido una solicitud para restablecer tu contraseña.</p>
            <p>Para crear una nueva contraseña, haz clic en el siguiente enlace (válido por 1 hora):</p>
            <p><a href='{$enlace}'>Restablecer contraseña</a></p>
            <p>Si no solicitaste este cambio, ignora este correo.</p>
        ";
        $mail->AltBody = "Hola $nombre,\n\nHemos recibido una solicitud para restablecer tu contraseña.\n\nPara crear una nueva contraseña, visita este enlace (válido por 1 hora):\n{$enlace}\n\nSi no solicitaste este cambio, ignora este correo.";

        $mail->send();
        mysqli_close($con);
        redirigirAlInicio('page=recuperacion&status=sent');
    } catch (Exception $e) {
        error_log('[Recuperación] Error al enviar correo: ' . $mail->ErrorInfo . ' | ' . $e->getMessage());
        mysqli_close($con);
        redirigirAlInicio('page=recuperacion&error=mail_error');
    }
}

mysqli_close($con);