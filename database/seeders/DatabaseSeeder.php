<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\UserSeeder; // Si este es tu único seeder de usuarios

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            // Agrega otros seeders aquí si los necesitas, pero asegúrate
            // de que no dupliquen la lógica de creación de usuarios.
        ]);
    }
}
