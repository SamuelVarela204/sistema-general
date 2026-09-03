@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="{{ route('pos.index') }}" class="text-orange-500 hover:text-orange-600 mb-6 inline-flex items-center">
        ← Volver a POS
    </a>

    <h1 class="text-4xl font-bold text-gray-800 mb-8">📊 Historial de Ventas</h1>

    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b-2">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">#Transacción</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-700">Fecha</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-700">Cajero</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-700">Productos</th>
                        <th class="text-right px-6 py-4 font-semibold text-gray-700">Total</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-700">Método Pago</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sales as $sale)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-800">#{{ $sale->id_venta }}</td>
                        <td class="px-6 py-4 text-center text-gray-700">{{ $sale->fecha_venta->format('d/m/Y H:i') }}</td>
                        <td class="px-6 py-4 text-center text-gray-700">{{ $sale->cajero->nombre_usu }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm font-semibold">
                                {{ $sale->detalles->count() }} item(s)
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="font-bold text-lg text-green-600">${{ number_format($sale->total, 2) }}</span>
                            @if($sale->descuento > 0)
                            <p class="text-xs text-gray-600">Desc: -${{ number_format($sale->descuento, 2) }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($sale->metodo_pago === 'efectivo')
                            <span class="inline-block bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">💵 Efectivo</span>
                            @elseif($sale->metodo_pago === 'tarjeta')
                            <span class="inline-block bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-sm">💳 Tarjeta</span>
                            @else
                            <span class="inline-block bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-sm">📱 Transfer</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('pos.receipt', $sale->id_venta) }}" class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Ver Recibo</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            No hay ventas registradas.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8">
        {{ $sales->links() }}
    </div>
</div>
@endsection
