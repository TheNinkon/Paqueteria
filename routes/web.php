<?php
// File: routes/web.php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

// Dashboards (alias para evitar colisiones de nombre)
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Proveedor\DashboardController as ProveedorDashboardController;
use App\Http\Controllers\Repartidor\DashboardController as RepartidorDashboardController;
use App\Http\Controllers\Cliente\DashboardController as ClienteDashboardController; // Ojo: "Cliente", no "Customer"
use App\Http\Controllers\Gerente\DashboardController as GerenteDashboardController;

// Users (Admin)
use App\Http\Controllers\Admin\UserController as AdminUserController;

// Controlador de Paquetes para Gerente
use App\Http\Controllers\Gerente\Package\PackageController as GerentePackageController;

// Rutas de autenticación
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

// Redirección por defecto
Route::get('/', fn () => redirect()->route('login'));

// Rutas protegidas por 'web'
Route::middleware(['auth:web'])->group(function () {
    // Admin
    Route::middleware(['role:Administrador'])
        ->prefix('admin')->name('admin.')
        ->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

            // CRUD de Usuarios
            Route::resource('users', AdminUserController::class);

            // CRUD de Repartidores (NUEVO)
            Route::resource('riders', \App\Http\Controllers\Admin\RiderController::class);

            Route::resource('empleados', \App\Http\Controllers\Admin\EmployeeController::class);
            Route::resource('proveedores', \App\Http\Controllers\Admin\VendorController::class);
            Route::resource('paquetes', \App\Http\Controllers\Admin\PackageController::class);
            Route::get('paquetes/{package}/historial', [\App\Http\Controllers\Admin\PackageController::class, 'history'])->name('packages.history');
            Route::get('reportes', [AdminDashboardController::class, 'reports'])->name('reports');
        });

    // Proveedor
    Route::middleware(['role:Proveedor'])
        ->prefix('proveedor')->name('proveedor.')
        ->group(function () {
            Route::get('/dashboard', [ProveedorDashboardController::class, 'index'])->name('dashboard');
            Route::resource('repartidores', \App\Http\Controllers\Proveedor\EmployeeController::class)->except(['destroy']);
            Route::get('paquetes', [\App\Http\Controllers\Proveedor\PackageController::class, 'index'])->name('packages.index');
            Route::get('reportes', [\App\Http\Controllers\Proveedor\ReportController::class, 'index'])->name('reports.index');
        });

    // Cliente corporativo (consistencia: usa \Cliente\* en todo)
    Route::middleware(['role:Cliente_Corporativo'])
        ->prefix('cliente')->name('cliente.')
        ->group(function () {
            Route::get('/dashboard', [ClienteDashboardController::class, 'index'])->name('dashboard');
            Route::get('paquetes', [\App\Http\Controllers\Cliente\PackageController::class, 'index'])->name('packages.index');
            Route::get('reportes', [\App\Http\Controllers\Cliente\PackageController::class, 'reports'])->name('reports.index');
        });

    // API compartida para identificación de cliente (usable por varios roles del guard 'web')
    Route::post('/api/clients/identify', [GerentePackageController::class, 'identifyClient'])->name('api.clients.identify');
});

// Rutas protegidas por 'Gerente'
Route::middleware(['auth:web', 'role:Gerente'])
    ->prefix('gerente')
    ->name('gerente.')
    ->group(function () {
        Route::get('/dashboard', [GerenteDashboardController::class, 'index'])->name('dashboard');

        // Listado y creación
        Route::get('packages', [GerentePackageController::class, 'index'])->name('packages.index');
        Route::post('packages', [GerentePackageController::class, 'store'])->name('packages.store');

        // Asignación de paquetes
        Route::get('packages/assign', [GerentePackageController::class, 'assign'])->name('packages.assign');
        Route::post('packages/assign', [GerentePackageController::class, 'performAssignment'])->name('packages.performAssignment');
    });

// Auth del repartidor (guard repartidor)
Route::get('/repartidor/login', [LoginController::class, 'showRiderLoginForm'])->name('repartidor.login');
Route::post('/repartidor/login', [LoginController::class, 'loginRider']);
Route::post('/repartidor/logout', [LogoutController::class, 'logoutRider'])->name('repartidor.logout');

Route::middleware(['auth:repartidor'])
    ->prefix('repartidor')->name('repartidor.')
    ->group(function () {
        Route::get('/dashboard', [RepartidorDashboardController::class, 'index'])->name('dashboard');
        Route::get('/perfil', [\App\Http\Controllers\Repartidor\ProfileController::class, 'show'])->name('profile');
        Route::get('/paquetes', [\App\Http\Controllers\Repartidor\PackageController::class, 'index'])->name('packages.index');
        Route::get('/escanear', [\App\Http\Controllers\Repartidor\PackageController::class, 'scan'])->name('packages.scan');
        Route::post('/paquetes/{package}/actualizar-estado', [\App\Http\Controllers\Repartidor\PackageController::class, 'updateStatus'])->name('packages.updateStatus');
    });

// No autorizado
Route::get('/not-authorized', fn () => view('pages.misc-not-authorized'))->name('not-authorized');
