<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

class MenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $menuData = json_decode('{"menu": []}'); // Menú vacío por defecto

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $menuFilePath = '';

            // LÍNEA CORREGIDA
            if ($user->hasRole('Administrador')) {
                // Si el usuario es administrador, carga el menú de administrador
                $menuFilePath = base_path('resources/menu/adminMenu.json');
            } elseif ($user->hasRole('Proveedor')) {
                $menuFilePath = base_path('resources/menu/proveedorMenu.json');
            } elseif ($user->hasRole('Cliente_Corporativo')) {
                $menuFilePath = base_path('resources/menu/clienteMenu.json');
            } else {
                // Menú por defecto si el usuario web no tiene un rol específico
                $menuFilePath = base_path('resources/menu/defaultWebMenu.json');
            }

            if (File::exists($menuFilePath)) {
                $menuData = json_decode(File::get($menuFilePath));
            }
        } elseif (Auth::guard('repartidor')->check()) {
            $menuFilePath = base_path('resources/menu/repartidorMenu.json');
            if (File::exists($menuFilePath)) {
                $menuData = json_decode(File::get($menuFilePath));
            }
        }

        $this->app->make('view')->share('menuData', $menuData);
    }
}
