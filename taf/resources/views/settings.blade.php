@extends('layouts.app')
@section('content')
<section class="settings-page">
    <p class="eyebrow">PERSONALIZACION</p>
    <h1>Ajustes</h1>
    <p class="settings-intro">Configura la apariencia y el comportamiento de tu experiencia.</p>
    <div class="settings-list">
        <label class="setting-row"><span><strong>Modo alto contraste</strong><small>Mejora la legibilidad de textos y controles.</small></span><input class="setting-toggle" type="checkbox" data-setting="highContrast"><i></i></label>
        <label class="setting-row"><span><strong>Animaciones reducidas</strong><small>Reduce los movimientos de la interfaz.</small></span><input class="setting-toggle" type="checkbox" data-setting="noAnimations"><i></i></label>
        <label class="setting-row"><span><strong>Texto grande</strong><small>Aumenta el tamaño base de la interfaz.</small></span><input class="setting-toggle" type="checkbox" data-setting="largeText"><i></i></label>
    </div>
</section>
@endsection
