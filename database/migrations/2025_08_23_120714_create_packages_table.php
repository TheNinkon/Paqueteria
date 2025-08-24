<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('packages', function (Blueprint $table) {
            $table->id();
            $table->string('unique_code')->unique();
            $table->string('shipment_id')->nullable();
            $table->string('status')->default('received');
            $table->foreignId('client_id')->nullable()->constrained('clients');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('packages');
    }
};
