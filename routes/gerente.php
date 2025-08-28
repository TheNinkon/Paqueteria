<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Gerente\DashboardController as GerenteDashboardController;
use App\Http\Controllers\Gerente\Package\PackageController as GerentePackageController;

Route::middleware(['auth:web', 'role:Gerente'])
    ->prefix('gerente')
    ->name('gerente.')
    ->group(function () {
        Route::get('/dashboard', [GerenteDashboardController::class, 'index'])->name('dashboard');

        // Paquetes
        Route::get('packages', [GerentePackageController::class, 'index'])->name('packages.index');
        Route::post('packages', [GerentePackageController::class, 'store'])->name('packages.store');
        Route::get('packages/assign', [GerentePackageController::class, 'assign'])->name('packages.assign');
        Route::post('packages/assign', [GerentePackageController::class, 'performAssignment'])->name('packages.performAssignment');

        // Historial (AJAX)
        Route::get('packages/{package}/history', [GerentePackageController::class, 'history'])->name('packages.history');

        // Incidencias
        Route::get('incidents', [GerentePackageController::class, 'incidents'])->name('incidents.index');
        Route::get('incidents/create', [GerentePackageController::class, 'createIncident'])->name('incidents.create');
        Route::post('incidents', [GerentePackageController::class, 'storeIncident'])->name('incidents.store');
        Route::put('incidents/{incident}/resolve', [GerentePackageController::class, 'resolveIncident'])->name('incidents.resolve');

        // API para buscar paquetes (findPackage)
        Route::post('api/clients/identify', [GerentePackageController::class, 'identifyClient'])->name('api.clients.identify');
        Route::post('packages/find', [GerentePackageController::class, 'findPackage'])->name('packages.find');
    });
