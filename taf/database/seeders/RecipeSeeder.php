<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Recipe;
use App\Models\Ingredient;

class RecipeSeeder extends Seeder
{
    public function run(): void
    {
        $recipes = [
            [
                'nombre_receta' => 'Jugo de Fresa Fresco',
                'descripcion' => 'Jugo natural de fresa con hielo, perfecto para refrescarse',
                'precio_base' => 8000,
                'personalizable' => true,
                'ingredientes' => [
                    1 => 250,  // Fresa: 250g
                    7 => 0.25, // Agua: 250ml
                    10 => 50,  // Hielo: 50g
                ]
            ],
            [
                'nombre_receta' => 'Batido de Mango Tropical',
                'descripcion' => 'Delicioso batido con mango, hielo y un toque de miel',
                'precio_base' => 9000,
                'personalizable' => true,
                'ingredientes' => [
                    2 => 200,  // Mango: 200g
                    7 => 0.2,  // Agua: 200ml
                    10 => 50,  // Hielo: 50g
                    9 => 0.05, // Miel: 50ml
                ]
            ],
            [
                'nombre_receta' => 'Jugo Detox Verde',
                'descripcion' => 'Zanahoria, remolacha y manzana - revitalizante',
                'precio_base' => 10000,
                'personalizable' => true,
                'ingredientes' => [
                    5 => 150,  // Zanahoria: 150g
                    6 => 100,  // Remolacha: 100g
                    7 => 0.15, // Agua: 150ml
                ]
            ],
            [
                'nombre_receta' => 'Piña Colada Tropical',
                'descripcion' => 'Refrescante mezcla de piña con un toque especial',
                'precio_base' => 11000,
                'personalizable' => false,
                'ingredientes' => [
                    3 => 250,  // Piña: 250g
                    7 => 0.25, // Agua: 250ml
                    10 => 60,  // Hielo: 60g
                ]
            ],
            [
                'nombre_receta' => 'Smoothie de Plátano y Fresa',
                'descripcion' => 'Cremoso y delicioso para un desayuno nutritivo',
                'precio_base' => 9500,
                'personalizable' => true,
                'ingredientes' => [
                    4 => 150,  // Plátano: 150g
                    1 => 100,  // Fresa: 100g
                    7 => 0.1,  // Agua: 100ml
                    10 => 40,  // Hielo: 40g
                ]
            ],
            [
                'nombre_receta' => 'Jugo Naranja Natural',
                'descripcion' => 'Jugo fresco de naranja recién exprimido',
                'precio_base' => 7500,
                'personalizable' => false,
                'ingredientes' => [
                    14 => 300, // Naranja: 300g
                    10 => 30,  // Hielo: 30g
                ]
            ],
        ];

        $adminUser = \App\Models\User::where('id_rol', 1)->first();
        $adminId = $adminUser ? $adminUser->id_usu : 1;

        foreach ($recipes as $recipe) {
            $ingredientes = $recipe['ingredientes'];
            unset($recipe['ingredientes']);

            $newRecipe = Recipe::create([
                ...$recipe,
                'estado' => 'activo',
                'id_usu_creador' => $adminId,
                'fecha_creacion' => now(),
                'fecha_actualizacion' => now(),
            ]);

            // Adjuntar ingredientes
            foreach ($ingredientes as $ingId => $cantidad) {
                $ing = Ingredient::find($ingId);
                if ($ing) {
                    $newRecipe->ingredientes()->attach($ingId, [
                        'cantidad_requerida' => $cantidad,
                        'unidad_medida' => $ing->unidad_medida,
                    ]);
                }
            }
        }
    }
}
