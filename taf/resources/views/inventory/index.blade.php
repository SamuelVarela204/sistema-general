@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-4xl font-bold text-gray-800">Inventario</h1>
        @if(auth()->user()?->hasRole('admin', 'gerente'))
        <a href="{{ route('inventory.create') }}" class="bg-gradient-to-r from-blue-400 to-blue-600 text-white px-6 py-3 rounded-lg hover:shadow-lg transition">
            + Nuevo Ingrediente
        </a>
        @endif
    </div>

    @if(session('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    {{-- Alertas de Stock Bajo --}}
    @if($alertas->count() > 0)
    <div class="bg-red-50 border-l-4 border-red-500 p-6 mb-8 rounded-r-lg">
        <h2 class="text-xl font-bold text-red-700 mb-4">⚠️ Alertas de Stock Bajo</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($alertas as $alerta)
            <div class="bg-white p-4 rounded-lg border-l-4 border-red-500">
                <p class="font-semibold text-gray-800">{{ $alerta->ingrediente->nombre_ingrediente }}</p>
                <p class="text-sm text-gray-600 mb-2">Stock actual: <strong class="text-red-600">{{ $alerta->stock_actual }}</strong> / Mínimo: {{ $alerta->stock_minimo }}</p>
                <form action="{{ route('inventory.alert.resolve', $alerta->id_alerta) }}" method="POST" class="inline">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="text-xs bg-green-500 text-white px-3 py-1 rounded hover:bg-green-600">Marcar Resuelta</button>
                </form>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Tabla de Ingredientes --}}
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-100 border-b-2">
                    <tr>
                        <th class="text-left px-6 py-4 font-semibold text-gray-700">Ingrediente</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-700">Tipo</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-700">Stock Actual</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-700">Stock Mínimo</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-700">Costo Unit.</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-700">Estado</th>
                        <th class="text-center px-6 py-4 font-semibold text-gray-700">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($ingredients as $ing)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-800">{{ $ing->nombre_ingrediente }}</p>
                            <p class="text-xs text-gray-500">ID: {{ $ing->id_ingrediente }}</p>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-700">{{ $ing->tipo }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="font-bold text-lg {{ $ing->stock_actual <= $ing->stock_minimo ? 'text-red-600' : 'text-green-600' }}">
                                {{ $ing->stock_actual }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center text-gray-700">{{ $ing->stock_minimo }}</td>
                        <td class="px-6 py-4 text-center text-gray-700">${{ number_format($ing->costo_unitario, 2) }}</td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $ing->estado === 'activo' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ ucfirst($ing->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <a href="{{ route('inventory.show', $ing->id_ingrediente) }}" class="text-blue-500 hover:text-blue-700 font-semibold text-sm">Ver</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                            No hay ingredientes registrados.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-8">
        {{ $ingredients->links() }}
    </div>
</div>
@endsection
