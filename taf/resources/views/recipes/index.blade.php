@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800">Recetas</h1>
        @if(auth()->user()?->hasRole('admin', 'gerente'))
        <a href="{{ route('recipes.create') }}" class="bg-gradient-to-r from-orange-400 to-pink-500 text-white px-6 py-3 rounded-lg hover:shadow-lg transition">
            + Crear Receta
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($recipes as $recipe)
        <div class="bg-white rounded-lg shadow-md hover:shadow-xl transition p-6 border-l-4 border-orange-400">
            <h3 class="text-xl font-semibold text-gray-800 mb-2">{{ $recipe->nombre_receta }}</h3>
            <p class="text-gray-600 text-sm mb-4 line-clamp-2">{{ $recipe->descripcion ?? 'Sin descripción' }}</p>
            
            <div class="flex justify-between items-center mb-4">
                <span class="text-2xl font-bold text-orange-500">${{ number_format($recipe->precio_base, 2) }}</span>
                <span class="inline-block bg-{{ $recipe->personalizable ? 'blue' : 'gray' }}-100 text-{{ $recipe->personalizable ? 'blue' : 'gray' }}-800 text-xs px-3 py-1 rounded-full">
                    {{ $recipe->personalizable ? 'Personalizable' : 'Fija' }}
                </span>
            </div>

            <p class="text-xs text-gray-500 mb-4">{{ $recipe->ingredientes->count() }} ingredientes • Creada por {{ $recipe->creador->nombre_usu ?? 'Sistema' }}</p>

            <a href="{{ route('recipes.show', $recipe->id_receta) }}" class="block text-center bg-blue-500 text-white py-2 rounded-lg hover:bg-blue-600 transition mb-3">
                Ver detalles
            </a>

            @if(auth()->user()?->hasRole('admin', 'gerente'))
            <div class="flex gap-2">
                <a href="{{ route('recipes.edit', $recipe->id_receta) }}" class="flex-1 text-center bg-yellow-400 text-white py-2 rounded-lg hover:bg-yellow-500 transition text-sm">
                    Editar
                </a>
                <form action="{{ route('recipes.destroy', $recipe->id_receta) }}" method="POST" class="flex-1">
                    @csrf
                    @method('DELETE')
                    <button type="submit" onclick="return confirm('¿Eliminar esta receta?')" class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600 transition text-sm">
                        Eliminar
                    </button>
                </form>
            </div>
            @endif
        </div>
        @empty
        <div class="col-span-full text-center py-12 bg-gray-50 rounded-lg">
            <p class="text-gray-500 text-lg">No hay recetas registradas aún.</p>
        </div>
        @endforelse
    </div>

    @if($recipes instanceof \Illuminate\Pagination\Paginator)
    <div class="mt-8">
        {{ $recipes->links() }}
    </div>
    @endif
</div>
@endsection
