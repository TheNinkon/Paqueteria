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

        // 1. Crear permisos
        $permissions = [
            'view_admin_dashboard',
            'manage_users',
            'manage_vendors',
            'manage_riders',
            'manage_packages',
            'view_reports',
            'view_proveedor_dashboard',
            'view_proveedor_riders',
            'view_proveedor_packages',
            'view_client_dashboard',
            'view_client_reports',
            'view_client_packages',
            'manage_gerente_dashboard',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $permissionsRider = [
            'view_rider_dashboard',
            'view_own_packages',
            'update_package_status',
            'scan_packages',
        ];
        foreach ($permissionsRider as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'repartidor']);
        }

        // 2. Crear roles y asignar permisos
        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $adminRole->givePermissionTo(Permission::where('guard_name', 'web')->get());

        $gerenteRole = Role::firstOrCreate(['name' => 'Gerente', 'guard_name' => 'web']);
        $gerenteRole->givePermissionTo(['manage_gerente_dashboard', 'manage_vendors', 'manage_riders']);

        $proveedorRole = Role::firstOrCreate(['name' => 'Proveedor', 'guard_name' => 'web']);
        $proveedorRole->givePermissionTo(['view_proveedor_dashboard', 'view_proveedor_riders', 'view_proveedor_packages']);

        $clienteRole = Role::firstOrCreate(['name' => 'Cliente_Corporativo', 'guard_name' => 'web']);
        $clienteRole->givePermissionTo(['view_client_dashboard', 'view_client_reports', 'view_client_packages']);

        $repartidorRole = Role::firstOrCreate(['name' => 'Repartidor', 'guard_name' => 'repartidor']);
        $repartidorRole->givePermissionTo(Permission::where('guard_name', 'repartidor')->get());
    }
}
