<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin() { return view('auth.login'); }
    public function showRegister() { return view('auth.register'); }

    public function login(Request $request)
    {
        $data = $request->validate(['correo' => ['required', 'email'], 'contrasena' => ['required']]);
        $user = User::where('correo', $data['correo'])->first();
        abort_if($user && $user->estado !== 'activo', 403, 'La cuenta está inactiva.');
        $valid = $user && (Hash::check($data['contrasena'], $user->usu_con) || hash_equals((string) $user->usu_con, $data['contrasena']));
        if (!$valid) return back()->withErrors(['correo' => 'Correo o contraseña incorrectos.'])->withInput($request->only('correo'));
        if (!Hash::check($data['contrasena'], $user->usu_con)) {
            $user->usu_con = Hash::make($data['contrasena']);
            $user->save();
        }
        Auth::login($user, $request->boolean('recordar'));
        $request->session()->regenerate();

        if ($user->hasRole('admin', 'inventario', 'gerente')) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->hasRole('cajero')) {
            return redirect()->route('pos.index');
        }

        if ($user->hasRole('trabajador')) {
            return redirect()->route('recipes.index');
        }

        return redirect()->route('home');
    }

    public function register(Request $request)
    {
        $data = $request->validate(['nombre' => ['required', 'string', 'max:225'], 'correo' => ['required', 'email', 'unique:usuarios,correo'], 'contrasena' => ['required', 'string', 'min:6'], 'profile-pic' => ['nullable', 'image', 'max:4096']]);
        $role = Role::where('nombre_rol', 'cliente')->value('id_rol') ?? 2;
        $user = new User(['id_rol' => $role, 'nom_com' => $data['nombre'], 'correo' => $data['correo'], 'usu_con' => Hash::make($data['contrasena']), 'descripcion' => 'perfil sin descripcion']);
        if ($request->hasFile('profile-pic')) $user->imagen = file_get_contents($request->file('profile-pic')->getRealPath());
        $user->save();
        Auth::login($user);
        $request->session()->regenerate();
        return redirect()->route('home')->with('success', 'Cuenta creada correctamente.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home');
    }
}
