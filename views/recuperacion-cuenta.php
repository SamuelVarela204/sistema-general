<?php
$titulo = 'Recuperación de cuenta';
?>

<div class="spc">
    <div class="logo-tf">
        <div class="logo-circle">
            <span>T&F</span>
        </div>
        <h3>Tropical & Fresh</h3>
        <p>Sabores naturales</p>
    </div>

    <div class="welcome-message" style="margin-top: -40px">
        <h1>Recupera tu cuenta</h1>
    </div>

    <form action="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/includes/recuperacion-envio.php" method="post" novalidate style="margin-top: -30px;">
        <input type="email" name="correo" placeholder="Correo electrónico" required class="input-pastel" autocomplete="email">
        <button type="submit" class="submit-btn" name="recuperar">Enviar</button><br>
    </form>

    <div class="back-home">
        <a href="index.php">← Volver al inicio</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_GET['error']) || isset($_GET['status'])): ?>
    <script>
        let message = '';
        let type = 'error';
        const code = '<?= isset($_GET['status']) ? htmlspecialchars($_GET['status'], ENT_QUOTES, 'UTF-8') : htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?>';
        switch (code) {
            case 'user_not_found':
                message = 'El correo no está registrado.';
                break;
            case 'invalid_email':
                message = 'El correo no es válido.';
                type = 'warning';
                break;
            case 'empty_fields':
                message = 'El correo es obligatorio.';
                type = 'warning';
                break;
            case 'mail_error':
                message = 'No se pudo enviar el correo. Revisa la configuración SMTP.';
                break;
            case 'sent':
                message = 'Se ha enviado el correo de recuperación.';
                type = 'success';
                break;
            default:
                message = 'Error desconocido.';
        }
        Swal.fire({
            icon: type,
            title: message,
            confirmButtonText: 'Aceptar'
        });
    </script>
<?php endif; ?>