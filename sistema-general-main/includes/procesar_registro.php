<?php
session_start();
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

$con = conectarBD();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['regi'])) {
    $nombre = trim($_POST['nombre'] ?? '');
    $email = trim($_POST['correo'] ?? '');
    $password = $_POST['contrasena'] ?? '';
    $descripcion = trim($_POST['descripcion'] ?? '');
    $imagen = null;

    // Validaciones básicas
    if (empty($nombre) || empty($email) || empty($password)) {
        redirigir('index.php?page=register&error=empty_fields');
    }

    if (!esEmailValido($email)) {
        redirigir('index.php?page=register&error=invalid_email');
    }

    if (strlen($password) < 6) {
        redirigir('index.php?page=register&error=weak_password');
    }

    // Verificar si el email ya existe
    $stmt = mysqli_prepare($con, 'SELECT id_usu FROM usuarios WHERE correo = ? LIMIT 1');
    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) > 0) {
        redirigir('index.php?page=register&error=email_exists');
    }
    mysqli_stmt_close($stmt);

    // Procesar imagen si existe
    if (isset($_FILES['profile-pic']) && $_FILES['profile-pic']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ($_FILES['profile-pic']['error'] !== UPLOAD_ERR_OK) {
            redirigir('index.php?page=register&error=upload_failed');
        }

        $check = getimagesize($_FILES['profile-pic']['tmp_name']);
        if ($check === false) {
            redirigir('index.php?page=register&error=invalid_image');
        }

        $imagen = file_get_contents($_FILES['profile-pic']['tmp_name']);
    }

    // Establecer descripción por defecto si está vacía
    if (empty($descripcion)) {
        $descripcion = 'perfil sin descripcion';
    }

    // Hashear contraseña
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);
    $rol = 2;

    if ($imagen !== null) {
        $stmt = mysqli_prepare($con, 'INSERT INTO usuarios (id_rol, nom_com, correo, usu_con, descripcion, imagen) VALUES (?, ?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'isssss', $rol, $nombre, $email, $passwordHash, $descripcion, $imagen);
    } else {
        $stmt = mysqli_prepare($con, 'INSERT INTO usuarios (id_rol, nom_com, correo, usu_con, descripcion) VALUES (?, ?, ?, ?, ?)');
        mysqli_stmt_bind_param($stmt, 'issss', $rol, $nombre, $email, $passwordHash, $descripcion);
    }

    if ($stmt && mysqli_stmt_execute($stmt)) {
        // Obtener el ID del usuario recién creado
        $id_usu = mysqli_insert_id($con);

        // La funcionalidad de frutas favoritas está deshabilitada temporalmente.

        // Enviar correo de bienvenida/confirmación
        $mailConfig = require __DIR__ . '/../config/mail.php';
        $debugOutput = '';

        try {
            $mail = new PHPMailer(true);
            $mail->isSMTP();
            $mail->Host = $mailConfig['host'];
            $mail->SMTPAuth = true;
            $mail->Username = $mailConfig['username'];
            $mail->Password = $mailConfig['password'];
            $mail->SMTPSecure = ($mailConfig['encryption'] === 'ssl') ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = $mailConfig['port'];
            $mail->CharSet = 'UTF-8';
            $mail->SMTPAutoTLS = true;
            $mail->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ],
            ];
            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
            $mail->Debugoutput = function ($str, $level) use (&$debugOutput) {
                $debugOutput .= "[$level] $str\n";
            };

            $mail->setFrom($mailConfig['from_email'], $mailConfig['from_name']);
            $mail->addAddress($email, $nombre);
            $mail->isHTML(true);
            $mail->Subject = '¡Bienvenido a Tropical & Fresh! 🍎';
            
            // Crear HTML bonito para el correo
            $htmlBody = '
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: "Arial", sans-serif; background-color: #f5f5f5; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; background-color: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%); color: white; padding: 40px 20px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; }
        .header p { margin: 10px 0 0 0; font-size: 14px; opacity: 0.9; }
        .content { padding: 40px 30px; }
        .greeting { font-size: 18px; color: #333; margin-bottom: 20px; }
        .greeting strong { color: #27ae60; }
        .message { color: #666; font-size: 16px; line-height: 1.6; margin-bottom: 30px; }
        .benefits { background-color: #f0f9f0; border-left: 4px solid #27ae60; padding: 20px; margin: 30px 0; border-radius: 5px; }
        .benefits h3 { color: #27ae60; margin-top: 0; }
        .benefits ul { margin: 10px 0; padding-left: 20px; }
        .benefits li { color: #555; margin: 8px 0; }
        .cta-button { display: inline-block; background-color: #2ecc71; color: white; padding: 12px 30px; border-radius: 5px; text-decoration: none; font-weight: bold; margin: 20px 0; transition: background-color 0.3s; }
        .cta-button:hover { background-color: #27ae60; }
        .footer { background-color: #f9f9f9; padding: 20px 30px; border-top: 1px solid #eee; text-align: center; color: #999; font-size: 12px; }
        .footer a { color: #27ae60; text-decoration: none; }
        .logo { font-size: 32px; margin: 0 0 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🍎 🍌 🍊</div>
            <h1>¡Bienvenido a Tropical & Fresh!</h1>
            <p>Tu tienda de productos frescos favorita</p>
        </div>
        
        <div class="content">
            <div class="greeting">
                ¡Hola <strong>' . htmlspecialchars($nombre, ENT_QUOTES, 'UTF-8') . '</strong>! 👋
            </div>
            
            <div class="message">
                Nos complace anunciarte que tu cuenta ha sido creada exitosamente. 
                Ya formas parte de nuestra comunidad de amantes de los productos frescos.
            </div>
            
            <div class="benefits">
                <h3>🎉 ¿Qué puedes hacer ahora?</h3>
                <ul>
                    <li><strong>Acceder a ofertas exclusivas</strong> - Descuentos especiales para miembros</li>
                    <li><strong>Comprar productos frescos</strong> - Frutas y verduras de calidad premium</li>
                    <li><strong>Personalizar tu perfil</strong> - Añade tu foto y descripción</li>
                    <li><strong>Recibir notificaciones</strong> - Mantente informado de nuevas promociones</li>
                </ul>
            </div>
            
            <a href="http://localhost/sistema-general/index.php" class="cta-button">
                ➜ Ir a mi cuenta
            </a>
            
            <div class="message">
                <strong>Tus credenciales de acceso:</strong><br>
                📧 <strong>Correo:</strong> ' . htmlspecialchars($email, ENT_QUOTES, 'UTF-8') . '<br>
                (La contraseña que registraste en el formulario)
            </div>
            
            <div class="message">
                Si tienes alguna pregunta, no dudes en contactarnos. 
                <strong>¡Que disfrutes tu experiencia con nosotros!</strong> 🎊
            </div>
        </div>
        
        <div class="footer">
            <p>© 2026 Tropical & Fresh. Todos los derechos reservados.</p>
            <p>
                <a href="#">Contacto</a> | 
                <a href="#">Política de Privacidad</a> | 
                <a href="#">Términos de Servicio</a>
            </p>
        </div>
    </div>
</body>
</html>
            ';
            
            $mail->Body = $htmlBody;
            $mail->AltBody = "Hola $nombre\n\nGracias por registrarte en Tropical & Fresh. ¡Bienvenido!\n\nYa puedes iniciar sesión en tu cuenta con el email: $email\n\nSi tienes preguntas, contáctanos.";

            $mail->send();
        } catch (Exception $e) {
            error_log('[Registro] Error al enviar correo de bienvenida: ' . ($mail->ErrorInfo ?? 'n/a') . ' | ' . $e->getMessage());
            if (!empty($debugOutput)) {
                error_log('[Registro] SMTP debug: ' . $debugOutput);
            }
        }

        $_SESSION['usuario'] = $nombre;
        $_SESSION['correo'] = $email;
        $_SESSION['descripcion'] = $descripcion;
        if ($imagen !== null) {
            $_SESSION['imagen'] = $imagen;
        }
        mysqli_stmt_close($stmt);
        redirigir('index.php');
    } else {
        redirigir('index.php?page=register&error=register_failed');
    }

    mysqli_stmt_close($stmt);
} else {
    redirigir('index.php?page=register');
}

mysqli_close($con);
