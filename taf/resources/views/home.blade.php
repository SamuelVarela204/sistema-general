@extends('layouts.app')
@section('content')
<section class="hero"><p class="eyebrow">PRODUCTOS FRESCOS</p><h1>Sabores naturales que transforman tu día.</h1><p>Encuentra bebidas, ensaladas y postres preparados con ingredientes de calidad.</p><form class="search" method="GET" action="{{ route('home') }}"><input name="q" value="{{ $search }}" placeholder="Buscar producto..."><button>Buscar</button></form></section>
<section class="grid">@forelse($products as $product)<article class="card"><span class="tag">{{ $product->categoria }}</span><h2>{{ $product->nom_pro }}</h2><p>{{ $product->descripcion }}</p><strong>${{ number_format($product->precio, 2) }}</strong></article>@empty<p>No hay productos disponibles.</p>@endforelse</section>
@endsection
