<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;

// ===== Admin =====
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\AccountController;
// El controlador 'AssignmentController' no se ha creado aún, se mantiene para referencia.
use App\Http\Controllers\Admin\AssignmentController;
// El controlador 'AuditLogController' no se ha creado aún, se mantiene para referencia.
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\VendorController as AdminVendorController;
use App\Http\Controllers\Admin\PackageController as AdminPackageController;

// ===== Proveedor =====
use App\Http\Controllers\Proveedor\VendorDashboardController;
// Los siguientes controladores aún no existen, se mantienen para referencia
use App\Http\Controllers\Proveedor\PackageController as VendorPackageController;
use App\Http\Controllers\Proveedor\ReportController as VendorReportController;

// ===== Repartidor =====
use App\Http\Controllers\Repartidor\DashboardController as RiderDashboardController;
// Los siguientes controladores aún no existen, se mantienen para referencia
use App\Http\Controllers\Rider\ProfileController as RiderProfileController;
use App\Http\Controllers\Rider\PackageController as RiderPackageController;

// ===== Cliente =====
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
// Los siguientes controladores aún no existen, se mantienen para referencia
use App\Http\Controllers\Customer\PackageController as CustomerPackageController;

// -------------------- Autenticación Guard web --------------------
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

Route::middleware(['auth:web'])->group(function () {

    // ----- Admin -----
    Route::middleware(['role:Administrador'])->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');

        // Los siguientes resource controllers aún no existen
        // Route::resource('empleados', EmployeeController::class);
        // Route::resource('proveedores', AdminVendorController::class);
        // Route::resource('paquetes', AdminPackageController::class);
        // Route::get('paquetes/{package}/historial', [AdminPackageController::class, 'history'])->name('packages.history');
        // Route::get('reportes', [AdminDashboardController::class, 'reports'])->name('admin.reports');
    });

    // ----- Proveedor -----
    Route::middleware(['role:Proveedor'])
        ->prefix('proveedor')
        ->name('proveedor.')
        ->group(function () {
            Route::get('/dashboard', [VendorDashboardController::class, 'index'])->name('dashboard');

            // Los siguientes resource controllers aún no existen
            // Route::resource('repartidores', EmployeeController::class)->except(['destroy']);
            // Route::get('paquetes', [VendorPackageController::class, 'index'])->name('packages.index');
            // Route::get('reportes', [VendorReportController::class, 'index'])->name('reports.index');
        });

    // ----- Cliente Corporativo -----
    Route::middleware(['role:Cliente_Corporativo'])
        ->prefix('cliente')
        ->name('cliente.')
        ->group(function () {
            Route::get('/dashboard', [CustomerDashboardController::class, 'index'])->name('dashboard');

            // Los siguientes controladores aún no existen
            // Route::get('paquetes', [CustomerPackageController::class, 'index'])->name('packages.index');
            // Route::get('reportes', [CustomerPackageController::class, 'reports'])->name('reports.index');
        });
});

// -------------------- Guard repartidor --------------------
Route::get('/repartidor/login', [LoginController::class, 'showRiderLoginForm'])->name('repartidor.login');
Route::post('/repartidor/login', [LoginController::class, 'loginRider']);
Route::post('/repartidor/logout', [LogoutController::class, 'logoutRider'])->name('repartidor.logout');

Route::middleware(['auth:repartidor'])
    ->prefix('repartidor')
    ->name('repartidor.')
    ->group(function () {
        Route::get('/dashboard', [RiderDashboardController::class, 'index'])->name('dashboard');

        // Los siguientes controladores aún no existen
        // Route::get('/perfil', [RiderProfileController::class, 'show'])->name('profile');
        // Route::get('/paquetes', [RiderPackageController::class, 'index'])->name('packages.index');
        // Route::get('/escanear', [RiderPackageController::class, 'scan'])->name('packages.scan');
        // Route::post('/paquetes/{package}/actualizar-estado', [RiderPackageController::class, 'updateStatus'])->name('packages.updateStatus');
    });

// -------------------- Públicas --------------------
Route::get('/', fn () => redirect()->route('login'));
Route::get('/not-authorized', fn () => view('pages.misc-not-authorized'))->name('not-authorized');c
