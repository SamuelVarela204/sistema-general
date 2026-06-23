<?php
// app/views/auth/login.php
require_once VIEW_PATH . '/layouts/header.php';
$titulo = 'Iniciar sesión';
?>

<div class="auth-container">
    <div class="auth-form">
        <h2>Iniciar sesión</h2>
        
        <?php if (isset($_GET['error'])): ?>
            <div class="error-message"><?= htmlspecialchars($_GET['error']) ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
            <div class="success-message">Registro exitoso. Ahora puedes iniciar sesión.</div>
        <?php endif; ?>
        
        <form method="POST" action="<?= BASE_URL ?>/login">
            <input type="email" name="correo" placeholder="Correo electrónico" required class="input-pastel" autocomplete="email">
            <input type="password" name="contrasena" placeholder="Contraseña" required class="input-pastel" autocomplete="current-password">
            
            <div class="secondary-row">
                <label class="remember-label">
                    <input type="checkbox" name="recordar" class="checkbox-recordar"> Recordarme
                </label>
                <a class="link-reg" href="<?= BASE_URL ?>/forgot-password">¿Olvidaste tu contraseña?</a>
            </div>
            
            <button type="submit" class="submit-btn">Iniciar sesión</button>
            
            <div class="back-home">
                <a href="<?= BASE_URL ?>/">← Volver al inicio</a>
            </div>
        </form>
    </div>
</div>

<?php
require_once VIEW_PATH . '/layouts/footer.php';