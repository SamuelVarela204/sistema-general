<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id_rol' => 2,
            'nom_com' => fake()->name(),
            'correo' => fake()->unique()->safeEmail(),
            'usu_con' => static::$password ??= Hash::make('password123'),
            'telefono' => fake()->numerify('3#########'),
            'direccion' => fake()->address(),
            'descripcion' => 'Usuario de prueba',
            'estado' => 'activo',
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
