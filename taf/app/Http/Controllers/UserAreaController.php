<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class UserAreaController extends Controller
{
    public function orders()
    {
        return view('orders', ['orders' => Order::where('id_usu', Auth::id())->latest('id_ped')->paginate(10)]);
    }

    public function recipes()
    {
        return view('recipes');
    }

    public function settings()
    {
        return view('settings', ['notificationsEnabled' => (bool) Auth::user()->notificaciones]);
    }

    public function updateSettings(Request $request)
    {
        $request->user()->update(['notificaciones' => $request->boolean('notificaciones')]);
        return back()->with('success', 'Preferencias actualizadas.');
    }

    public function image(): Response
    {
        $user = Auth::user();
        abort_unless($user && $user->imagen, 404);
        return response($user->imagen)->header('Content-Type', 'image/jpeg')->header('Cache-Control', 'private, max-age=3600');
    }
}
