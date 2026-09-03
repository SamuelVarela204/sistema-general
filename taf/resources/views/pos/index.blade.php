@extends('layouts.app')

@section('content')
<div class="container-fluid mx-auto px-4 py-8">
    <h1 class="text-4xl font-bold text-gray-800 mb-8">🛒 Punto de Venta (POS)</h1>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Sección de Productos/Recetas --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Seleccionar Bebidas</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    @forelse($recipes as $recipe)
                    <button type="button" onclick="addToCart({{ $recipe->id_receta }}, '{{ addslashes($recipe->nombre_receta) }}', {{ $recipe->precio_base }})" 
                        class="bg-gradient-to-br from-orange-300 to-pink-400 text-white p-4 rounded-lg hover:shadow-lg transition transform hover:scale-105 text-center">
                        <p class="font-bold text-sm">{{ $recipe->nombre_receta }}</p>
                        <p class="text-2xl font-bold mt-2">${{ number_format($recipe->precio_base, 2) }}</p>
                        <p class="text-xs mt-2">{{ $recipe->ingredientes->count() }} ingredientes</p>
                    </button>
                    @empty
                    <div class="col-span-full text-center py-12 text-gray-500">
                        No hay recetas disponibles. Crea recetas primero en el módulo de Recetas.
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Sección de Carrito --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 sticky top-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Carrito de Compra</h2>
                
                <div id="cartItems" class="space-y-3 mb-6 max-h-64 overflow-y-auto border-b pb-4">
                    <p class="text-gray-500 text-center py-4">Carrito vacío</p>
                </div>

                <div class="space-y-3 mb-6 border-t pt-4">
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-800">Subtotal:</span>
                        <span id="subtotalDisplay" class="font-bold text-lg">$0.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="font-semibold text-gray-800">Descuento:</span>
                        <input type="number" id="descuento" step="0.01" min="0" value="0" class="w-24 px-2 py-1 border rounded-lg" onchange="updateTotal()">
                    </div>
                    <div class="flex justify-between bg-gradient-to-r from-orange-400 to-pink-500 text-white p-3 rounded-lg">
                        <span class="font-bold">Total a Pagar:</span>
                        <span id="totalDisplay" class="font-bold text-xl">$0.00</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <div>
                        <label class="block text-gray-700 font-semibold mb-2">Método de Pago</label>
                        <select id="metodoPago" class="w-full px-4 py-2 border-2 rounded-lg focus:border-orange-400 outline-none">
                            <option value="efectivo">💵 Efectivo</option>
                            <option value="tarjeta">💳 Tarjeta</option>
                            <option value="transferencia">📱 Transferencia</option>
                        </select>
                    </div>
                    
                    <form id="saleForm" method="POST" action="{{ route('pos.store') }}">
                        @csrf
                        <input type="hidden" id="itemsInput" name="items" value="[]">
                        <input type="hidden" id="descuentoInput" name="descuento" value="0">
                        <input type="hidden" id="metodoPagoInput" name="metodo_pago" value="efectivo">
                        
                        <button type="submit" id="submitBtn" disabled class="w-full bg-gradient-to-r from-green-400 to-green-600 text-white py-3 rounded-lg hover:shadow-lg transition font-bold text-lg disabled:opacity-50">
                            ✓ Completar Venta
                        </button>
                    </form>
                    
                    <button type="button" onclick="clearCart()" class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition">
                        🗑 Limpiar Carrito
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let cart = {};
let recipeDetails = {};

// Cargar detalles de recetas (para ingredientes)
@foreach($recipes as $recipe)
recipeDetails[{{ $recipe->id_receta }}] = {
    nombre: '{{ addslashes($recipe->nombre_receta) }}',
    precio: {{ $recipe->precio_base }},
    ingredientes: [
        @foreach($recipe->ingredientes as $ing)
        { nombre: '{{ addslashes($ing->nombre_ingrediente) }}', cantidad: {{ $ing->pivot->cantidad_requerida }}, unidad: '{{ $ing->pivot->unidad_medida }}' },
        @endforeach
    ]
};
@endforeach

function addToCart(recipeId, recipeName, price) {
    if (cart[recipeId]) {
        cart[recipeId].cantidad++;
    } else {
        cart[recipeId] = {
            id: recipeId,
            nombre: recipeName,
            precio: price,
            cantidad: 1,
            personalizacion: ''
        };
    }
    updateCart();
}

function removeFromCart(recipeId) {
    delete cart[recipeId];
    updateCart();
}

function updateCartQuantity(recipeId, qty) {
    if (qty <= 0) {
        removeFromCart(recipeId);
    } else {
        cart[recipeId].cantidad = parseInt(qty);
        updateCart();
    }
}

function updateCart() {
    const cartItems = document.getElementById('cartItems');
    const items = Object.values(cart);
    
    if (items.length === 0) {
        cartItems.innerHTML = '<p class="text-gray-500 text-center py-4">Carrito vacío</p>';
        document.getElementById('submitBtn').disabled = true;
    } else {
        cartItems.innerHTML = items.map(item => `
            <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                <div class="flex-1">
                    <p class="font-semibold text-sm">${item.nombre}</p>
                    <p class="text-xs text-gray-600">$${item.precio.toFixed(2)} c/u</p>
                </div>
                <div class="flex items-center gap-1">
                    <input type="number" min="1" value="${item.cantidad}" onchange="updateCartQuantity(${item.id}, this.value)" class="w-12 px-1 py-1 border rounded text-center text-sm">
                    <button type="button" onclick="removeFromCart(${item.id})" class="text-red-500 hover:text-red-700 text-sm">✕</button>
                </div>
            </div>
        `).join('');
        document.getElementById('submitBtn').disabled = false;
    }
    
    updateTotal();
}

function updateTotal() {
    const items = Object.values(cart);
    let subtotal = items.reduce((sum, item) => sum + (item.precio * item.cantidad), 0);
    const descuento = parseFloat(document.getElementById('descuento').value) || 0;
    const total = Math.max(0, subtotal - descuento);
    
    document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
    document.getElementById('totalDisplay').textContent = '$' + total.toFixed(2);
    
    // Actualizar inputs para formulario
    document.getElementById('itemsInput').value = JSON.stringify(
        items.map(item => ({
            id_receta: item.id,
            cantidad: item.cantidad,
            precio_unitario: item.precio,
            personalizacion: item.personalizacion || null
        }))
    );
    document.getElementById('descuentoInput').value = descuento;
    document.getElementById('metodoPagoInput').value = document.getElementById('metodoPago').value;
}

function clearCart() {
    if (confirm('¿Limpiar el carrito?')) {
        cart = {};
        updateCart();
    }
}

document.getElementById('metodoPago').addEventListener('change', updateTotal);
</script>
@endsection
