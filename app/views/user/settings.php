<?php
// app/views/user/settings.php
require_once LAYOUTS_PATH . '/header.php';
$titulo = 'Ajustes';
?>

<div class="settings-container">
    <div class="page-header">
        <h1>⚙️ Panel de ajustes</h1>
        <p>Personaliza la experiencia visual de la aplicación</p>
    </div>
    
    <div class="settings-card">
        <div class="setting-item">
            <div class="setting-info">
                <h2>tema negro
                    <span class="status-badge" id="statusLabel">Desactivado</span>
                </h2>
                <p class="desc">Activa el filtro de escala de grises en toda la aplicación</p>
            </div>
            <label class="toggle-switch">
                <input type="checkbox" id="grayscaleToggle">
                <span class="slider"></span>
            </label>
        </div>
    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';