<?php
require_once 'controlador/controlador.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - TAF2</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="taf2-login-body">
    <div class="login-shell">
        <div class="custom-card login-card">
            <div class="card-header-blue">Acceso TAF2</div>
            <div class="card-body">
                <div class="login-brand">
                    <div class="logo-circle"><span>TF</span></div>
                    <h2>Tropical & Fresh</h2>
                    <p>Panel de control</p>
                </div>

                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($_GET['error']) ?></div>
                <?php endif; ?>

                <form action="procesar.php" method="POST">
                    <input type="hidden" name="action" value="iniciar_sesion">

                    <div class="form-group">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="correo" class="form-input" placeholder="Ingrese su correo" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Contraseña</label>
                        <input type="password" name="password" class="form-input" placeholder="Contraseña" required>
                    </div>

                    <button type="submit" class="btn-submit" style="background: linear-gradient(135deg, #ff7a18 0%, #af002d 100%);">Ingresar al Panel</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>