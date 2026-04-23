<?php
    include '../config/database.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/sty.css">
    <title>Inicio de sesión</title>
    <link rel="icon" href="../image/placeholder.png">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <!-- Volver-->
    <form action="../index.php" class="back-form"><button class="back-btn" type="submit">← Volver</button></form>

    <!-- Zona central (tarjeta) -->
    <div class="spc">
        <div class="profile-center">
        <img src="../image/placeholder.jfif" alt="Perfil" class="cirp" height="100" width="100">
    </div>

        <h1>Inicio de sesión</h1>
        <!-- Formulario: enviar al verify.php en la raíz del proyecto -->
        <form class="login" action="../verify.php" method="post" novalidate>
            
            <input type="email" name="correo" placeholder="Correo electrónico" required class="input-pastel" autocomplete="email">
            <input type="password" name="contrasena" placeholder="Contraseña" required class="input-pastel" autocomplete="current-password">
            <div class="secondary-row">
                <label class="remember-label">
                    <input type="checkbox" name="recordar" style="transform:scale(1.02); accent-color: #ff7a18;"> Recordarme
                </label>
                <a class="link-reg" href="registro.php">¿No estás registrado?</a>
            </div>

            <button type="submit" class="submit-btn" name="inic">Iniciar sesión</button>
        </form>
    </div>

    <script>
        <?php if (isset($_GET['error'])): ?>
            let message = '';
            let type = 'error';
            switch ('<?php echo htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8'); ?>') {
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
        <?php endif; ?>
    </script>
</body>
</html>