<?php
// Si ya está logueado, redirigir
if (estaLogueado()) {
    redirigir('index.php');
}

$titulo = 'Registro';
?>

<div class="spc" style="width: 500px; gap: 20px;">
    <div class="logo-tf">
        <h3>Tropical & Fresh</h3>
        <p>Sabores naturales</p>
    </div>
    <div class="profile-center">
        <h1 style="margin-top: 0;">REGISTRO</h1>
        <label for="profile-pic" style="cursor: pointer; width:140px; height:140px; display:flex; align-items:center; justify-content:center; margin-top: 0; margin-bottom: 10px;">
            <div id="circle-preview" class="cirp" aria-hidden="true">+</div>
        </label>
    </div>

    <form action="<?= dirname($_SERVER['SCRIPT_NAME']) ?>/includes/procesar_registro.php" method="post" enctype="multipart/form-data" novalidate>
        <input type="file" id="profile-pic" name="profile-pic" accept="image/*" style="display:none;">
        <input type="text" name="nombre" placeholder="Nombre" required class="input-pastel">
        <input type="email" name="correo" placeholder="Correo" required class="input-pastel">
        <input type="password" name="contrasena" placeholder="Contraseña (mínimo 6 caracteres)" required class="input-pastel">
        <label style="font-size:0.95rem; display:flex; gap:8px; align-items:center;">
            <input type="checkbox" name="terminos" required>
            <a href="https://youtu.be/GBcJyVTDYH4?t=8" target="_blank" style="margin-top: -5px;">Acepto los términos y condiciones</a>
        </label>
        <button type="submit" class="submit-btn" style="width:100%; margin-top: 20px;" name="regi">Registrar</button>
        <div class="back-home">
            <a href="index.php">← Volver al inicio</a>
        </div>
    </form>
</div>

<?php if (isset($_GET['error'])): ?>
    <script>
        let message = '';
        let type = 'error';
        switch ('<?= htmlspecialchars($_GET['error'], ENT_QUOTES, 'UTF-8') ?>') {
            case 'empty_fields':
                message = 'Todos los campos son obligatorios.';
                type = 'warning';
                break;
            case 'invalid_email':
                message = 'El correo electrónico no es válido.';
                break;
            case 'weak_password':
                message = 'La contraseña debe tener al menos 6 caracteres.';
                break;
            case 'email_exists':
                message = 'Este correo ya está registrado.';
                break;
            case 'invalid_image':
                message = 'El archivo cargado no es una imagen válida.';
                break;
            case 'upload_failed':
                message = 'Error al subir la imagen. Intenta de nuevo.';
                break;
            case 'register_failed':
                message = 'Error al registrar. Intenta de nuevo.';
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

<style>
    .profile-center {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 12px;
        margin-bottom: 10px;
    }
    #circle-preview {
        width: 140px;
        height: 140px;
        border-radius: 50%;
        border: 3px solid rgba(255, 255, 255, 0.85);
        background: linear-gradient(180deg, #fff 0%, #f5d4dd 100%);
        box-shadow: 0 18px 35px rgba(0, 0, 0, 0.12);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #a76d84;
        font-size: 2rem;
        font-weight: 700;
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    #circle-preview:hover {
        transform: translateY(-2px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.18);
    }
</style>

<script>
    document.getElementById('profile-pic')?.addEventListener('change', function(e) {
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
    document.getElementById('circle-preview')?.addEventListener('click', function() {
        document.getElementById('profile-pic').click();
    });
</script>
