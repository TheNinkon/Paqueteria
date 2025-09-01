<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Proveedor\DashboardController as ProveedorDashboardController;

Route::middleware(['auth:web', 'role:Proveedor'])
    ->prefix('proveedor')
    ->name('proveedor.')
    ->group(function () {
    Route::get('/dashboard', [ProveedorDashboardController::class, 'index'])->name('dashboard');
    Route::resource('repartidores', \App\Http\Controllers\Proveedor\EmployeeController::class)->except(['destroy']);
    Route::get('paquetes', [\App\Http\Controllers\Proveedor\PackageController::class, 'index'])->name('packages.index');
    Route::get('reportes', [\App\Http\Controllers\Proveedor\ReportController::class, 'index'])->name('reports.index');
});
