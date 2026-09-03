<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PasswordResetController extends Controller
{
    public function create() { return view('auth.forgot-password'); }

    public function send(Request $request)
    {
        $data = $request->validate(['correo' => ['required', 'email']]);
        $user = User::where('correo', $data['correo'])->first();
        if ($user) {
            $token = Str::random(64);
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $user->correo],
                ['token' => Hash::make($token), 'created_at' => now()]
            );
            $link = route('password.reset', ['token' => $token, 'correo' => $user->correo]);
            Mail::raw("Hola {$user->nom_com},\n\nRestablece tu contraseña aquí (válido por 1 hora):\n{$link}", function ($message) use ($user) {
                $message->to($user->correo, $user->nom_com)->subject('Recuperación de cuenta - Tropical & Fresh');
            });
        }

        return back()->with('success', 'Si el correo está registrado, recibirás un enlace de recuperación.');
    }

    public function edit(Request $request)
    {
        abort_unless($this->validToken($request), 404);
        return view('auth.reset-password', ['token' => $request->token, 'correo' => $request->correo]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'token' => ['required', 'string'],
            'correo' => ['required', 'email'],
            'contrasena' => ['required', 'string', 'min:6', 'same:contrasena_confirmation'],
        ]);
        abort_unless($this->validToken($request), 404);
        User::where('correo', $data['correo'])->update(['usu_con' => Hash::make($data['contrasena'])]);
        DB::table('password_reset_tokens')->where('email', $data['correo'])->delete();
        return redirect()->route('login')->with('success', 'Contraseña actualizada correctamente.');
    }

    private function validToken(Request $request): bool
    {
        $record = DB::table('password_reset_tokens')->where('email', $request->correo)->first();
        return $record && $record->created_at && now()->diffInMinutes($record->created_at) <= 60
            && Hash::check($request->token, $record->token);
    }
}