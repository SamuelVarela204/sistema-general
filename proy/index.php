<?php
// Iniciar sesión para permitir personalización y control de acceso.
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/sty.css">
    <link rel="icon" href="../image/placeholder.png">   
    <title>inicial</title>
</head>
<body>
<!-- Inicio de sesión: botones visibles para usuario no autenticado / cerrar sesión para usuario autenticado -->
<?php if (empty($_SESSION['usuario'])): ?>
    <div class="corner-buttons">
        <a href="views/inicio.php"><button type="button" class="regINI-buttons">Iniciar sesión</button></a>
        <a href="views/registro.php"><button type="button" class="regINI-buttons">Registro</button></a>
    </div>
<?php else: ?>
    <div class="corner-buttons">
        <form action="logout.php" method="post" style="display: inline;">
            <button type="submit" class="regINI-buttons">Cerrar sesión</button>
        </form>
    </div>
<?php endif; ?>
<!-- Sidebar -->
<?php if (isset($_SESSION['usuario'])): ?>
<div class="sidebar" id="sidebar">
    <!-- imagen de perfil centrada dentro del sidebar -->
    <div class="profile-center">
        <?php if (isset($_SESSION['imagen']) && $_SESSION['imagen']): ?>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($_SESSION['imagen']); ?>" alt="Perfil">
        <?php endif; ?>
    </div>
    <?php if (isset($_SESSION['usuario'])): ?>
        <h2 style="text-align: center;"><?php echo htmlspecialchars($_SESSION['usuario']); ?></h2>
    <?php endif; ?>

    <!-- Menú del sidebar: 4 cards -->
    <nav class="sidebar-menu" aria-label="Menú lateral">
        <a href="views/perfil.php" class="menu-item">Perfil</a>
        <a href="views/pedidos.html" class="menu-item">Pedidos</a>
        <a href="views/recetas.html" class="menu-item">Recetas</a>
        <a href="views/ajustes.html" class="menu-item">Ajustes</a>
    </nav>
</div>

<!-- Holder al borde izquierdo: al acercar el ratón abre el sidebar -->
<div id="hoverHolder" title="Abrir menú"></div>
<?php endif; ?>

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
            <div class="thumb"><img src="image/placeholder.jfif" alt="Item 1"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
        <div class="card">
            <div class="thumb"><img src="image/placeholder.jfif" alt="Item 2"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
        <div class="card">
            <div class="thumb"><img src="image/placeholder.jfif" alt="Item 3"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
        <div class="card">
            <div class="thumb"><img src="image/placeholder.jfif" alt="Item 4"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
        <div class="card">
            <div class="thumb"><img src="image/placeholder.jfif" alt="Item 5"></div>
            <div class="info"><h3>placeholder</h3><p>Descripción breve de la bebida</p></div>
        </div>
        <div class="card">
            <div class="thumb"><img src="image/placeholder.jfif" alt="Item 6"></div>
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
<script>
    document.documentElement.style.zoom = '100%';
</script>
<footer class="site-footer" aria-label="Pie de página">
    <div class="footer-inner">
        <p><a href="views/siscop.php" class="footer-link">&copy;</a>2025 Samuel Varela — Todos los derechos reservados.</p><br>
    </div>
</footer>

</body>
</html>