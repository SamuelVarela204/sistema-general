<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\SiscopController;
use App\Http\Controllers\UserAreaController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/siscop', SiscopController::class)->name('siscop');
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/registro', [AuthController::class, 'showRegister'])->name('register');
Route::post('/registro', [AuthController::class, 'register'])->name('register.store');
Route::get('/recuperar-contrasena', [PasswordResetController::class, 'create'])->name('password.request');
Route::post('/recuperar-contrasena', [PasswordResetController::class, 'send'])->name('password.email');
Route::get('/restablecer-contrasena/{token}', [PasswordResetController::class, 'edit'])->name('password.reset');
Route::post('/restablecer-contrasena', [PasswordResetController::class, 'update'])->name('password.update');
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/perfil', [ProfileController::class, 'show'])->name('profile');
    Route::get('/perfil/imagen', [UserAreaController::class, 'image'])->name('profile.image');
    Route::put('/perfil', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/perfil', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/pedidos', [UserAreaController::class, 'orders'])->name('orders');
    Route::get('/recetas', [UserAreaController::class, 'recipes'])->name('recipes');
    Route::get('/ajustes', [UserAreaController::class, 'settings'])->name('settings');
    Route::put('/ajustes', [UserAreaController::class, 'updateSettings'])->name('settings.update');
});

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin,gerente,inventario'])->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/productos', [AdminController::class, 'products'])->name('products');
    Route::post('/productos', [AdminController::class, 'storeProduct'])->middleware('role:admin')->name('products.store');
    Route::put('/productos/{product}', [AdminController::class, 'updateProduct'])->middleware('role:admin,inventario')->name('products.update');
    Route::delete('/productos/{product}', [AdminController::class, 'destroyProduct'])->middleware('role:admin')->name('products.destroy');
    Route::get('/usuarios', [AdminController::class, 'users'])->middleware('role:admin,gerente')->name('users');
    Route::post('/usuarios', [AdminController::class, 'storeUser'])->middleware('role:admin')->name('users.store');
    Route::put('/usuarios/{user}', [AdminController::class, 'updateUser'])->middleware('role:admin')->name('users.update');
    Route::delete('/usuarios/{user}', [AdminController::class, 'destroyUser'])->middleware('role:admin')->name('users.destroy');
    Route::get('/pedidos', [AdminController::class, 'orders'])->name('orders');
    Route::put('/pedidos/{order}', [AdminController::class, 'updateOrder'])->middleware('role:admin,gerente,inventario')->name('orders.update');
    Route::delete('/pedidos/{order}', [AdminController::class, 'destroyOrder'])->middleware('role:admin')->name('orders.destroy');
    Route::get('/fondo', [AdminController::class, 'background'])->middleware('role:admin')->name('background');
    Route::post('/fondo', [AdminController::class, 'updateBackground'])->middleware('role:admin')->name('background.update');
    Route::delete('/fondo', [AdminController::class, 'destroyBackground'])->middleware('role:admin')->name('background.destroy');
});

// Rutas de Recetas (Admin y Gerente pueden crear/editar/eliminar)
Route::prefix('recetas')->name('recipes.')->middleware('auth')->group(function () {
    Route::get('/', [RecipeController::class, 'index'])->name('index');
    Route::get('/{recipe}', [RecipeController::class, 'show'])->name('show');
    Route::middleware('role:admin,gerente')->group(function () {
        Route::get('/crear', [RecipeController::class, 'create'])->name('create');
        Route::post('/', [RecipeController::class, 'store'])->name('store');
        Route::get('/{recipe}/editar', [RecipeController::class, 'edit'])->name('edit');
        Route::put('/{recipe}', [RecipeController::class, 'update'])->name('update');
        Route::delete('/{recipe}', [RecipeController::class, 'destroy'])->name('destroy');
    });
});

// Rutas de Inventario (Admin, Gerente e Inventario)
Route::prefix('inventario')->name('inventory.')->middleware(['auth', 'role:admin,gerente,inventario'])->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('index');
    Route::get('/{ingredient}', [InventoryController::class, 'show'])->name('show');
    Route::get('/crear', [InventoryController::class, 'create'])->name('create');
    Route::post('/', [InventoryController::class, 'store'])->name('store');
    Route::get('/{ingredient}/editar', [InventoryController::class, 'edit'])->name('edit');
    Route::put('/{ingredient}', [InventoryController::class, 'update'])->name('update');
    Route::post('/{ingredient}/movimiento', [InventoryController::class, 'recordMovement'])->name('movement.store');
    Route::put('/alerta/{alert}/resolver', [InventoryController::class, 'resolveAlert'])->name('alert.resolve');
});

// Rutas de POS/Facturación (Admin, Gerente y Cajero)
Route::prefix('pos')->name('pos.')->middleware(['auth', 'role:admin,gerente,cajero'])->group(function () {
    Route::get('/', [PosController::class, 'index'])->name('index');
    Route::get('/receta/{recipe}', [PosController::class, 'getRecipeDetails'])->name('recipe-details');
    Route::post('/venta', [PosController::class, 'store'])->name('store');
    Route::get('/recibo/{sale}', [PosController::class, 'receipt'])->name('receipt');
    Route::get('/historial', [PosController::class, 'history'])->name('history');
});

Route::get('/fondo-global', [AdminController::class, 'backgroundImage'])->name('background.image');
