<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class MenuServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $verticalMenuPath = '';
        $horizontalMenuPath = '';

        // Lógica para cargar el menú dinámicamente según el rol
        if (Auth::check()) {
            if (Auth::user()->hasRole('Administrador')) {
                $verticalMenuPath = base_path('resources/menu/adminMenu.json'); // Asumiendo que has creado este archivo
            } elseif (Auth::user()->hasRole('Proveedor')) {
                $verticalMenuPath = base_path('resources/menu/proveedorMenu.json');
            } elseif (Auth::user()->hasRole('Repartidor')) {
                $verticalMenuPath = base_path('resources/menu/repartidorMenu.json');
            } elseif (Auth::user()->hasRole('Cliente_Corporativo')) {
                $verticalMenuPath = base_path('resources/menu/clienteMenu.json');
            }
        } else {
            // Menú para usuarios no autenticados, si fuera necesario
            $verticalMenuPath = base_path('resources/menu/defaultMenu.json');
        }

        if (File::exists($verticalMenuPath)) {
            $verticalMenuJson = File::get($verticalMenuPath);
            $verticalMenuData = json_decode($verticalMenuJson);
            $this->app->make('view')->share('menuData', [$verticalMenuData]);
        }
        // Si también usas un menú horizontal, podrías añadir una lógica similar.
        // Para este proyecto, nos enfocaremos en el menú vertical.

    }
}
