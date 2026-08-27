<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SiscopController;
use App\Http\Controllers\UserAreaController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/siscop', SiscopController::class)->name('siscop');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile');
    Route::get('/perfil/imagen', [UserAreaController::class, 'image'])->name('profile.image');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/pedidos', [UserAreaController::class, 'orders'])->name('orders');
    Route::get('/recetas', [UserAreaController::class, 'recipes'])->name('recipes');
    Route::get('/ajustes', [UserAreaController::class, 'settings'])->name('settings');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/productos', [AdminController::class, 'products'])->name('products');
    Route::post('/productos', [AdminController::class, 'storeProduct'])->name('products.store');
    Route::put('/productos/{product}', [AdminController::class, 'updateProduct'])->name('products.update');
    Route::delete('/productos/{product}', [AdminController::class, 'destroyProduct'])->name('products.destroy');
    Route::get('/usuarios', [AdminController::class, 'users'])->name('users');
    Route::post('/usuarios', [AdminController::class, 'storeUser'])->name('users.store');
    Route::put('/usuarios/{user}', [AdminController::class, 'updateUser'])->name('users.update');
    Route::delete('/usuarios/{user}', [AdminController::class, 'destroyUser'])->name('users.destroy');
    Route::get('/pedidos', [AdminController::class, 'orders'])->name('orders');
    Route::put('/pedidos/{order}', [AdminController::class, 'updateOrder'])->name('orders.update');
    Route::delete('/pedidos/{order}', [AdminController::class, 'destroyOrder'])->name('orders.destroy');
});
