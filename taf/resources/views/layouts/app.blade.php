<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Tropical & Fresh' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>body { background-image: radial-gradient(circle at 90% 0%, #f8dce5 0, transparent 30%), url("{{ route('background.image') }}"); background-size: cover; background-attachment: fixed; }</style>
</head>
<body class="{{ auth()->check() ? 'has-sidebar' : '' }}">
@auth
<button class="sidebar-toggle" id="sidebarToggle" type="button" aria-label="Abrir menú" aria-expanded="false">☰</button>
<aside class="sidebar" id="sidebar">
    <a class="sidebar-back" href="{{ route('home') }}" aria-label="Volver al inicio">←</a>
    <div class="sidebar-avatar">@if(auth()->user()->imagen)<img src="{{ route('profile.image') }}" alt="Foto de perfil">@else{{ strtoupper(substr(auth()->user()->nom_com, 0, 1)) }}@endif</div>
    <h2>{{ auth()->user()->nom_com }}</h2><p class="sidebar-role">{{ auth()->user()->role_name ?: 'cliente' }}</p>
    <nav class="sidebar-menu">
        <a href="{{ route('profile') }}">Mi perfil</a>
        <a href="{{ route('orders') }}">Mis pedidos</a>
        <a href="{{ route('recipes.index') }}">Recetas</a>
        <a href="{{ route('settings') }}">Ajustes</a>

        @if(auth()->user()?->hasRole('admin', 'gerente', 'cajero'))
        <div style="border-top: 1px solid rgba(255,255,255,0.3); margin: 0.5rem 0; padding-top: 0.5rem;">
            <p style="font-size: 0.8rem; opacity: 0.7; margin: 0.5rem 0;">OPERACIONES</p>
            <a href="{{ route('pos.index') }}">🛒 POS/Facturación</a>
            <a href="{{ route('pos.history') }}">📊 Historial Ventas</a>
        </div>
        @endif

        @if(auth()->user()?->hasRole('admin', 'gerente', 'inventario'))
        <div style="border-top: 1px solid rgba(255,255,255,0.3); margin: 0.5rem 0; padding-top: 0.5rem;">
            <p style="font-size: 0.8rem; opacity: 0.7; margin: 0.5rem 0;">INVENTARIO</p>
            <a href="{{ route('inventory.index') }}">📦 Gestión de Stock</a>
            <a href="{{ route('admin.products') }}">📦 Productos</a>
        </div>
        @endif

        @if(auth()->user()?->hasRole('admin', 'gerente'))
        <div style="border-top: 1px solid rgba(255,255,255,0.3); margin: 0.5rem 0; padding-top: 0.5rem;">
            <p style="font-size: 0.8rem; opacity: 0.7; margin: 0.5rem 0;">ADMINISTRACIÓN</p>
            <a href="{{ route('admin.dashboard') }}">🎛 Dashboard</a>
            <a href="{{ route('admin.users') }}">👥 Usuarios</a>
            <a href="{{ route('admin.products') }}">📦 Productos</a>
            <a href="{{ route('admin.orders') }}">📋 Pedidos</a>
        </div>
        @endif

        @if(auth()->user()?->hasRole('trabajador'))
        <div style="border-top: 1px solid rgba(255,255,255,0.3); margin: 0.5rem 0; padding-top: 0.5rem;">
            <p style="font-size: 0.8rem; opacity: 0.7; margin: 0.5rem 0;">PRODUCCIÓN</p>
            <a href="{{ route('recipes.index') }}">📖 Recetas</a>
        </div>
        @endif
    </nav>
    <div class="sidebar-bottom"><button id="notificationBell" class="notification-bell" type="button">Notificaciones <span id="notificationCount">0</span></button><form method="POST" action="{{ route('logout') }}">@csrf<button class="sidebar-logout">Cerrar sesión</button></form></div>
</aside>
<div class="sidebar-edge" id="sidebarEdge" aria-hidden="true"></div>
<div class="sidebar-overlay" id="sidebarOverlay"></div>
<section class="notification-panel" id="notificationPanel" aria-hidden="true"><div class="notification-header"><strong>Notificaciones</strong><button id="notificationClose" type="button" aria-label="Cerrar">×</button></div><div id="notificationList">No hay notificaciones nuevas.</div></section>
@endauth
<header class="topbar"><a class="brand" href="{{ route('home') }}">Tropical <strong>& Fresh</strong></a><nav>
    <a href="{{ route('home') }}">Inicio</a>
    @auth
    @else <a href="{{ route('login') }}">Iniciar sesión</a><a class="button" href="{{ route('register') }}">Crear cuenta</a>@endauth
</nav></header>
<main class="container">
@if(session('success'))<div class="flash success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="flash error">{{ $errors->first() }}</div>@endif
@yield('content')
</main>
<footer><a class="copyright-easter-egg" href="{{ route('siscop') }}" aria-label="Información de autor">©</a> Tropical & Fresh · Sabores naturales</footer>
</body>
</html>
