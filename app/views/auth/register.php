<?php
// app/views/auth/register.php
require_once VIEW_PATH . '/layouts/header.php';
$titulo = 'Registrarse';
?>

<div class="auth-container">
    <div class="auth-form">
        <h2>Crear cuenta</h2>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>/register">
            <input type="text" name="nombre" placeholder="Nombre completo" required class="input-pastel">
            <input type="email" name="correo" placeholder="Correo electrónico" required class="input-pastel" autocomplete="email">
            <input type="password" name="contrasena" placeholder="Contraseña" required class="input-pastel" autocomplete="new-password">
            
            <div class="secondary-row">
                <small>¿Ya tienes cuenta?</small>
                <a class="link-reg" href="<?= BASE_URL ?>/login">Iniciar sesión</a>
            </div>
            
            <button type="submit" class="submit-btn">Registrar</button>
            
            <div class="back-home">
                <a href="<?= BASE_URL ?>/">← Volver al inicio</a>
            </div>
        </form>
    </div>
</div>

<?php
require_once VIEW_PATH . '/layouts/footer.php';