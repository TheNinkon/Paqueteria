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
        $verticalMenuData = json_decode('{"menu": []}'); // Menú vacío por defecto

        if (Auth::guard('web')->check()) {
            $user = Auth::guard('web')->user();
            $menuFilePath = '';

            if ($user->hasRole('Administrador')) {
                $menuFilePath = base_path('resources/menu/adminMenu.json');
            } elseif ($user->hasRole('Proveedor')) {
                $menuFilePath = base_path('resources/menu/proveedorMenu.json');
            } elseif ($user->hasRole('Cliente_Corporativo')) {
                $menuFilePath = base_path('resources/menu/clienteMenu.json');
            }

            if (File::exists($menuFilePath)) {
                $verticalMenuJson = File::get($menuFilePath);
                $verticalMenuData = json_decode($verticalMenuJson);
            }
        } elseif (Auth::guard('repartidor')->check()) {
             $menuFilePath = base_path('resources/menu/repartidorMenu.json');
             if (File::exists($menuFilePath)) {
                 $verticalMenuJson = File::get($menuFilePath);
                 $verticalMenuData = json_decode($verticalMenuJson);
             }
        }

        $this->app->make('view')->share('menuData', [$verticalMenuData]);
    }
}
