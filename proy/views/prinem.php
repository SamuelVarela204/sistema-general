<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assest/styem.css">
    <title>inicial</title>
</head>
<body>
<!-- Inicio de sesion (botones en esquina) -->
    <div class="corner-buttons">
        <a href="inicio.php"><button type="button" class="regINI-buttons">Iniciar sesión</button></a>
        <a href="registro.php"><button type="button" class="regINI-buttons">Registro</button></a>
    </div>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- imagen de perfil centrada dentro del sidebar -->
    <div class="profile-center">
        <img src="anon enojao.webp" alt="Perfil">
    </div>
    <h2 style="text-align: center;">Anon I Mouse</h2>

    <!-- Menú del sidebar: 4 cards -->
    <nav class="sidebar-menu" aria-label="Menú lateral">
        <a href="perfil.html" class="menu-item">Perfil</a>
        <a href="pedidos.html" class="menu-item">Pedidos</a>
        <a href="recetas.html" class="menu-item">Recetas</a>
        <a href="ajustes.html" class="menu-item">Ajustes</a>
    </nav>
</div>

<!-- Holder al borde izquierdo: al acercar el ratón abre el sidebar -->
<div id="hoverHolder" title="Abrir menú"></div>

<div class="content">
    <!-- Buscador centrado y compactado -->
    <div class="search-container">
        <div class="search-box">
            <form action="resultado.html" method="get">
                <input type="text" name="q" placeholder="Buscar..." class="tab" autocomplete="off">
                <button class="but" type="submit">Buscar</button>
            </form>
        </div>
    </div>

    <!-- ...añadido: 6 cuadros (2 por fila) debajo del buscador -->
    <div class="cards-grid">
        <div class="card">
            <div class="thumb"><img src="placeholder.jfif" alt="Item 1"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
        <div class="card">
            <div class="thumb"><img src="placeholder.jfif" alt="Item 2"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
        <div class="card">
            <div class="thumb"><img src="placeholder.jfif" alt="Item 3"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
        <div class="card">
            <div class="thumb"><img src="placeholder.jfif" alt="Item 4"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
        <div class="card">
            <div class="thumb"><img src="placeholder.jfif" alt="Item 5"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
        <div class="card">
            <div class="thumb"><img src="placeholder.jfif" alt="Item 6"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
    </div>
</div>

<script>
    const sidebar = document.getElementById('sidebar');
    const holder = document.getElementById('hoverHolder');
    let open = false;
    let closeTimeout = null;

    function openSidebar() { clearTimeout(closeTimeout); sidebar.style.transform = 'translateX(0)'; open = true; }
    function closeSidebar() { sidebar.style.transform = 'translateX(-260px)'; open = false; }

    holder.addEventListener('mouseenter', openSidebar);
    holder.addEventListener('mouseleave', () => { closeTimeout = setTimeout(closeSidebar, 350); });
    sidebar.addEventListener('mouseenter', () => clearTimeout(closeTimeout));
    sidebar.addEventListener('mouseleave', () => { closeTimeout = setTimeout(closeSidebar, 350); });

    // asegurar sidebar cerrado al inicio
    closeSidebar();
</script>

</body>
</html>