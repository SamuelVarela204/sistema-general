<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Ingredient;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RecipeController extends Controller
{
    // Solo admin y gerente pueden crear/editar/eliminar recetas
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!$request->user()?->hasRole('admin', 'gerente')) {
                abort(403, 'No tienes permiso para gestionar recetas.');
            }
            return $next($request);
        })->except(['index', 'show']);
    }

    public function index(): View
    {
        $recipes = Recipe::with('ingredientes', 'creador')
            ->orderBy('fecha_creacion', 'desc')
            ->paginate(15);
        
        return view('recipes.index', compact('recipes'));
    }

    public function show(Recipe $recipe): View
    {
        $recipe->load('ingredientes', 'creador');
        return view('recipes.show', compact('recipe'));
    }

    public function create(): View
    {
        $ingredients = Ingredient::where('estado', 'activo')->get();
        return view('recipes.create', compact('ingredients'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_receta' => 'required|string|unique:recetas|max:225',
            'descripcion' => 'nullable|string',
            'precio_base' => 'required|numeric|min:0',
            'personalizable' => 'boolean',
            'ingredientes' => 'required|array|min:1',
            'ingredientes.*.id' => 'required|exists:ingredientes,id_ingrediente',
            'ingredientes.*.cantidad' => 'required|numeric|min:0.01',
            'ingredientes.*.unidad' => 'required|string',
        ]);

        $recipe = Recipe::create([
            'nombre_receta' => $validated['nombre_receta'],
            'descripcion' => $validated['descripcion'] ?? null,
            'precio_base' => $validated['precio_base'],
            'personalizable' => $validated['personalizable'] ?? true,
            'estado' => 'activo',
            'id_usu_creador' => auth()->id(),
            'fecha_creacion' => now(),
            'fecha_actualizacion' => now(),
        ]);

        // Attach ingredientes
        foreach ($validated['ingredientes'] as $ing) {
            $recipe->ingredientes()->attach($ing['id'], [
                'cantidad_requerida' => $ing['cantidad'],
                'unidad_medida' => $ing['unidad'],
            ]);
        }

        return redirect()->route('recipes.show', $recipe->id_receta)
                        ->with('success', 'Receta creada exitosamente.');
    }

    public function edit(Recipe $recipe): View
    {
        $ingredients = Ingredient::where('estado', 'activo')->get();
        $recipe->load('ingredientes');
        return view('recipes.edit', compact('recipe', 'ingredients'));
    }

    public function update(Request $request, Recipe $recipe): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_receta' => 'required|string|unique:recetas,nombre_receta,' . $recipe->id_receta . ',id_receta|max:225',
            'descripcion' => 'nullable|string',
            'precio_base' => 'required|numeric|min:0',
            'personalizable' => 'boolean',
            'ingredientes' => 'required|array|min:1',
            'ingredientes.*.id' => 'required|exists:ingredientes,id_ingrediente',
            'ingredientes.*.cantidad' => 'required|numeric|min:0.01',
            'ingredientes.*.unidad' => 'required|string',
        ]);

        $recipe->update([
            'nombre_receta' => $validated['nombre_receta'],
            'descripcion' => $validated['descripcion'] ?? null,
            'precio_base' => $validated['precio_base'],
            'personalizable' => $validated['personalizable'] ?? true,
            'fecha_actualizacion' => now(),
        ]);

        // Sync ingredientes
        $syncData = [];
        foreach ($validated['ingredientes'] as $ing) {
            $syncData[$ing['id']] = [
                'cantidad_requerida' => $ing['cantidad'],
                'unidad_medida' => $ing['unidad'],
            ];
        }
        $recipe->ingredientes()->sync($syncData);

        return redirect()->route('recipes.show', $recipe->id_receta)
                        ->with('success', 'Receta actualizada exitosamente.');
    }

    public function destroy(Recipe $recipe): RedirectResponse
    {
        $recipe->ingredientes()->detach();
        $recipe->delete();
        
        return redirect()->route('recipes.index')
                        ->with('success', 'Receta eliminada exitosamente.');
    }
}
