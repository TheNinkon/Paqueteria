<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\RiderController;
use App\Http\Controllers\Admin\EmployeeController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\PackageController;

Route::middleware(['auth:web', 'role:Administrador'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', AdminUserController::class);
    Route::resource('riders', RiderController::class);
    Route::resource('empleados', EmployeeController::class);
    Route::resource('proveedores', VendorController::class);
    Route::resource('paquetes', PackageController::class);

        Route::get('paquetes/{package}/historial', [PackageController::class, 'history'])->name('packages.history');
        Route::get('reportes', [AdminDashboardController::class, 'reports'])->name('reports');
    });
