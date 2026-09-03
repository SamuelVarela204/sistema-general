<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;
use App\Models\GlobalSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard', ['products' => Product::count(), 'users' => User::count(), 'orders' => Order::count()]);
    }

    public function products() { return view('admin.products', ['products' => Product::latest('id_pro')->paginate(15)]); }
    public function storeProduct(Request $request)
    {
        Product::create($request->validate(['nom_pro' => 'required|string|max:225', 'descripcion' => 'nullable|string|max:100', 'precio' => 'required|numeric|min:0', 'stock' => 'required|integer|min:0', 'categoria' => 'required|string|max:100']));
        return back()->with('success', 'Producto creado.');
    }
    public function updateProduct(Request $request, Product $product)
    {
        $product->update($request->validate(['nom_pro' => 'required|string|max:225', 'descripcion' => 'nullable|string|max:100', 'precio' => 'required|numeric|min:0', 'stock' => 'required|integer|min:0', 'categoria' => 'required|string|max:100']));
        return back()->with('success', 'Producto actualizado.');
    }
    public function destroyProduct(Product $product) { $product->delete(); return back()->with('success', 'Producto eliminado.'); }

    public function users() { return view('admin.users', ['users' => User::with('role')->latest('id_usu')->paginate(15), 'roles' => Role::orderBy('id_rol')->get()]); }
    public function storeUser(Request $request)
    {
        $data = $request->validate(['nom_com' => 'required|string|max:225', 'correo' => 'required|email|unique:usuarios,correo', 'contrasena' => 'required|string|min:6', 'id_rol' => 'required|exists:roles,id_rol', 'estado' => 'required|in:activo,inactivo']);
        User::create(['nom_com' => $data['nom_com'], 'correo' => $data['correo'], 'usu_con' => Hash::make($data['contrasena']), 'id_rol' => $data['id_rol'], 'estado' => $data['estado']]);
        return back()->with('success', 'Usuario creado.');
    }
    public function updateUser(Request $request, User $user)
    {
        $data = $request->validate(['nom_com' => 'required|string|max:225', 'correo' => 'required|email|unique:usuarios,correo,'.$user->id_usu.',id_usu', 'id_rol' => 'required|exists:roles,id_rol', 'estado' => 'required|in:activo,inactivo']);
        $user->update($data);
        return back()->with('success', 'Usuario actualizado.');
    }
    public function destroyUser(User $user) { abort_if($user->id_usu === Auth::id(), 422, 'No puedes eliminar tu propia cuenta.'); $user->delete(); return back()->with('success', 'Usuario eliminado.'); }

    public function orders() { return view('admin.orders', ['orders' => Order::with('user')->latest('id_ped')->paginate(15)]); }
    public function updateOrder(Request $request, Order $order) { $order->update($request->validate(['estado' => 'required|in:pendiente,preparando,enviado,entregado,cancelado', 'total' => 'required|numeric|min:0'])); return back()->with('success', 'Pedido actualizado.'); }
    public function destroyOrder(Order $order) { $order->delete(); return back()->with('success', 'Pedido eliminado.'); }

    public function background() { return view('admin.background', ['setting' => GlobalSetting::find(1)]); }

    public function updateBackground(Request $request)
    {
        $request->validate(['fondo' => ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:8192']]);
        GlobalSetting::updateOrCreate(['id' => 1], [
            'glob_wall' => file_get_contents($request->file('fondo')->getRealPath()),
            'glob_mime' => $request->file('fondo')->getMimeType(),
        ]);
        return back()->with('success', 'Fondo global actualizado.');
    }

    public function destroyBackground() { GlobalSetting::where('id', 1)->update(['glob_wall' => null, 'glob_mime' => null]); return back()->with('success', 'Fondo global eliminado.'); }

    public function backgroundImage(): Response
    {
        $setting = GlobalSetting::find(1);
        abort_unless($setting && $setting->glob_wall, 404);
        return response($setting->glob_wall)->header('Content-Type', $setting->glob_mime ?: 'image/jpeg')->header('Cache-Control', 'public, max-age=3600');
    }
}
