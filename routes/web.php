<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Proveedor\DashboardController as VendorDashboardController;
use App\Http\Controllers\Repartidor\DashboardController as RiderDashboardController;
use App\Http\Controllers\Cliente\DashboardController as CustomerDashboardController;

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Redirección por defecto
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas protegidas por el guard 'web' para el panel de administración
Route::middleware(['auth:web'])->group(function () {

    // Rutas del Administrador (con middleware de rol)
    Route::middleware(['role:Administrador'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

        // Rutas para la gestión de usuarios, paquetes, etc.
        Route::resource('empleados', \App\Http\Controllers\Admin\EmployeeController::class);
        Route::resource('proveedores', \App\Http\Controllers\Admin\VendorController::class);
        Route::resource('paquetes', \App\Http\Controllers\Admin\PackageController::class);
        Route::get('paquetes/{package}/historial', [\App\Http\Controllers\Admin\PackageController::class, 'history'])->name('packages.history');
        Route::get('reportes', [AdminDashboardController::class, 'reports'])->name('reports');
    });

    // Rutas del Proveedor (con middleware de rol)
    Route::middleware(['role:Proveedor'])->prefix('proveedor')->name('proveedor.')->group(function () {
        Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');

        // Rutas para la gestión de repartidores y paquetes
        Route::resource('repartidores', \App\Http\Controllers\Proveedor\EmployeeController::class)->except(['destroy']);
        Route::get('paquetes', [\App\Http\Controllers\Proveedor\PackageController::class, 'index'])->name('packages.index');
        Route::get('reportes', [\App\Http\Controllers\Proveedor\ReportController::class, 'index'])->name('reports.index');
    });

    // Rutas del Cliente Corporativo (con middleware de rol de solo lectura)
    Route::middleware(['role:Cliente_Corporativo'])->prefix('cliente')->name('cliente.')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

        // Rutas de solo lectura
        Route::get('paquetes', [\App\Http\Controllers\Customer\PackageController::class, 'index'])->name('packages.index');
        Route::get('reportes', [\App\Http\Controllers\Customer\PackageController::class, 'reports'])->name('reports.index');
    });
});

// Rutas de autenticación para el repartidor (guard 'rider')
Route::get('/repartidor/login', [LoginController::class, 'showRiderLoginForm'])->name('repartidor.login');
Route::post('/repartidor/login', [LoginController::class, 'loginRider']);
Route::post('/repartidor/logout', [LogoutController::class, 'logoutRider'])->name('repartidor.logout');

// Rutas protegidas por el guard 'repartidor'
Route::middleware(['auth:repartidor'])->prefix('repartidor')->name('repartidor.')->group(function () {
    Route::get('/dashboard', [RiderDashboardController::class, 'index'])->name('dashboard');
    // Rutas para el perfil y paquetes del repartidor
    Route::get('/perfil', [\App\Http\Controllers\Rider\ProfileController::class, 'show'])->name('profile');
    Route::get('/paquetes', [\App\Http\Controllers\Rider\PackageController::class, 'index'])->name('packages.index');
    Route::get('/escanear', [\App\Http\Controllers\Rider\PackageController::class, 'scan'])->name('packages.scan');
    Route::post('/paquetes/{package}/actualizar-estado', [\App\Http\Controllers\Rider\PackageController::class, 'updateStatus'])->name('packages.updateStatus');
});

// Ruta de acceso no autorizado
Route::get('/not-authorized', function () {
    return view('pages.misc-not-authorized');
})->name('not-authorized');
