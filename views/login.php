<?php
$titulo = 'Iniciar sesión';
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
        <h1>Bienvenido de vuelta</h1>
    </div>

    <form action="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/includes/verify.php" method="post" novalidate style="margin-top: -30px;">
        <input type="email" name="correo" placeholder="Correo electrónico" required class="input-pastel" autocomplete="email">
        <input type="password" name="contrasena" placeholder="Contraseña" required class="input-pastel" autocomplete="current-password">

        <div class="secondary-row">
            <label class="remember-label">
                <input type="checkbox" name="recordar" class="checkbox-recordar" style="margin-left: 10px;margin-top: -10px;"> Recordarme
            </label>
            <a class="link-reg" href="index.php?page=register" style="margin-left: 110px;">¿No estás registrado?</a><br>
        </div>

        <button type="submit" class="submit-btn" name="inic">Iniciar sesión</button><br>
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