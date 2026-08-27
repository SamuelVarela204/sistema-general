<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->filled('q')) $query->where(fn ($q) => $q->where('nom_pro', 'like', '%'.$request->q.'%')->orWhere('descripcion', 'like', '%'.$request->q.'%'));
        return view('home', ['products' => $query->limit(20)->get(), 'search' => $request->q]);
    }
}
