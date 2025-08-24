<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Rider;
use App\Models\Vendor;
use App\Models\Client;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Asegurarse de que los roles existan
        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $gerenteRole = Role::firstOrCreate(['name' => 'Gerente', 'guard_name' => 'web']);
        $vendorRole = Role::firstOrCreate(['name' => 'Proveedor', 'guard_name' => 'web']);
        $clientRole = Role::firstOrCreate(['name' => 'Cliente_Corporativo', 'guard_name' => 'web']);
        $riderRole = Role::firstOrCreate(['name' => 'Repartidor', 'guard_name' => 'repartidor']);

        // Crear un usuario administrador
        $admin = User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole($adminRole);

        // Crear un usuario gerente
        $gerente = User::firstOrCreate([
            'email' => 'gerente@example.com',
        ], [
            'name' => 'Gerente de Zona',
            'password' => Hash::make('password'),
        ]);
        $gerente->assignRole($gerenteRole);

        // Crear el registro de proveedor
        $vendorRecord = Vendor::firstOrCreate([
            'email' => 'proveedor@example.com',
        ], [
            'name' => 'Proveedor Principal',
            'manager_id' => $gerente->id,
            'phone' => '123456789',
            'contact_person' => 'Jefe de Proveedores',
        ]);

        // Crear un usuario de autenticación para el proveedor y asignarle el rol
        $proveedorAuthUser = User::firstOrCreate([
            'email' => 'proveedor@example.com',
        ], [
            'name' => 'Proveedor Principal',
            'password' => Hash::make('password'),
        ]);
        $proveedorAuthUser->assignRole($vendorRole);

        // Crear el registro de cliente corporativo
        $clientRecord = Client::firstOrCreate([
            'contact_email' => 'cliente@example.com',
        ], [
            'name' => 'CTT Express',
        ]);

        // Crear un usuario de autenticación para el cliente y asignarle el rol
        $clientAuthUser = User::firstOrCreate([
            'email' => 'cliente@example.com',
        ], [
            'name' => 'CTT Express',
            'password' => Hash::make('password'),
        ]);
        $clientAuthUser->assignRole($clientRole);


        // Crear un usuario repartidor usando el modelo Rider
        $rider = Rider::firstOrCreate([
            'email' => 'repartidor@example.com',
        ], [
            'full_name' => 'Pedro Pérez',
            'phone' => '987654321',
            'start_date' => now(),
            'password' => Hash::make('password'),
            'vendor_id' => $vendorRecord->id,
        ]);
        $rider->assignRole($riderRole);
    }
}
