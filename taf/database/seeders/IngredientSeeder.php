<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ingredient;

class IngredientSeeder extends Seeder
{
    public function run(): void
    {
        $ingredients = [
            ['nombre_ingrediente' => 'Fresa', 'tipo' => 'fruta', 'unidad_medida' => 'kg', 'costo_unitario' => 8500, 'stock_minimo' => 5],
            ['nombre_ingrediente' => 'Mango', 'tipo' => 'fruta', 'unidad_medida' => 'kg', 'costo_unitario' => 7000, 'stock_minimo' => 5],
            ['nombre_ingrediente' => 'Piña', 'tipo' => 'fruta', 'unidad_medida' => 'kg', 'costo_unitario' => 6000, 'stock_minimo' => 5],
            ['nombre_ingrediente' => 'Plátano', 'tipo' => 'fruta', 'unidad_medida' => 'kg', 'costo_unitario' => 4000, 'stock_minimo' => 5],
            ['nombre_ingrediente' => 'Zanahoria', 'tipo' => 'verdura', 'unidad_medida' => 'kg', 'costo_unitario' => 3000, 'stock_minimo' => 3],
            ['nombre_ingrediente' => 'Remolacha', 'tipo' => 'verdura', 'unidad_medida' => 'kg', 'costo_unitario' => 4500, 'stock_minimo' => 3],
            ['nombre_ingrediente' => 'Agua', 'tipo' => 'bebida', 'unidad_medida' => 'l', 'costo_unitario' => 500, 'stock_minimo' => 50],
            ['nombre_ingrediente' => 'Azúcar', 'tipo' => 'endulzante', 'unidad_medida' => 'kg', 'costo_unitario' => 3000, 'stock_minimo' => 5],
            ['nombre_ingrediente' => 'Miel', 'tipo' => 'endulzante', 'unidad_medida' => 'l', 'costo_unitario' => 15000, 'stock_minimo' => 2],
            ['nombre_ingrediente' => 'Hielo', 'tipo' => 'bebida', 'unidad_medida' => 'kg', 'costo_unitario' => 2000, 'stock_minimo' => 20],
            ['nombre_ingrediente' => 'Vasos 16oz', 'tipo' => 'empaques', 'unidad_medida' => 'unidad', 'costo_unitario' => 50, 'stock_minimo' => 500],
            ['nombre_ingrediente' => 'Pitillos', 'tipo' => 'empaques', 'unidad_medida' => 'paquete', 'costo_unitario' => 2000, 'stock_minimo' => 10],
            ['nombre_ingrediente' => 'Limón', 'tipo' => 'fruta', 'unidad_medida' => 'unidad', 'costo_unitario' => 300, 'stock_minimo' => 50],
            ['nombre_ingrediente' => 'Naranja', 'tipo' => 'fruta', 'unidad_medida' => 'kg', 'costo_unitario' => 6500, 'stock_minimo' => 5],
            ['nombre_ingrediente' => 'Melón', 'tipo' => 'fruta', 'unidad_medida' => 'unidad', 'costo_unitario' => 8000, 'stock_minimo' => 3],
        ];

        foreach ($ingredients as $ingredient) {
            Ingredient::create([
                ...$ingredient,
                'descripcion' => null,
                'estado' => 'activo',
                'stock_actual' => 20
            ]);
        }
    }
}
