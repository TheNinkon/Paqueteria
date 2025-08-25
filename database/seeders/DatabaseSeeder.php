<?php
// File: database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Database\Seeders\RoleAndPermissionSeeder;
use Database\Seeders\UserSeeder;
use Database\Seeders\ClientSeeder; // Importa el seeder de clientes

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->call([
            RoleAndPermissionSeeder::class,
            UserSeeder::class,
            ClientSeeder::class, // Agrega esta línea para llamar a tu seeder de clientes
            // Agrega otros seeders aquí si los necesitas
        ]);
    }
}
