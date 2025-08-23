<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AccountController;
use App\Http\Controllers\Admin\AssignmentController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Rider\DashboardController as RiderDashboardController;
use App\Http\Controllers\Rider\ProfileController as RiderProfileController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Customer\PackageController as CustomerPackageController;

// Autenticación de Administrador, Proveedor y Cliente Corporativo (Guard 'web')
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Rutas protegidas para Administrador, Proveedor y Cliente Corporativo
Route::middleware(['auth:web'])->group(function () {

    // Panel de Administrador y gestión general
    Route::middleware(['role:Administrador'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::resource('empleados', EmployeeController::class); // Repartidores creados por admin
        Route::resource('proveedores', VendorController::class); // Proveedores
        Route::resource('paquetes', PackageController::class); // Gestión completa de paquetes
        Route::get('paquetes/{package}/historial', [PackageController::class, 'history'])->name('packages.history');
        Route::get('reportes', [AdminDashboardController::class, 'reports'])->name('admin.reports');
    });

    // Rutas protegidas para Proveedores
    Route::middleware(['role:Proveedor'])->prefix('proveedor')->name('proveedor.')->group(function () {
        Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');
        Route::resource('repartidores', EmployeeController::class)->except(['destroy']); // CRUD de repartidores para el proveedor
        Route::get('paquetes', [VendorPackageController::class, 'index'])->name('packages.index');
        Route::get('reportes', [VendorReportController::class, 'index'])->name('reports.index');
    });

    // Rutas protegidas para Clientes Corporativos
    Route::middleware(['role:Cliente_Corporativo'])->prefix('cliente')->name('cliente.')->group(function () {
        Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');
        Route::get('paquetes', [CustomerPackageController::class, 'index'])->name('packages.index');
        Route::get('reportes', [CustomerPackageController::class, 'reports'])->name('reports.index');
    });

});

// Autenticación de Repartidor (Guard 'repartidor')
Route::get('/repartidor/login', [LoginController::class, 'showRiderLoginForm'])->name('repartidor.login');
Route::post('/repartidor/login', [LoginController::class, 'loginRider']);
Route::post('/repartidor/logout', [LogoutController::class, 'logoutRider'])->name('repartidor.logout');

// Rutas protegidas para Repartidores
Route::middleware(['auth:repartidor'])->prefix('repartidor')->name('repartidor.')->group(function () {
    Route::get('/dashboard', [RiderDashboardController::class, 'index'])->name('dashboard');
    Route::get('/perfil', [RiderProfileController::class, 'show'])->name('profile');
    Route::get('/paquetes', [RiderPackageController::class, 'index'])->name('packages.index');
    Route::get('/escanear', [RiderPackageController::class, 'scan'])->name('packages.scan');
    Route::post('/paquetes/{package}/actualizar-estado', [RiderPackageController::class, 'updateStatus'])->name('packages.updateStatus');
});

// Rutas públicas (por ejemplo, página de inicio, errores)
Route::get('/', function () {
    return view('welcome');
});

// Ruta para la página de "no autorizado"
Route::get('/not-authorized', function () {
    return view('pages.misc-not-authorized');
})->name('not-authorized');
