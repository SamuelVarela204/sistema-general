<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Response;

class UserAreaController extends Controller
{
    public function orders()
    {
        return view('orders', ['orders' => Order::where('id_usu', auth()->id())->latest('id_ped')->paginate(10)]);
    }

    public function recipes()
    {
        return view('recipes');
    }

    public function settings()
    {
        return view('settings');
    }

    public function image(): Response
    {
        $user = auth()->user();
        abort_unless($user && $user->imagen, 404);
        return response($user->imagen)->header('Content-Type', 'image/jpeg')->header('Cache-Control', 'private, max-age=3600');
    }
}
