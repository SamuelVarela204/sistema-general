<?php
$titulo = 'Restablecer contraseña';
$token = $_GET['token'] ?? '';

require_once __DIR__ . '/../includes/TokenRecuperacion.php';

if (empty($token)) {
    header('Location: index.php?page=recuperacion&error=invalid_token');
    exit;
}

$tokenManager = new TokenRecuperacion($con);
if ($tokenManager->verificarToken($token) === false) {
    header('Location: index.php?page=recuperacion&error=invalid_token');
    exit;
}
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
        <h1>Nueva contraseña</h1>
    </div>

    <form action="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/includes/restablecer-procesar.php" method="post" novalidate style="margin-top: -30px;">
        <input type="hidden" name="token" value="<?= htmlspecialchars($token, ENT_QUOTES, 'UTF-8') ?>">
        <input type="password" name="nueva_contrasena" placeholder="Nueva contraseña (mínimo 6 caracteres)" required class="input-pastel" autocomplete="new-password">
        <input type="password" name="confirmar_contrasena" placeholder="Confirmar contraseña" required class="input-pastel" autocomplete="new-password">
        <button type="submit" class="submit-btn" name="restablecer">Restablecer contraseña</button>
    </form>

    <div class="back-home">
        <a href="index.php">← Volver al inicio</a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<?php if (isset($_GET['error'])): ?>
    <script>
        let message = '';
        let type = 'error';
        switch ('<?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?>') {
            case 'empty_fields':
                message = 'Todos los campos son obligatorios.';
                type = 'warning';
                break;
            case 'password_mismatch':
                message = 'Las contraseñas no coinciden.';
                break;
            case 'weak_password':
                message = 'La contraseña debe tener al menos 6 caracteres.';
                break;
            case 'invalid_token':
                message = 'El enlace no es válido o ha expirado.';
                break;
            case 'update_failed':
                message = 'Error al actualizar la contraseña. Intenta de nuevo.';
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