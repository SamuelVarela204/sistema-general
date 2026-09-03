<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'nom_com' => 'Admin Principal',
                'correo' => 'admin@tropical.com',
                'telefono' => '3001234567',
                'id_rol' => 1, // admin
            ],
            [
                'nom_com' => 'Gerente Operativo',
                'correo' => 'gerente@tropical.com',
                'telefono' => '3012345678',
                'id_rol' => 4, // gerente
            ],
            [
                'nom_com' => 'Inventario Expert',
                'correo' => 'inventario@tropical.com',
                'telefono' => '3013456789',
                'id_rol' => 3, // inventario
            ],
            [
                'nom_com' => 'Cajero Principal',
                'correo' => 'cajero@tropical.com',
                'telefono' => '3014567890',
                'id_rol' => 5, // cajero
            ],
            [
                'nom_com' => 'Trabajador de Cocina',
                'correo' => 'trabajador@tropical.com',
                'telefono' => '3015678901',
                'id_rol' => 6, // trabajador
            ],
            [
                'nom_com' => 'Cliente Frecuente',
                'correo' => 'cliente@tropical.com',
                'telefono' => '3016789012',
                'id_rol' => 2, // cliente
            ],
        ];

        foreach ($users as $user) {
            User::create([
                ...$user,
                'usu_con' => bcrypt('password123'), // Contraseña: password123
                'estado' => 'activo',
            ]);
        }
    }
}
