<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\Sale;
use App\Models\SaleDetail;
use App\Models\Ingredient;
use App\Models\InventoryMovement;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;

class PosController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!$request->user()?->hasRole('admin', 'gerente', 'cajero')) {
                abort(403, 'No tienes permiso para usar POS.');
            }
            return $next($request);
        });
    }

    public function index(): View
    {
        $recipes = Recipe::where('estado', 'activo')
            ->with('ingredientes')
            ->get();
        
        return view('pos.index', compact('recipes'));
    }

    public function getRecipeDetails(Recipe $recipe): JsonResponse
    {
        $recipe->load('ingredientes');
        return response()->json($recipe);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id_receta' => 'required|exists:recetas,id_receta',
            'items.*.cantidad' => 'required|numeric|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
            'items.*.personalizacion' => 'nullable|string',
            'descuento' => 'nullable|numeric|min:0',
            'metodo_pago' => 'required|in:efectivo,tarjeta,transferencia',
        ]);

        // Crear venta
        $total = 0;
        foreach ($validated['items'] as $item) {
            $subtotal = $item['cantidad'] * $item['precio_unitario'];
            $total += $subtotal;
        }
        
        $total -= $validated['descuento'] ?? 0;

        $sale = Sale::create([
            'id_usu_cajero' => auth()->id(),
            'total' => max(0, $total),
            'descuento' => $validated['descuento'] ?? 0,
            'metodo_pago' => $validated['metodo_pago'],
            'estado' => 'completada',
            'fecha_venta' => now(),
        ]);

        // Crear detalles de venta y descontar inventario
        foreach ($validated['items'] as $item) {
            $recipe = Recipe::findOrFail($item['id_receta']);
            $subtotal = $item['cantidad'] * $item['precio_unitario'];

            // Crear detalle de venta
            SaleDetail::create([
                'id_venta' => $sale->id_venta,
                'id_receta' => $recipe->id_receta,
                'cantidad' => $item['cantidad'],
                'precio_unitario' => $item['precio_unitario'],
                'subtotal' => $subtotal,
                'personalizacion' => $item['personalizacion'] ?? null,
            ]);

            // Descontar ingredientes del inventario (RN-09)
            foreach ($recipe->ingredientes as $ingrediente) {
                $cantidadDescontar = $ingrediente->pivot->cantidad_requerida * $item['cantidad'];
                
                // Registrar movimiento
                InventoryMovement::create([
                    'id_ingrediente' => $ingrediente->id_ingrediente,
                    'tipo_movimiento' => 'salida',
                    'cantidad' => $cantidadDescontar,
                    'descripcion' => "Venta: {$recipe->nombre_receta} (x{$item['cantidad']})",
                    'id_usu_responsable' => auth()->id(),
                    'fecha_movimiento' => now(),
                ]);

                // Actualizar stock
                $ingrediente->decrement('stock_actual', $cantidadDescontar);
            }
        }

        return redirect()->route('pos.receipt', $sale->id_venta)
                        ->with('success', 'Venta registrada exitosamente.');
    }

    public function receipt(Sale $sale): View
    {
        $sale->load('detalles.receta', 'cajero');
        return view('pos.receipt', compact('sale'));
    }

    public function history(): View
    {
        $sales = Sale::with('detalles.receta', 'cajero')
            ->orderBy('fecha_venta', 'desc')
            ->paginate(20);
        
        return view('pos.history', compact('sales'));
    }
}
