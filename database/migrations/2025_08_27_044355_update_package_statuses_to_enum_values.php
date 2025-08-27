<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Enums\PackageStatus;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Primero, ajusta el tamaño de la columna 'status'
        Schema::table('packages', function (Blueprint $table) {
            $table->string('status', 30)->change();
        });

        Schema::table('package_histories', function (Blueprint $table) {
            $table->string('status', 30)->change();
        });

        // Mapeo de los valores antiguos a los nuevos del Enum
        $statusMap = [
            'received' => PackageStatus::RECEIVED->value,
            'assigned' => PackageStatus::ASSIGNED->value,
            'in_delivery' => PackageStatus::IN_TRANSIT->value,
            'delivered' => PackageStatus::DELIVERED->value,
            'incident' => PackageStatus::INCIDENT->value,
            'returned' => PackageStatus::RETURNED_TO_ORIGIN->value,
        ];

        foreach ($statusMap as $oldValue => $newValue) {
            DB::table('packages')->where('status', $oldValue)->update(['status' => $newValue]);
            DB::table('package_histories')->where('status', $oldValue)->update(['status' => $newValue]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // En caso de querer revertir la migración, define la lógica inversa aquí.
        // Esto podría ser complicado dependiendo de la longitud de los nuevos valores.
        // Para este ejemplo, lo dejaremos vacío.
    }
};
