@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="{{ route('pos.index') }}" class="text-orange-500 hover:text-orange-600 mb-6 inline-flex items-center">
        ← Volver a POS
    </a>

    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8 border-t-4 border-green-500">
        <div class="text-center mb-8">
            <h1 class="text-4xl font-bold text-gray-800">✓ Venta Completada</h1>
            <p class="text-gray-600 mt-2">Número de transacción: <strong>#{{ $sale->id_venta }}</strong></p>
        </div>

        <div class="bg-gray-50 p-6 rounded-lg mb-8">
            <div class="mb-6 pb-6 border-b-2">
                <p class="text-sm text-gray-600">Cajero</p>
                <p class="text-lg font-semibold text-gray-800">{{ $sale->cajero->nombre_usu }}</p>
            </div>

            <div class="mb-6 pb-6 border-b-2">
                <p class="text-sm text-gray-600">Fecha y Hora</p>
                <p class="text-lg font-semibold text-gray-800">{{ $sale->fecha_venta->format('d/m/Y H:i:s') }}</p>
            </div>

            <div class="mb-6 pb-6 border-b-2">
                <p class="text-sm text-gray-600">Método de Pago</p>
                <p class="text-lg font-semibold text-gray-800">
                    @if($sale->metodo_pago === 'efectivo')
                    💵 Efectivo
                    @elseif($sale->metodo_pago === 'tarjeta')
                    💳 Tarjeta
                    @else
                    📱 Transferencia
                    @endif
                </p>
            </div>
        </div>

        <div class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-4">Detalle de Productos</h2>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-100 border-b-2">
                        <tr>
                            <th class="text-left px-4 py-3 font-semibold">Producto</th>
                            <th class="text-center px-4 py-3 font-semibold">Cant.</th>
                            <th class="text-right px-4 py-3 font-semibold">Precio Unit.</th>
                            <th class="text-right px-4 py-3 font-semibold">Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->detalles as $detalle)
                        <tr class="border-b">
                            <td class="px-4 py-3">
                                <p class="font-semibold text-gray-800">{{ $detalle->receta->nombre_receta }}</p>
                                @if($detalle->personalizacion)
                                <p class="text-xs text-gray-600">Personalización: {{ $detalle->personalizacion }}</p>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center font-semibold">{{ $detalle->cantidad }}</td>
                            <td class="px-4 py-3 text-right">${{ number_format($detalle->precio_unitario, 2) }}</td>
                            <td class="px-4 py-3 text-right font-semibold">${{ number_format($detalle->subtotal, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-50 to-pink-50 p-6 rounded-lg mb-8">
            <div class="flex justify-between mb-3 pb-3 border-b-2">
                <span class="text-gray-700">Subtotal:</span>
                <span class="font-semibold">${{ number_format($sale->total + $sale->descuento, 2) }}</span>
            </div>
            @if($sale->descuento > 0)
            <div class="flex justify-between mb-3 pb-3 border-b-2">
                <span class="text-gray-700">Descuento:</span>
                <span class="font-semibold text-red-600">-${{ number_format($sale->descuento, 2) }}</span>
            </div>
            @endif
            <div class="flex justify-between">
                <span class="text-xl font-bold text-gray-800">Total a Pagar:</span>
                <span class="text-3xl font-bold text-green-600">${{ number_format($sale->total, 2) }}</span>
            </div>
        </div>

        <div class="flex gap-4">
            <a href="{{ route('pos.index') }}" class="flex-1 text-center bg-gradient-to-r from-orange-400 to-pink-500 text-white py-3 rounded-lg hover:shadow-lg transition font-bold">
                + Nueva Venta
            </a>
            <a href="{{ route('pos.history') }}" class="flex-1 text-center bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600 transition font-bold">
                📊 Ver Historial
            </a>
        </div>

        <button type="button" onclick="window.print()" class="w-full mt-4 bg-gray-500 text-white py-2 rounded-lg hover:bg-gray-600 transition">
            🖨 Imprimir Recibo
        </button>
    </div>
</div>
@endsection
