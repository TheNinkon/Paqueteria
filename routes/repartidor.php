<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Repartidor\DashboardController as RepartidorDashboardController;

Route::middleware(['auth:repartidor'])
    ->prefix('repartidor')
    ->name('repartidor.')
    ->group(function () {
        Route::get('/dashboard', [RepartidorDashboardController::class, 'index'])->name('dashboard');
    Route::get('/perfil', [\App\Http\Controllers\Repartidor\ProfileController::class, 'show'])->name('profile');
    Route::get('/paquetes', [\App\Http\Controllers\Repartidor\PackageController::class, 'index'])->name('packages.index');
    Route::get('/escanear', [\App\Http\Controllers\Repartidor\PackageController::class, 'scan'])->name('packages.scan');
    Route::post('/paquetes/{package}/actualizar-estado', [\App\Http\Controllers\Repartidor\PackageController::class, 'updateStatus'])->name('packages.updateStatus');

    Route::post('paquetes/{package}/reportar-incidencia', [\App\Http\Controllers\Repartidor\PackageController::class, 'reportIncident'])->name('packages.reportIncident');
});
