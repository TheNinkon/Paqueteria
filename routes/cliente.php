<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Cliente\DashboardController as ClienteDashboardController;

Route::middleware(['auth:web', 'role:Cliente_Corporativo'])
    ->prefix('cliente')
    ->name('cliente.')
    ->group(function () {
    Route::get('/dashboard', [ClienteDashboardController::class, 'index'])->name('dashboard');
    Route::get('paquetes', [\App\Http\Controllers\Cliente\PackageController::class, 'index'])->name('packages.index');
    Route::get('reportes', [\App\Http\Controllers\Cliente\PackageController::class, 'reports'])->name('reports.index');
});
