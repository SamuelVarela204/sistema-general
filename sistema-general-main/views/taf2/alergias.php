<?php
require_once __DIR__ . '/../../includes/taf2/controlador.php';

// Obtener ID del usuario actual
$id_usuario = $_SESSION['usuario_id'] ?? 0;

if ($id_usuario <= 0) {
    redirigir('index.php?page=login');
}

// Obtener alergias del usuario
$alergias_usuario = obtenerAlergiasUsuario($con, $id_usuario);
$frutas_disponibles = obtenerFrutasDisponibles($con, $id_usuario);
$todas_frutas = obtenerFrutas($con);
?>
<div class="taf2-main">
    <section class="taf2-hero">
        <div class="taf2-hero-info">
            <h1>Gestionar Alergias</h1>
            <p>Selecciona las frutas o ingredientes a los que eres alérgico para que podamos personalizar nuestras recomendaciones.</p>
        </div>
    </section>

    <section class="taf2-card">
        <h3>Mis Alergias Actuales</h3>
        <div id="alergias-actuales" style="display: flex; flex-wrap: wrap; gap: 10px; margin-bottom: 20px;">
            <?php if (empty($alergias_usuario)): ?>
                <p style="color: #aaa; font-style: italic;">No has marcado ninguna alergia</p>
            <?php else: ?>
                <?php foreach ($alergias_usuario as $alergia): ?>
                    <div style="background: #e74c3c; color: white; padding: 8px 12px; border-radius: 20px; display: flex; align-items: center; gap: 8px;">
                        <span><?= htmlspecialchars($alergia['nom_fru']) ?></span>
                        <button type="button" 
                                onclick="removerAlergia(<?= (int)$alergia['id_fru'] ?>)"
                                style="background: none; border: none; color: white; cursor: pointer; font-size: 1.2rem; padding: 0;">
                            ✕
                        </button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <h3>Agregar Nueva Alergia</h3>
        <div style="display: flex; gap: 10px; flex-wrap: wrap;">
            <?php foreach ($frutas_disponibles as $fruta): ?>
                <button type="button"
                        onclick="agregarAlergia(<?= (int)$fruta['id_fru'] ?>, '<?= htmlspecialchars($fruta['nom_fru']) ?>')"
                        style="padding: 10px 15px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: 600;">
                    + <?= htmlspecialchars($fruta['nom_fru']) ?>
                </button>
            <?php endforeach; ?>
            
            <?php if (empty($frutas_disponibles)): ?>
                <p style="color: #aaa; font-style: italic;">Ya has marcado todas las frutas como alérgenas</p>
            <?php endif; ?>
        </div>
    </section>

    <section class="taf2-card">
        <h3>Todas las Frutas Disponibles</h3>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px;">
            <?php foreach ($todas_frutas as $fruta): 
                $es_alergia = array_filter($alergias_usuario, fn($a) => $a['id_fru'] == $fruta['id_fru']);
            ?>
                <div style="padding: 15px; border: 2px solid <?= !empty($es_alergia) ? '#e74c3c' : '#ddd' ?>; border-radius: 8px; text-align: center; background: <?= !empty($es_alergia) ? '#ffe6e6' : '#f9f9f9' ?>;">
                    <strong><?= htmlspecialchars($fruta['nom_fru']) ?></strong>
                    <p style="font-size: 0.9rem; color: #999; margin: 8px 0 0 0;">
                        <?= !empty($es_alergia) ? '🚫 Alergia marcada' : '✓ Seguro' ?>
                    </p>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</div>

<script>
function agregarAlergia(idFru, nombreFru) {
    // Enviar al servidor
    fetch('index.php?page=taf2&action=agregar_alergia', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id_fru=' + idFru
    })
    .then(() => {
        // Recargar la página
        location.reload();
    })
    .catch(err => console.error('Error:', err));
}

function removerAlergia(idFru) {
    // Enviar al servidor
    fetch('index.php?page=taf2&action=remover_alergia', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'id_fru=' + idFru
    })
    .then(() => {
        // Recargar la página
        location.reload();
    })
    .catch(err => console.error('Error:', err));
}
</script>

<style>
.taf2-main section.taf2-card {
    margin-bottom: 30px;
}

.taf2-main section.taf2-card h3 {
    margin-bottom: 20px;
    color: #212529;
    font-size: 1.2rem;
}

#alergias-actuales {
    min-height: 40px;
    padding: 15px;
    background: #f9f9f9;
    border-radius: 5px;
}
</style>
