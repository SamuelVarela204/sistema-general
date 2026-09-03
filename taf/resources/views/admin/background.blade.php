@extends('layouts.app')
@section('content')
<section class="form-card">
    <p class="eyebrow">ADMINISTRACION</p>
    <h1>Fondo global</h1>
    <p>El fondo se muestra en toda la aplicación.</p>
    <form method="POST" action="{{ route('admin.background.update') }}" enctype="multipart/form-data">
        @csrf
        <label>Seleccionar imagen<input type="file" name="fondo" accept="image/jpeg,image/png,image/webp" required></label>
        <button class="button" type="submit">Guardar fondo</button>
    </form>
    @if($setting?->glob_wall)
        <form method="POST" action="{{ route('admin.background.destroy') }}" class="danger-form">
            @csrf @method('DELETE')
            <button type="submit">Eliminar fondo</button>
        </form>
    @endif
</section>
@endsection
