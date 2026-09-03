@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="{{ route('recipes.index') }}" class="text-orange-500 hover:text-orange-600 mb-6 inline-flex items-center">
        ← Volver a Recetas
    </a>

    <div class="bg-white rounded-lg shadow-lg p-8 border-l-4 border-orange-400">
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">{{ $recipe->nombre_receta }}</h1>
            <p class="text-gray-600 mb-6">{{ $recipe->descripcion ?? 'Sin descripción' }}</p>
            
            <div class="flex gap-6 mb-6">
                <div>
                    <p class="text-gray-600 text-sm">Precio Base</p>
                    <p class="text-3xl font-bold text-orange-500">${{ number_format($recipe->precio_base, 2) }}</p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Personalización</p>
                    <p class="text-lg font-semibold {{ $recipe->personalizable ? 'text-green-600' : 'text-gray-600' }}">
                        {{ $recipe->personalizable ? '✓ Permitida' : '✗ No permitida' }}
                    </p>
                </div>
                <div>
                    <p class="text-gray-600 text-sm">Estado</p>
                    <span class="inline-block bg-green-100 text-green-800 px-3 py-1 rounded-full text-sm font-semibold">
                        {{ ucfirst($recipe->estado) }}
                    </span>
                </div>
            </div>
        </div>

        <div class="border-t-2 border-gray-200 pt-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Ingredientes</h2>
            
            @if($recipe->ingredientes->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-gray-100 border-b-2">
                            <th class="text-left px-4 py-3 font-semibold text-gray-700">Ingrediente</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-700">Cantidad</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-700">Unidad</th>
                            <th class="text-center px-4 py-3 font-semibold text-gray-700">Stock Actual</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recipe->ingredientes as $ingrediente)
                        <tr class="border-b hover:bg-gray-50">
                            <td class="px-4 py-3">
                                <span class="font-semibold text-gray-800">{{ $ingrediente->nombre_ingrediente }}</span>
                                <p class="text-xs text-gray-500">{{ $ingrediente->tipo }}</p>
                            </td>
                            <td class="px-4 py-3 text-center font-semibold">{{ $ingrediente->pivot->cantidad_requerida }}</td>
                            <td class="px-4 py-3 text-center">{{ $ingrediente->pivot->unidad_medida }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="inline-block px-3 py-1 rounded-full text-sm font-semibold {{ $ingrediente->stock_actual > $ingrediente->stock_minimo ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                    {{ $ingrediente->stock_actual }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <p class="text-gray-500 text-center py-8">Esta receta no tiene ingredientes asignados.</p>
            @endif
        </div>

        @if(auth()->user()?->hasRole('admin', 'gerente'))
        <div class="mt-8 flex gap-4">
            <a href="{{ route('recipes.edit', $recipe->id_receta) }}" class="bg-yellow-400 text-white px-6 py-3 rounded-lg hover:bg-yellow-500 transition">
                ✎ Editar Receta
            </a>
            <form action="{{ route('recipes.destroy', $recipe->id_receta) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" onclick="return confirm('¿Eliminar esta receta?')" class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 transition">
                    🗑 Eliminar
                </button>
            </form>
        </div>
        @endif
    </div>
</div>
@endsection
