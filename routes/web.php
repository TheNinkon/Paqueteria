<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

// Redirección por defecto
Route::get('/', fn () => redirect()->route('login'));

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::get('/repartidor/login', [LoginController::class, 'showRiderLoginForm'])->name('repartidor.login');
Route::post('/repartidor/login', [LoginController::class, 'loginRider']);
Route::post('/repartidor/logout', [LogoutController::class, 'logoutRider'])->name('repartidor.logout');

// Rutas protegidas
Route::middleware(['auth:web'])->group(function () {
    Route::get('/not-authorized', fn () => view('pages.misc-not-authorized'))->name('not-authorized');
});

// Incluir los archivos de rutas separados
require __DIR__ . '/admin.php';
require __DIR__ . '/gerente.php';
require __DIR__ . '/proveedor.php';
require __DIR__ . '/cliente.php';
require __DIR__ . '/repartidor.php';
