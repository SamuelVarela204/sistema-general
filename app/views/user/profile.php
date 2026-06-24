<?php
// app/views/user/profile.php
require_once VIEW_PATH . '../layouts/header.php';
$titulo = 'Mi Perfil';
?>

<main class="spc" style="max-width: 1100px; width: 100%; margin: 0 auto; padding: 20px 16px; position: relative; margin-top: 30px;">
    <div style="text-align: center;">
        <h1><strong>MI PERFIL</strong></h1>
    </div>
    
    <?php if (isset($_GET['success']) && $_GET['success'] == '1'): ?>
        <div class="success-message">Perfil actualizado correctamente</div>
    <?php endif; ?>
    
    <?php if (isset($_GET['error']) && $_GET['error'] == '1'): ?>
        <div class="error-message">Error al actualizar el perfil</div>
    <?php endif; ?>
    
    <section class="perfil-header">
        <div class="cirp perfil-foto" style="background-image: url('data:image/jpeg;base64, <?= $_SESSION['imagen'] ?? '' ?>')"></div>
        <div class="datos-perfil">
            <h2><?= htmlspecialchars($_SESSION['usuario'] ?? '') ?></h2>
            <p>Rol: <?= htmlspecialchars($user['nombre_rol'] ?? '') ?></p>
        </div>
    </section>
    
    <form method="POST" action="<?= BASE_URL ?>/update-profile" enctype="multipart/form-data">
        <!-- Formulario de actualización -->
        <div class="formulario-perfil">
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" value="<?= htmlspecialchars($user['nom_com'] ?? '') ?>" required>
            
            <label for="telefono">Teléfono:</label>
            <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($user['telefono'] ?? '') ?>">
            
            <label for="direccion">Dirección:</label>
            <input type="text" id="direccion" name="direccion" value="<?= htmlspecialchars($user['direccion'] ?? '') ?>">
            
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" rows="4"><?= htmlspecialchars($user['descripcion'] ?? '') ?></textarea>
            
            <label for="imagen">Foto de perfil:</label>
            <input type="file" id="imagen" name="imagen" accept="image/*">
            
            <button type="submit" class="btn-primary">Actualizar Perfil</button>
        </div>
    </form>
</main>

<?php
require_once VIEW_PATH . '../layouts/footer.php';