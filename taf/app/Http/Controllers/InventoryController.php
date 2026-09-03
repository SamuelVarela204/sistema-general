<?php

namespace App\Http\Controllers;

use App\Models\Ingredient;
use App\Models\InventoryMovement;
use App\Models\StockAlert;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!$request->user()?->hasRole('admin', 'gerente', 'inventario')) {
                abort(403, 'No tienes permiso para gestionar inventario.');
            }
            return $next($request);
        })->except(['index', 'show']);
    }

    public function index(): View
    {
        $ingredients = Ingredient::with('alertas')
            ->orderBy('nombre_ingrediente')
            ->paginate(20);
        
        $alertas = StockAlert::where('estado_alerta', 'activa')
            ->with('ingrediente')
            ->get();

        return view('inventory.index', compact('ingredients', 'alertas'));
    }

    public function show(Ingredient $ingredient): View
    {
        $ingredient->load('movimientos', 'alertas');
        $movimientos = $ingredient->movimientos()
            ->with('responsable')
            ->orderBy('fecha_movimiento', 'desc')
            ->paginate(20);
        
        return view('inventory.show', compact('ingredient', 'movimientos'));
    }

    public function create(): View
    {
        return view('inventory.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_ingrediente' => 'required|string|unique:ingredientes|max:225',
            'tipo' => 'required|string|max:50',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:20',
            'costo_unitario' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:1',
        ]);

        Ingredient::create($validated + ['estado' => 'activo', 'stock_actual' => 0]);

        return redirect()->route('inventory.index')
                        ->with('success', 'Ingrediente registrado exitosamente.');
    }

    public function edit(Ingredient $ingredient): View
    {
        return view('inventory.edit', compact('ingredient'));
    }

    public function update(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $validated = $request->validate([
            'nombre_ingrediente' => 'required|string|unique:ingredientes,nombre_ingrediente,' . $ingredient->id_ingrediente . ',id_ingrediente|max:225',
            'tipo' => 'required|string|max:50',
            'descripcion' => 'nullable|string',
            'unidad_medida' => 'required|string|max:20',
            'costo_unitario' => 'required|numeric|min:0',
            'stock_minimo' => 'required|numeric|min:1',
            'estado' => 'required|in:activo,inactivo',
        ]);

        $ingredient->update($validated);

        return redirect()->route('inventory.show', $ingredient->id_ingrediente)
                        ->with('success', 'Ingrediente actualizado exitosamente.');
    }

    public function recordMovement(Request $request, Ingredient $ingredient): RedirectResponse
    {
        $validated = $request->validate([
            'tipo_movimiento' => 'required|in:entrada,salida,ajuste,merma',
            'cantidad' => 'required|numeric|min:1',
            'descripcion' => 'nullable|string|max:225',
        ]);

        // Registrar movimiento
        InventoryMovement::create([
            'id_ingrediente' => $ingredient->id_ingrediente,
            'tipo_movimiento' => $validated['tipo_movimiento'],
            'cantidad' => $validated['cantidad'],
            'descripcion' => $validated['descripcion'] ?? null,
            'id_usu_responsable' => auth()->id(),
            'fecha_movimiento' => now(),
        ]);

        // Actualizar stock
        if (in_array($validated['tipo_movimiento'], ['entrada', 'ajuste'])) {
            $ingredient->increment('stock_actual', $validated['cantidad']);
        } else {
            $ingredient->decrement('stock_actual', $validated['cantidad']);
        }

        // Verificar alerta de stock bajo
        if ($ingredient->stock_actual <= $ingredient->stock_minimo) {
            StockAlert::firstOrCreate([
                'id_ingrediente' => $ingredient->id_ingrediente,
                'estado_alerta' => 'activa',
            ], [
                'stock_actual' => $ingredient->stock_actual,
                'stock_minimo' => $ingredient->stock_minimo,
                'fecha_alerta' => now(),
            ]);
        }

        return back()->with('success', 'Movimiento registrado exitosamente.');
    }

    public function resolveAlert(StockAlert $alert): RedirectResponse
    {
        $alert->update([
            'estado_alerta' => 'resuelta',
            'fecha_resolucion' => now(),
        ]);

        return back()->with('success', 'Alerta resuelta.');
    }
}
