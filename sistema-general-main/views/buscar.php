<?php
if (!isset($con)) {
    $con = conectarBD();
}
$titulo = 'Búsqueda';
$productos = obtenerProductos($con); // todos los productos
?>
<div class="buscador-pagina">
    <div class="container">
        <div class="buscador-card">
            <h1>ENCUENTRA TU SABOR</h1>
            <p class="subtitulo">Busca recetas</p>
            <p class="descripcion">Explora jugos, postres y platos frescos.</p>

        <!-- DOS COLUMNAS -->
        <div class="two-columns">
            <!-- COLUMNA IZQUIERDA -->
            <div class="left-col">
                <!-- Buscador -->
                <div class="search-wrapper">
                    <input type="text" id="searchInput" class="input-pastel"
                           placeholder="Buscar productos, bebidas o postres.">
                    <button id="searchBtn" class="submit-btn">Buscar</button>
                </div>
                <p class="mensaje-info" id="mensajeInfo">Escribe algo en el buscador para ver resultados rápidos y detallados.</p>

                <!-- Productos desde la base de datos -->
                <div class="recomendados-grid" id="productosGrid">
                    <?php foreach ($productos as $prod): ?>
                        <div class="recomendado-card" data-nombre="<?= htmlspecialchars($prod['nom_pro']) ?>">
                            <img src="public/images/placeholder.png" alt="<?= htmlspecialchars($prod['nom_pro']) ?>" class="recomendado-img">
                            <h3><?= htmlspecialchars($prod['nom_pro']) ?></h3>
                            <p><?= htmlspecialchars($prod['descripcion']) ?></p>
                            <span class="precio-producto">$<?= number_format($prod['precio'], 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- COLUMNA DERECHA: Lista de recetas estática -->
            <div class="right-col">
                <div class="filtros-section">
                    <ul class="categorias-lista">
                        <li>Recetas <span class="numero">(35)</span></li>
                        <ul class="subcategorias">
                            <li data-categoria="bebidas">Bebidas <span class="numero">(9)</span></li>
                            <li data-categoria="cocteles">Cocteles <span class="numero">(6)</span></li>
                            <li data-categoria="cocina">Cocina <span class="numero">(6)</span></li>
                            <li data-categoria="salsas">Salsas y Aderezos <span class="numero">(3)</span></li>
                            <li data-categoria="helados">Helados <span class="numero">(3)</span></li>
                            <li data-categoria="postres">Postres <span class="numero">(14)</span></li>
                            <li data-categoria="batidos">Batidos <span class="numero">(3)</span></li>
                            <li data-categoria="comida">Comida <span class="numero">(2)</span></li>
                            <li data-categoria="flan">Flan <span class="numero">(2)</span></li>
                            <li data-categoria="galletas">Galletas <span class="numero">(1)</span></li>
                            <li data-categoria="granizados">Granizados <span class="numero">(1)</span></li>
                            <li data-categoria="limonadas">Limonadas <span class="numero">(2)</span></li>
                            <li data-categoria="margaritas">Margaritas <span class="numero">(3)</span></li>
                            <li data-categoria="mermeladas">Mermeladas <span class="numero">(1)</span></li>
                            <li data-categoria="mousse">Mousse <span class="numero">(5)</span></li>
                        </ul>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// ===== remplazo buscar.js (adaptado) =====
const searchInput = document.getElementById('searchInput');
const searchBtn = document.getElementById('searchBtn');
const categoriasItems = document.querySelectorAll('.subcategorias li');
const recomendados = document.querySelectorAll('.recomendado-card');
const mensajeInfo = document.getElementById('mensajeInfo');

function filtrarTodo() {
    const termino = searchInput.value.toLowerCase().trim();

    let algunaCategoriaVisible = false;
    categoriasItems.forEach(item => {
        const texto = item.textContent.toLowerCase();
        if (termino === '' || texto.includes(termino)) {
            item.classList.remove('hidden');
            algunaCategoriaVisible = true;
        } else {
            item.classList.add('hidden');
        }
    });

    let algunRecomendadoVisible = false;
    recomendados.forEach(rec => {
        const texto = rec.querySelector('h3').textContent.toLowerCase();
        const desc = rec.querySelector('p').textContent.toLowerCase();
        if (termino === '' || texto.includes(termino) || desc.includes(termino)) {
            rec.classList.remove('hidden');
            algunRecomendadoVisible = true;
        } else {
            rec.classList.add('hidden');
        }
    });

    if (termino !== '' && !algunaCategoriaVisible && !algunRecomendadoVisible) {
        mensajeInfo.innerHTML = '😿 No encontramos resultados para "' + termino + '". Intenta con otra palabra.';
        mensajeInfo.style.color = '#ff7a5c';
    } else if (termino === '') {
        mensajeInfo.innerHTML = '✨ Escribe algo en el buscador para ver resultados rápidos y detallados.';
        mensajeInfo.style.color = '#a07a7a';
    } else {
        mensajeInfo.innerHTML = '🔍 Mostrando resultados para: "' + termino + '"';
        mensajeInfo.style.color = '#6b9e6b';
    }
}

searchInput.addEventListener('input', filtrarTodo);
searchBtn.addEventListener('click', filtrarTodo);

categoriasItems.forEach(item => {
    item.addEventListener('click', () => {
        const texto = item.textContent.split('(')[0].trim();
        searchInput.value = texto;
        filtrarTodo();
    });
});

recomendados.forEach(rec => {
    rec.addEventListener('click', () => {
        const texto = rec.querySelector('h3').textContent;
        searchInput.value = texto;
        filtrarTodo();
    });
});

filtrarTodo();
</script>