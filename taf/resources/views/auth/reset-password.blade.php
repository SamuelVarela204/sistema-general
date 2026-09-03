@extends('layouts.app')
@section('content')
<section class="form-card">
    <p class="eyebrow">RECUPERACION</p>
    <h1>Nueva contraseña</h1>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <input type="hidden" name="correo" value="{{ $correo }}">
        <label>Nueva contraseña<input type="password" name="contrasena" minlength="6" required autocomplete="new-password"></label>
        <label>Confirmar contraseña<input type="password" name="contrasena_confirmation" minlength="6" required autocomplete="new-password"></label>
        <button class="button" type="submit">Restablecer contraseña</button>
    </form>
</section>
@endsection
