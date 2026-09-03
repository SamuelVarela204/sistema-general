@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="{{ route('inventory.index') }}" class="text-blue-500 hover:text-blue-600 mb-6 inline-flex items-center">
        ← Volver a Inventario
    </a>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Editar Ingrediente: {{ $ingredient->nombre_ingrediente }}</h1>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <strong>Errores:</strong>
            <ul class="list-disc ml-5 mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('inventory.update', $ingredient->id_ingrediente) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Nombre del Ingrediente *</label>
                    <input type="text" name="nombre_ingrediente" value="{{ $ingredient->nombre_ingrediente }}" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-blue-400 outline-none">
                    @error('nombre_ingrediente')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Tipo *</label>
                    <select name="tipo" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-blue-400 outline-none">
                        <option value="fruta" {{ $ingredient->tipo === 'fruta' ? 'selected' : '' }}>Fruta</option>
                        <option value="verdura" {{ $ingredient->tipo === 'verdura' ? 'selected' : '' }}>Verdura</option>
                        <option value="bebida" {{ $ingredient->tipo === 'bebida' ? 'selected' : '' }}>Bebida</option>
                        <option value="endulzante" {{ $ingredient->tipo === 'endulzante' ? 'selected' : '' }}>Endulzante</option>
                        <option value="especias" {{ $ingredient->tipo === 'especias' ? 'selected' : '' }}>Especias</option>
                        <option value="empaques" {{ $ingredient->tipo === 'empaques' ? 'selected' : '' }}>Empaques</option>
                        <option value="otro" {{ $ingredient->tipo === 'otro' ? 'selected' : '' }}>Otro</option>
                    </select>
                    @error('tipo')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Descripción</label>
                <textarea name="descripcion" rows="2" class="w-full px-4 py-2 border-2 rounded-lg focus:border-blue-400 outline-none">{{ $ingredient->descripcion }}</textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Unidad de Medida *</label>
                    <input type="text" name="unidad_medida" value="{{ $ingredient->unidad_medida }}" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-blue-400 outline-none">
                    @error('unidad_medida')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Costo Unitario ($) *</label>
                    <input type="number" name="costo_unitario" step="0.01" value="{{ $ingredient->costo_unitario }}" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-blue-400 outline-none">
                    @error('costo_unitario')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Stock Mínimo (Unidades) *</label>
                    <input type="number" name="stock_minimo" value="{{ $ingredient->stock_minimo }}" min="1" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-blue-400 outline-none">
                    @error('stock_minimo')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Estado *</label>
                    <select name="estado" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-blue-400 outline-none">
                        <option value="activo" {{ $ingredient->estado === 'activo' ? 'selected' : '' }}>Activo</option>
                        <option value="inactivo" {{ $ingredient->estado === 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                    </select>
                    @error('estado')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-blue-400 to-blue-600 text-white px-8 py-3 rounded-lg hover:shadow-lg transition">
                    Guardar Cambios
                </button>
                <a href="{{ route('inventory.show', $ingredient->id_ingrediente) }}" class="bg-gray-400 text-white px-8 py-3 rounded-lg hover:bg-gray-500 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
