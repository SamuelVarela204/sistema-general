<?php
include '../config/database.php';
mysqli_report(MYSQLI_REPORT_OFF); // Desactivar excepción automática y manejar errores manualmente

$alertMessage = '';
$alertType = 'info';
$redirectUrl = '';

/**
 * Función auxiliar para sanitizar texto de entrada.
 */
function sanitize($value) {
    return trim($value);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['regi'])) {
    $nombre = mysqli_real_escape_string($con, sanitize($_POST['nombre'] ?? ''));
    $email = mysqli_real_escape_string($con, sanitize($_POST['correo'] ?? ''));
    $passwordText = $_POST['contrasena'] ?? '';

    if (empty($nombre) || empty($email) || empty($passwordText)) {
        $alertMessage = 'Nombre, correo y contraseña son obligatorios.';
        $alertType = 'warning';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $alertMessage = 'Formato de correo inválido.';
        $alertType = 'error';
    } elseif (!isset($_POST['terminos'])) {
        $alertMessage = 'Debe aceptar los términos y condiciones.';
        $alertType = 'warning';
    } else {
        $password = password_hash($passwordText, PASSWORD_DEFAULT);

        $telefono = null;
        if (!empty($_POST['telefono'])) {
            $telefono = mysqli_real_escape_string($con, sanitize($_POST['telefono']));
        }

        $image_data = null;
        if (!empty($_FILES['profile-pic']['name']) && $_FILES['profile-pic']['error'] === UPLOAD_ERR_OK) {
            $check = getimagesize($_FILES['profile-pic']['tmp_name']);
            if ($check !== false) {
                $image_data = file_get_contents($_FILES['profile-pic']['tmp_name']);
            } else {
                $alertMessage = 'El archivo no es una imagen válida.';
                $alertType = 'error';
            }
        }

        if ($alertMessage === '') {
            $stmt = mysqli_prepare($con, 'INSERT INTO usuarios(nom_com, correo, usu_con, imagen, telefono) VALUES (?, ?, ?, ?, ?)');
            mysqli_stmt_bind_param($stmt, 'sssss', $nombre, $email, $password, $image_data, $telefono);

            if (mysqli_stmt_execute($stmt)) {
                $alertMessage = 'Registro exitoso.';
                $alertType = 'success';
                $redirectUrl = '../views/inicio.php';
            } else {
                $errno = mysqli_errno($con);
                if ($errno === 1062) {
                    // Duplicado unique: correo o teléfono
                    $errorText = mysqli_error($con);
                    if (stripos($errorText, 'correo') !== false) {
                        $alertMessage = 'Error: el correo ya está registrado.';
                    } elseif (stripos($errorText, 'telefono') !== false) {
                        $alertMessage = 'Error: el número de teléfono ya está registrado.';
                    } else {
                        $alertMessage = 'Error: valor duplicado ya existe.';
                    }
                    $alertType = 'error';
                } else {
                    $alertMessage = 'Error al registrar: ' . mysqli_error($con);
                    $alertType = 'error';
                }
            }
            mysqli_stmt_close($stmt);
        }
    }
}

mysqli_close($con);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/sty.css">
    <link rel="icon" href="../image/placeholder.png">
    <title>Registro</title>
</head>
<body>
    <!-- Volver -->
    <form action="../index.php" class="back-form">
        <button class="back-btn" type="submit">← Volver</button>
    </form>

    <!-- Zona central (tarjeta) -->
    <div class="spc" style="width: 500px; height: 500px; gap: 20px;">
        <h1>REGISTRO</h1>

        <div class="profile-center">
            <label for="profile-pic" style="cursor: pointer; width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
                <div id="circle-preview" class="cirp" aria-hidden="true"></div>
            </label>
        </div>

        <h1>Seleccione su imagen de perfil</h1>

        <!-- Formulario -->
        <form action="" method="post" class="login" style="width:100%; gap:12px;" enctype="multipart/form-data" novalidate>
            <input type="file" id="profile-pic" name="profile-pic" accept="image/*" style="display:none;">

            <input type="text" name="nombre" placeholder="Nombre" required class="input-pastel" autocomplete="name">

            <input type="email" name="correo" placeholder="Correo" required class="input-pastel" autocomplete="email">

            <input type="password" name="contrasena" placeholder="Contraseña" required class="input-pastel" autocomplete="new-password">

            <label style="font-size:0.95rem; display:flex; gap:8px; align-items:center;">
                <input type="checkbox" name="terminos" required style="transform:scale(1.02);"> 
                <a href="https://youtu.be/GBcJyVTDYH4?si=n4nbXmE4rR3g0FKH&t=8" target="_blank">Acepto los términos y condiciones</a>
            </label>

            <button type="submit" class="submit-btn" style="width:100%;"name="regi">Registrar</button>
        </form>
    </div>

<script>
    // Previsualizar la imagen en el círculo
    document.getElementById('profile-pic').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(evt) {
            const circle = document.getElementById('circle-preview');
            circle.style.backgroundImage = `url('${evt.target.result}')`;
            circle.textContent = '';
        };
        reader.readAsDataURL(file);
    });

    // Abrir selector al hacer clic en el círculo
    document.getElementById('circle-preview').addEventListener('click', function() {
        document.getElementById('profile-pic').click();
    });
</script>

<?php if (!empty($alertMessage)): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: '<?= htmlspecialchars($alertType, ENT_QUOTES, 'UTF-8') ?>',
            title: '<?= htmlspecialchars($alertMessage, ENT_QUOTES, 'UTF-8') ?>',
            confirmButtonText: 'Aceptar',
            timer: <?= $alertType === 'success' ? 1800 : 3000 ?>,
            timerProgressBar: true,
            didClose: () => {
                <?php if (!empty($redirectUrl)) : ?>
                window.location.href = '<?= $redirectUrl ?>';
                <?php endif; ?>
            }
        });
    </script>
<?php endif; ?>

</body>
</html>

