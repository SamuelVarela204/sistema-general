<?php
// Si ya está logueado, redirigir
if (estaLogueado()) {
    redirigir('index.php');
}

$titulo = 'Registro';
ob_start();
?>

<div class="spc" style="width: 500px; gap: 20px;">
    <h1>REGISTRO</h1>

    <div class="profile-center">
        <label for="profile-pic" style="cursor: pointer; width:100%; height:100%; display:flex; align-items:center; justify-content:center;">
            <div id="circle-preview" class="cirp" aria-hidden="true"></div>
        </label>
    </div>

    <form action="register_procesar.php" method="post" enctype="multipart/form-data" novalidate>
        <input type="file" id="profile-pic" name="profile-pic" accept="image/*" style="display:none;">
        <input type="text" name="nombre" placeholder="Nombre" required class="input-pastel">
        <input type="email" name="correo" placeholder="Correo" required class="input-pastel">
        <input type="password" name="contrasena" placeholder="Contraseña" required class="input-pastel">
        <label style="font-size:0.95rem; display:flex; gap:8px; align-items:center;">
            <input type="checkbox" name="terminos" required>
            <a href="https://youtu.be/GBcJyVTDYH4?t=8" target="_blank">Acepto los términos y condiciones</a>
        </label>
        <button type="submit" class="submit-btn" style="width:100%;" name="regi">Registrar</button>
    </form>
</div>

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

<?php
$contenido = ob_get_clean();
require_once __DIR__ . '/../includes/layout.php';
?>