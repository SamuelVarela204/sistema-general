@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <a href="{{ route('recipes.index') }}" class="text-orange-500 hover:text-orange-600 mb-6 inline-flex items-center">
        ← Volver a Recetas
    </a>

    <div class="bg-white rounded-lg shadow-lg p-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Editar Receta: {{ $recipe->nombre_receta }}</h1>

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <strong>Errores de validación:</strong>
            <ul class="list-disc ml-5 mt-2">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('recipes.update', $recipe->id_receta) }}" method="POST" id="recipeForm">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Nombre de la Receta *</label>
                    <input type="text" name="nombre_receta" value="{{ $recipe->nombre_receta }}" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-orange-400 outline-none">
                    @error('nombre_receta')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>

                <div>
                    <label class="block text-gray-700 font-semibold mb-2">Precio Base *</label>
                    <input type="number" name="precio_base" step="0.01" value="{{ $recipe->precio_base }}" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-orange-400 outline-none">
                    @error('precio_base')<span class="text-red-500 text-sm">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Descripción</label>
                <textarea name="descripcion" rows="3" class="w-full px-4 py-2 border-2 rounded-lg focus:border-orange-400 outline-none">{{ $recipe->descripcion }}</textarea>
            </div>

            <div class="mb-6">
                <label class="flex items-center gap-3">
                    <input type="checkbox" name="personalizable" value="1" {{ $recipe->personalizable ? 'checked' : '' }} class="w-5 h-5">
                    <span class="text-gray-700 font-semibold">Permitir personalización</span>
                </label>
            </div>

            <div class="border-t-2 border-gray-200 pt-8 mb-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Ingredientes</h2>

                <div id="ingredientesContainer">
                    @foreach($recipe->ingredientes as $index => $ing)
                    <div class="ingrediente-row bg-gray-50 p-4 rounded-lg mb-4 flex gap-4 items-end">
                        <div class="flex-1">
                            <label class="block text-gray-700 font-semibold mb-2">Ingrediente *</label>
                            <select name="ingredientes[{{ $index }}][id]" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-orange-400 outline-none">
                                <option value="">Seleccionar ingrediente...</option>
                                @foreach($ingredients as $igr)
                                <option value="{{ $igr->id_ingrediente }}" {{ $ing->id_ingrediente == $igr->id_ingrediente ? 'selected' : '' }}>{{ $igr->nombre_ingrediente }} ({{ $igr->tipo }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="w-32">
                            <label class="block text-gray-700 font-semibold mb-2">Cantidad *</label>
                            <input type="number" name="ingredientes[{{ $index }}][cantidad]" step="0.01" min="0.01" value="{{ $ing->pivot->cantidad_requerida }}" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-orange-400 outline-none">
                        </div>
                        <div class="w-32">
                            <label class="block text-gray-700 font-semibold mb-2">Unidad *</label>
                            <input type="text" name="ingredientes[{{ $index }}][unidad]" value="{{ $ing->pivot->unidad_medida }}" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-orange-400 outline-none">
                        </div>
                        <button type="button" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 remove-ingrediente">
                            Quitar
                        </button>
                    </div>
                    @endforeach
                </div>

                <button type="button" id="addIngrediente" class="mt-4 bg-green-500 text-white px-6 py-2 rounded-lg hover:bg-green-600">
                    + Agregar Ingrediente
                </button>
            </div>

            <div class="flex gap-4">
                <button type="submit" class="bg-gradient-to-r from-orange-400 to-pink-500 text-white px-8 py-3 rounded-lg hover:shadow-lg transition">
                    Guardar Cambios
                </button>
                <a href="{{ route('recipes.show', $recipe->id_receta) }}" class="bg-gray-400 text-white px-8 py-3 rounded-lg hover:bg-gray-500 transition">
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
let ingredienteCount = {{ $recipe->ingredientes->count() }};

document.getElementById('addIngrediente').addEventListener('click', function() {
    const container = document.getElementById('ingredientesContainer');
    const ingredientsHtml = `
        <div class="ingrediente-row bg-gray-50 p-4 rounded-lg mb-4 flex gap-4 items-end">
            <div class="flex-1">
                <label class="block text-gray-700 font-semibold mb-2">Ingrediente *</label>
                <select name="ingredientes[${ingredienteCount}][id]" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-orange-400 outline-none">
                    <option value="">Seleccionar ingrediente...</option>
                    @foreach($ingredients as $ing)
                    <option value="{{ $ing->id_ingrediente }}">{{ $ing->nombre_ingrediente }} ({{ $ing->tipo }})</option>
                    @endforeach
                </select>
            </div>
            <div class="w-32">
                <label class="block text-gray-700 font-semibold mb-2">Cantidad *</label>
                <input type="number" name="ingredientes[${ingredienteCount}][cantidad]" step="0.01" min="0.01" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-orange-400 outline-none">
            </div>
            <div class="w-32">
                <label class="block text-gray-700 font-semibold mb-2">Unidad *</label>
                <input type="text" name="ingredientes[${ingredienteCount}][unidad]" required class="w-full px-4 py-2 border-2 rounded-lg focus:border-orange-400 outline-none" placeholder="ml, g, etc">
            </div>
            <button type="button" class="bg-red-500 text-white px-4 py-2 rounded-lg hover:bg-red-600 remove-ingrediente">
                Quitar
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', ingredientsHtml);
    ingredienteCount++;
    attachRemoveListeners();
});

function attachRemoveListeners() {
    document.querySelectorAll('.remove-ingrediente').forEach(btn => {
        btn.addEventListener('click', function() {
            this.parentElement.remove();
        });
    });
}

attachRemoveListeners();
</script>
@endsection
