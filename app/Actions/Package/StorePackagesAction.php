<?php

namespace App\Actions\Package;

use App\Models\Package;
use App\Models\PackageHistory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StorePackagesAction
{
    public function execute(array $codes, int $clientId, int $userId)
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
                    'status'      => 'received',
                ]);

                $package->histories()->create([
                    'status' => 'received',
                    'details' => 'Paquete recibido en la nave.',
                    'user_id' => $userId,
                ]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }
}
