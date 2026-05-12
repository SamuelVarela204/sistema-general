<?php
// No iniciar sesión aquí, ya está activa
// Solo verificamos si ya está logueado
if (estaLogueado()) {
    redirigir('index.php');
}

$titulo = 'Iniciar sesión';
ob_start();
?>

<div class="spc">
    <div class="profile-center">
        <img src="public/images/placeholder.jfif" class="cirp" height="100" width="100">
    </div>
    <h1>Inicio de sesión</h1>
    
    <form action="includes/procesar_login.php" method="post" novalidate>
        <input type="email" name="correo" placeholder="Correo electrónico" required class="input-pastel" autocomplete="email">
        <input type="password" name="contrasena" placeholder="Contraseña" required class="input-pastel" autocomplete="current-password">
        <div class="secondary-row">
            <label class="remember-label">
                <input type="checkbox" name="recordar" class="checkbox-recordar"> Recordarme
            </label>
            <a class="link-reg" href="index.php?page=register">¿No estás registrado?</a>
        </div>
        <button type="submit" class="submit-btn" name="inic">Iniciar sesión</button>
    </form>
</div>

<?php if (isset($_GET['error'])): ?>
<script>
    let message = '';
    let type = 'error';
    switch ('<?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?>') {
        case 'user_not_found':
            message = 'El usuario no existe.';
            break;
        case 'wrong_password':
            message = 'Contraseña incorrecta.';
            break;
        case 'empty_fields':
            message = 'Correo y contraseña son obligatorios.';
            type = 'warning';
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

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../includes/layout.php';
?>