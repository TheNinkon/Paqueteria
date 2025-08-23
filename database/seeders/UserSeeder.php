<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Asegurarse de que los roles existan
        $adminRole = Role::firstOrCreate(['name' => 'Administrador']);
        $vendorRole = Role::firstOrCreate(['name' => 'Proveedor']);
        $clientRole = Role::firstOrCreate(['name' => 'Cliente_Corporativo']);

        // Crear un usuario administrador
        $admin = User::firstOrCreate([
            'email' => 'admin@admin.com',
        ], [
            'name' => 'Admin',
            'password' => Hash::make('password'),
        ]);
        $admin->assignRole($adminRole);

        // Crear un usuario proveedor
        $vendor = User::firstOrCreate([
            'email' => 'proveedor@example.com',
        ], [
            'name' => 'Proveedor Principal',
            'password' => Hash::make('password'),
        ]);
        $vendor->assignRole($vendorRole);

        // Crear un usuario cliente corporativo
        $client = User::firstOrCreate([
            'email' => 'cliente@example.com',
        ], [
            'name' => 'CTT Express',
            'password' => Hash::make('password'),
        ]);
        $client->assignRole($clientRole);
    }
}
