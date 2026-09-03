<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);
$request = \Illuminate\Http\Request::capture();
$kernel->handle($request);

// Test login logic
$user = \App\Models\User::find(1);
echo "========= ADMIN USER TEST =========\n";
echo "Usuario: " . $user->nom_com . "\n";
echo "Correo: " . $user->correo . "\n";
echo "ID Rol: " . $user->id_rol . "\n";
echo "Estado: " . $user->estado . "\n";
echo "\n--- Role Relationship ---\n";
echo "Has Role relationship loaded: " . ($user->relationLoaded('role') ? 'yes' : 'no') . "\n";
$roleModel = $user->role;
echo "Role from DB: " . ($roleModel ? $roleModel->nombre_rol : "NULL") . "\n";
echo "\n--- Role Name Attribute ---\n";
echo "role_name attribute: " . $user->role_name . "\n";
echo "\n--- hasRole Tests ---\n";
echo "hasRole('admin'): " . ($user->hasRole('admin') ? 'true' : 'false') . "\n";
echo "hasRole('admin', 'inventario', 'gerente'): " . ($user->hasRole('admin', 'inventario', 'gerente') ? 'true' : 'false') . "\n";
echo "hasRole('cajero'): " . ($user->hasRole('cajero') ? 'true' : 'false') . "\n";

echo "\n\n========= GERENTE USER TEST =========\n";
$user2 = \App\Models\User::find(2);
echo "Usuario: " . $user2->nom_com . "\n";
echo "ID Rol: " . $user2->id_rol . "\n";
echo "role_name: " . $user2->role_name . "\n";
echo "hasRole('gerente'): " . ($user2->hasRole('gerente') ? 'true' : 'false') . "\n";
echo "hasRole('admin', 'inventario', 'gerente'): " . ($user2->hasRole('admin', 'inventario', 'gerente') ? 'true' : 'false') . "\n";

echo "\n\n========= CAJERO USER TEST =========\n";
$user3 = \App\Models\User::find(4);
echo "Usuario: " . $user3->nom_com . "\n";
echo "ID Rol: " . $user3->id_rol . "\n";
echo "role_name: " . $user3->role_name . "\n";
echo "hasRole('cajero'): " . ($user3->hasRole('cajero') ? 'true' : 'false') . "\n";
echo "hasRole('trabajador'): " . ($user3->hasRole('trabajador') ? 'true' : 'false') . "\n";

echo "\n\n========= PASSWORD TEST =========\n";
$password = 'password123';
echo "Testing password for admin user...\n";
echo "Stored hash: " . substr($user->usu_con, 0, 20) . "...\n";
echo "Hash::check result: " . (\Illuminate\Support\Facades\Hash::check($password, $user->usu_con) ? 'true' : 'false') . "\n";
