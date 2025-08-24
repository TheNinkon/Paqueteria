<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Reiniciar la caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // 2. Crear permisos
        // Permisos para el guard 'web'
        $permissionsWeb = [
            'view_admin_dashboard',
            'manage_employees',
            'manage_vendors',
            'manage_packages_admin',
            'view_reports_admin',
            'view_vendor_dashboard',
            'manage_repartidores',
            'view_proveedor_packages',
            'view_client_dashboard',
            'view_client_reports',
            'view_client_packages',
        ];
        foreach ($permissionsWeb as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Permisos para el guard 'repartidor'
        $permissionsRider = [
            'view_rider_dashboard',
            'view_own_packages', // Este era el permiso que faltaba
            'update_package_status',
            'scan_packages',
        ];
        foreach ($permissionsRider as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'repartidor']);
        }

        // 3. Crear roles
        $adminRole = Role::firstOrCreate(['name' => 'Administrador', 'guard_name' => 'web']);
        $vendorRole = Role::firstOrCreate(['name' => 'Proveedor', 'guard_name' => 'web']);
        $clientRole = Role::firstOrCreate(['name' => 'Cliente_Corporativo', 'guard_name' => 'web']);
        $riderRole = Role::firstOrCreate(['name' => 'Repartidor', 'guard_name' => 'repartidor']);

        // 4. Asignar permisos a los roles
        $adminRole->givePermissionTo($permissionsWeb);
        $vendorRole->givePermissionTo(['view_vendor_dashboard', 'manage_repartidores', 'view_proveedor_packages']);
        $clientRole->givePermissionTo(['view_client_dashboard', 'view_client_reports', 'view_client_packages']);
        $riderRole->givePermissionTo($permissionsRider);
    }
}
