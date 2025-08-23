<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Rider;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RiderSeeder extends Seeder
{
    public function run(): void
    {
        $riderRole = Role::firstOrCreate(['name' => 'Repartidor', 'guard_name' => 'repartidor']);

        $rider = Rider::firstOrCreate([
            'email' => 'repartidor@example.com',
        ], [
            'full_name' => 'Repartidor Test',
            'phone' => '123456789',
            'start_date' => now(),
            'password' => Hash::make('password'),
        ]);

        $rider->assignRole($riderRole);
    }
}
