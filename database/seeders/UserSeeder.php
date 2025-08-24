<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rider;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear un usuario administrador
        $admin = User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole('Administrador');

        // Crear un usuario proveedor
        $vendor = User::firstOrCreate([
            'email' => 'proveedor@example.com',
        ], [
            'name' => 'Proveedor Principal',
            'password' => Hash::make('password'),
        ]);
        $vendor->assignRole('Proveedor');

        // Crear un usuario cliente corporativo
        $client = User::firstOrCreate([
            'email' => 'cliente@example.com',
        ], [
            'name' => 'CTT Express',
            'password' => Hash::make('password'),
        ]);
        $client->assignRole('Cliente_Corporativo');

        // Crear un usuario repartidor usando el modelo Rider
        // Se añaden los campos 'phone' y 'start_date' para evitar errores
        $rider = Rider::firstOrCreate([
            'email' => 'repartidor@example.com',
        ], [
            'full_name' => 'Pedro Pérez',
            'phone' => '123456789',
            'start_date' => now(), // ¡Añade este campo!
            'password' => Hash::make('password'),
        ]);
        $rider->assignRole('Repartidor');
    }
}
