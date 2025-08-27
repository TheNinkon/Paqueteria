<?php

namespace App\Actions\Package;

use App\Models\Package;
use App\Enums\PackageStatus;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StorePackagesAction
{
    public function execute(array $codes, int $clientId)
    {
        DB::beginTransaction();
        try {
            $foundShipmentId = null;
            foreach ($codes as $code) {
                $existingPackage = Package::where('unique_code', 'like', substr($code, 0, -3) . '%')
                    ->where('client_id', $clientId)
                    ->first();

                if ($existingPackage && $existingPackage->shipment_id) {
                    $foundShipmentId = $existingPackage->shipment_id;
                    break;
                }
            }
            $shipmentId = $foundShipmentId ?? 'SHIP-' . now()->format('YmdHis') . Str::random(5);

            foreach ($codes as $code) {
                $package = Package::create([
                    'unique_code' => $code,
                    'client_id'   => $clientId,
                    'shipment_id' => $shipmentId,
                    'status'      => PackageStatus::WAREHOUSE_RECEIVED, // Usar el Enum aquí
                ]);

                $package->histories()->create([
                    'status' => PackageStatus::WAREHOUSE_RECEIVED, // Usar el Enum aquí
                    'details' => 'Paquete recibido en la nave.',
                    'user_id' => auth()->id(),
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
