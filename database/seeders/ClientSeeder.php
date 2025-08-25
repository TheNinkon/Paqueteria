<?php
// File: database/seeders/ClientSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Client; // Importa el modelo Client

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::firstOrCreate(
            ['name' => 'CTT Express'],
            [
                'contact_email' => 'contact@cttexpress.es',
                'label_pattern' => '^004521.*$',
            ]
        );

        Client::firstOrCreate(
            ['name' => 'CTT Express (Tipo 2)'],
            [
                'contact_email' => 'contact_tipo2@cttexpress.es',
                'label_pattern' => '^008290.*$',
            ]
        );

        Client::firstOrCreate(
            ['name' => 'SEUR'],
            [
                'contact_email' => 'contact@seur.com',
                'label_pattern' => '^002803.*$',
            ]
        );

        // Puedes agregar más clientes aquí
    }
}
