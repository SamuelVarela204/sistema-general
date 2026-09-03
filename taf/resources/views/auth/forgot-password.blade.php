@extends('layouts.app')
@section('content')
<section class="form-card">
    <p class="eyebrow">RECUPERACION</p>
    <h1>Recupera tu cuenta</h1>
    <p>Indica tu correo y te enviaremos un enlace para crear una nueva contraseña.</p>
    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <label>Correo<input type="email" name="correo" value="{{ old('correo') }}" required autofocus></label>
        <button class="button" type="submit">Enviar enlace</button>
    </form>
    <p><a href="{{ route('login') }}">Volver al inicio de sesión</a></p>
</section>
@endsection
