<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Http\Kernel::class);

// Simulate login request
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

$symRequest = SymfonyRequest::create(
    '/login',
    'POST',
    [
        'correo' => 'admin@tropical.com',
        'contrasena' => 'password123'
    ]
);

$data = ['correo' => 'admin@tropical.com', 'contrasena' => 'password123'];

echo "========= SIMULATED LOGIN TEST =========\n";
echo "Email: " . $data['correo'] . "\n";
echo "Password: " . $data['contrasena'] . "\n\n";

$user = \App\Models\User::where('correo', $data['correo'])->first();
echo "User found: " . ($user ? 'yes' : 'no') . "\n";

if ($user) {
    echo "User name: " . $user->nom_com . "\n";
    echo "User state: " . $user->estado . "\n";
    echo "User id_rol: " . $user->id_rol . "\n";
    echo "User role_name: " . $user->role_name . "\n\n";
    
    $passwordValid = \Illuminate\Support\Facades\Hash::check($data['contrasena'], $user->usu_con);
    echo "Password check: " . ($passwordValid ? 'valid' : 'invalid') . "\n\n";
    
    // Simulate role checks
    echo "Role checks:\n";
    echo "  hasRole('admin', 'inventario', 'gerente'): " . ($user->hasRole('admin', 'inventario', 'gerente') ? 'true (-> admin.dashboard)' : 'false') . "\n";
    echo "  hasRole('cajero'): " . ($user->hasRole('cajero') ? 'true (-> pos.index)' : 'false') . "\n";
    echo "  hasRole('trabajador'): " . ($user->hasRole('trabajador') ? 'true (-> recipes.index)' : 'false') . "\n";
    echo "  else: home\n";
}
