<?php

namespace App\Actions\Package;

use App\Models\Package;
use App\Models\PackageHistory;
use App\Enums\PackageStatus;
use Illuminate\Support\Facades\DB;

class StorePackagesAction
{
  public function execute(array $codes, int $clientId)
  {
    try {
      DB::beginTransaction();

      foreach ($codes as $uniqueCode) {
        // Validar si el paquete ya existe para evitar duplicados
        $existingPackage = Package::where('unique_code', $uniqueCode)->first();
        if ($existingPackage) {
            DB::rollBack();
            return ['success' => false, 'message' => "El paquete con el código {$uniqueCode} ya existe."];
        }

        $package = Package::create([
          'unique_code' => $uniqueCode,
          'client_id' => $clientId,
          'status' => PackageStatus::RECEIVED,
        ]);

        PackageHistory::create([
          'package_id' => $package->id,
          'user_id' => auth()->id(),
          'status' => PackageStatus::RECEIVED,
          'description' => 'Paquete recibido en la nave.',
        ]);
      }

      DB::commit();
      return ['success' => true, 'message' => 'Paquetes agregados con éxito.'];
    } catch (\Exception $e) {
      DB::rollBack();
      return ['success' => false, 'message' => 'Error al guardar los paquetes: ' . $e->getMessage()];
    }
  }
}
