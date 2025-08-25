<?php
// File: database/migrations/xxxx_xx_xx_xxxxxx_create_incidents_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('package_id')->constrained('packages')->onDelete('cascade');
            $table->foreignId('incident_type_id')->constrained('incident_types'); // Clave foránea al tipo de incidencia
            $table->text('notes')->nullable();
            // CORRECCIÓN: La clave foránea debe apuntar a la tabla 'users'
            $table->foreignId('reported_by_user_id')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incidents');
    }
};
