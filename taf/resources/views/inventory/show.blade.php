@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="{{ route('inventory.index') }}" class="text-blue-500 hover:text-blue-600 mb-6 inline-flex items-center">
        ← Volver a Inventario
    </a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- Panel Izquierdo: Información del Ingrediente --}}
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-lg p-6 border-l-4 border-blue-400">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $ingredient->nombre_ingrediente }}</h2>
                
                <div class="space-y-4 mb-6">
                    <div>
                        <p class="text-sm text-gray-600">Tipo</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $ingredient->tipo }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Unidad de Medida</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $ingredient->unidad_medida }}</p>
                    </div>
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-lg">
                        <p class="text-sm text-gray-600">Stock Actual</p>
                        <p class="text-4xl font-bold text-blue-600">{{ $ingredient->stock_actual }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Stock Mínimo</p>
                        <p class="text-lg font-semibold text-gray-800">{{ $ingredient->stock_minimo }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Costo Unitario</p>
                        <p class="text-lg font-semibold text-gray-800">${{ number_format($ingredient->costo_unitario, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Estado</p>
                        <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $ingredient->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                            {{ ucfirst($ingredient->estado) }}
                        </span>
                    </div>
                </div>

                <div class="border-t pt-4">
                    <a href="{{ route('inventory.edit', $ingredient->id_ingrediente) }}" class="block text-center bg-yellow-400 text-white py-2 rounded-lg hover:bg-yellow-500 transition mb-2">
                        ✎ Editar
                    </a>
                </div>
            </div>

            {{-- Formulario de Movimiento --}}
            <div class="bg-white rounded-lg shadow-lg p-6 mt-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">Registrar Movimiento</h3>
                <form action="{{ route('inventory.movement.store', $ingredient->id_ingrediente) }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Tipo *</label>
                        <select name="tipo_movimiento" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-blue-400 outline-none">
                            <option value="">Seleccionar...</option>
                            <option value="entrada">Entrada (Compra)</option>
                            <option value="salida">Salida (Venta/Uso)</option>
                            <option value="ajuste">Ajuste</option>
                            <option value="merma">Merma (Daño/Vencido)</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Cantidad *</label>
                        <input type="number" name="cantidad" step="0.01" min="0.01" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-blue-400 outline-none" placeholder="0">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-2">Descripción</label>
                        <textarea name="descripcion" class="w-full px-4 py-2 border-2 rounded-lg focus:border-blue-400 outline-none" rows="2" placeholder="Motivo del movimiento..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-gradient-to-r from-blue-400 to-blue-600 text-white py-2 rounded-lg hover:shadow-lg transition">
                        Registrar Movimiento
                    </button>
                </form>
            </div>
        </div>

        {{-- Panel Derecho: Historial de Movimientos --}}
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Historial de Movimientos</h2>
                
                @if($movimientos->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 border-b-2">
                            <tr>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Fecha</th>
                                <th class="text-center px-4 py-3 font-semibold text-gray-700">Tipo</th>
                                <th class="text-center px-4 py-3 font-semibold text-gray-700">Cantidad</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Responsable</th>
                                <th class="text-left px-4 py-3 font-semibold text-gray-700">Descripción</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($movimientos as $mov)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-4 py-3 text-gray-700">{{ $mov->fecha_movimiento->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 text-center">
                                    <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                                        {{ $mov->tipo_movimiento === 'entrada' ? 'bg-green-100 text-green-700' : 
                                           ($mov->tipo_movimiento === 'salida' ? 'bg-red-100 text-red-700' : 
                                           ($mov->tipo_movimiento === 'merma' ? 'bg-yellow-100 text-yellow-700' : 'bg-blue-100 text-blue-700')) }}">
                                        {{ ucfirst($mov->tipo_movimiento) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center font-semibold">{{ $mov->cantidad }}</td>
                                <td class="px-4 py-3 text-gray-700">{{ $mov->responsable->nombre_usu ?? 'Sistema' }}</td>
                                <td class="px-4 py-3 text-gray-600">{{ $mov->descripcion ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-8">
                    {{ $movimientos->links() }}
                </div>
                @else
                <p class="text-center text-gray-500 py-8">No hay movimientos registrados.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
