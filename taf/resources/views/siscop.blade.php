@extends('layouts.app')
@section('content')
<section class="credits-page">
    <p class="eyebrow">TROPICAL & FRESH</p>
    <h1>Esta página está hecha por</h1>
    <div class="credits-grid">
        <article class="credit-person"><img src="{{ asset('images/sistema de copyright.gif') }}" alt="Ilustración de derechos de autor"><h2>Samuel Varela</h2></article>
        <article class="credit-person"><img src="{{ asset('images/mirk.png') }}" alt="Ilustración de Mirk"><h2>Diego Garcia</h2></article>
    </div>
    <p class="credits-note">El uso de esta página requiere autorización de sus autores.</p>
</section>
@endsection
