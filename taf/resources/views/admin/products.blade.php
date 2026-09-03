@extends('layouts.app')
@section('content')
<h1>Productos</h1>

@if(auth()->user()->hasRole('admin'))
<section class="form-card">
    <form method="POST" action="{{ route('admin.products.store') }}">
        @csrf
        <div class="form-grid">
            <input name="nom_pro" placeholder="Nombre" required>
            <input name="descripcion" placeholder="Descripción">
            <input name="precio" type="number" step=".01" placeholder="Precio" required>
            <input name="stock" type="number" placeholder="Stock" required>
            <input name="categoria" placeholder="Categoría" required>
        </div>
        <button class="button">Agregar producto</button>
    </form>
</section>
@endif

<section class="table-wrap">
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Acciones</th>
        </tr>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->id_pro }}</td>
            <td>{{ $product->nom_pro }}</td>
            <td>${{ number_format($product->precio, 2) }}</td>
            <td>{{ $product->stock }}</td>
            <td>
                @if(auth()->user()->hasRole('admin', 'inventario'))
                <form method="POST" action="{{ route('admin.products.update', $product) }}" style="display:inline-block; margin-right:0.5rem;">
                    @csrf
                    @method('PUT')
                    <input type="text" name="nom_pro" value="{{ $product->nom_pro }}" required style="width: 120px; margin-bottom:0.25rem; display:block;">
                    <input type="text" name="descripcion" value="{{ $product->descripcion ?? '' }}" style="width: 130px; margin-bottom:0.25rem; display:block;">
                    <input type="number" step="0.01" name="precio" value="{{ $product->precio }}" required style="width: 100px; margin-bottom:0.25rem; display:block;">
                    <input type="number" name="stock" value="{{ $product->stock }}" required style="width: 90px; margin-bottom:0.25rem; display:block;">
                    <input type="text" name="categoria" value="{{ $product->categoria }}" required style="width: 120px; margin-bottom:0.25rem; display:block;">
                    <button class="button" type="submit">Guardar</button>
                </form>
                @endif

                @if(auth()->user()->hasRole('admin'))
                <form method="POST" action="{{ route('admin.products.destroy', $product) }}" style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button class="link-danger" type="submit">Borrar</button>
                </form>
                @endif
            </td>
        </tr>
        @endforeach
    </table>
    {{ $products->links() }}
</section>
@endsection
