<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_add_rider_id_to_packages_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            // Agregamos la clave foránea para el repartidor.
            $table->foreignId('rider_id')->nullable()->constrained('riders')->after('client_id');
        });
    }

    public function down(): void
    {
        Schema::table('packages', function (Blueprint $table) {
            $table->dropForeign(['rider_id']);
            $table->dropColumn('rider_id');
        });
    }
};
