<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Proveedor', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Cliente_Corporativo', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'Repartidor', 'guard_name' => 'repartidor']);
    }
}
