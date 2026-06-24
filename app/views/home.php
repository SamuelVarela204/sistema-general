<?php
// app/views/home.php
require_once LAYOUTS_PATH . '/header.php';
$titulo = 'Inicio';
?>

<div class="pagina-completa">
    <div class="marca">
        <div class="logo-circle">
            <span>T&F</span>
        </div>
        <h2>Tropical y fresco</h2>
        <p>Sabores naturales que transforman tu día</p>
    </div>
    
    <div class="contenido-principal">
        <div class="columna-de-accion">
            <h3 style="text-align: center;">¿Qué buscas hoy?</h3>
            <div style="text-align: center; margin-bottom: 10px;">Encuentra tu bebida favorita</div>
            
            <form method="GET" action="<?= BASE_URL ?>/buscar" class="search-wrapper">
                <input type="text" name="q" placeholder="Buscar productos..." required>
                <button type="submit" class="submit-btn">Buscar</button>
            </form>
        </div>
    </div>
</div>

<?php
require_once LAYOUTS_PATH . '/footer.php';
?>