<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function show() { return view('profile'); }

    public function update(Request $request)
    {
        $data = $request->validate(['nombre' => ['required', 'string', 'max:225'], 'telefono' => ['nullable', 'string', 'max:15'], 'direccion' => ['nullable', 'string', 'max:225'], 'alergias' => ['nullable', 'string', 'max:225'], 'descripcion' => ['nullable', 'string', 'max:225'], 'imagen' => ['nullable', 'image', 'max:4096']]);
        $user = $request->user();
        $user->fill(['nom_com' => $data['nombre'], 'telefono' => $data['telefono'] ?? null, 'direccion' => $data['direccion'] ?? null, 'alergias' => $data['alergias'] ?? null, 'descripcion' => $data['descripcion'] ?? null]);
        if ($request->hasFile('imagen')) $user->imagen = file_get_contents($request->file('imagen')->getRealPath());
        $user->save();
        return back()->with('success', 'Perfil actualizado correctamente.');
    }

    public function destroy(Request $request)
    {
        $user = $request->user();
        Auth::logout();
        $user->delete();
        $request->session()->invalidate();
        return redirect()->route('home')->with('success', 'Perfil eliminado.');
    }
}
