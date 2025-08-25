<?php
// File: app/Http/Controllers/Gerente/Package/PackageController.php

namespace App\Http\Controllers\Gerente\Package;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\Package;
use Illuminate\Http\JsonResponse;

class PackageController extends Controller
{
    /**
     * Listado de paquetes (GET /gerente/packages)
     */
    public function index()
    {
        $packages = Package::with('client')->latest()->get();
        $clients  = Client::orderBy('name')->get();
        return view('content.gerente.packages.index', compact('packages', 'clients'));
    }

    /**
     * Identifica al cliente por el patrón de la etiqueta (POST /api/clients/identify)
     */
    public function identifyClient(Request $request): JsonResponse
    {
        try {
            $request->validate(['unique_code' => 'required|string']);
            $uniqueCode = $request->string('unique_code');

            $clients = Client::all();

            foreach ($clients as $client) {
                if ($client->label_pattern && preg_match('/' . $client->label_pattern . '/', $uniqueCode)) {
                    return response()->json([
                        'success' => true,
                        'client'  => $client
                    ]);
                }
            }

            return response()->json([
                'success' => false,
                'message' => 'No se pudo identificar al cliente.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al identificar el cliente.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Guarda paquetes ingresados (POST /gerente/packages)
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'codes'     => 'required|array',
                'codes.*'   => 'required|string|unique:packages,unique_code',
                'client_id' => 'required|exists:clients,id'
            ]);

            $packagesToCreate = [];
            $foundShipmentId = null;

            // Lógica para encontrar un Envío ID existente
            foreach ($request->input('codes') as $code) {
                $lastChars = substr($code, -3);
                $prefix = substr($code, 0, strlen($code) - 3);

                // Buscamos un paquete que comparta el mismo prefijo
                $existingPackage = Package::where('unique_code', 'like', $prefix . '%')
                                          ->where('client_id', $request->input('client_id'))
                                          ->first();

                if ($existingPackage && $existingPackage->shipment_id) {
                    $foundShipmentId = $existingPackage->shipment_id;
                    break; // Salimos del bucle si encontramos un ID
                }
            }

            // Si no encontramos un ID, generamos uno nuevo para todo el lote
            $shipmentId = $foundShipmentId ?? 'SHIP-' . now()->format('YmdHis') . Str::random(5);

            foreach ($request->input('codes') as $code) {
                $packagesToCreate[] = [
                    'unique_code' => $code,
                    'client_id'   => $request->input('client_id'),
                    'shipment_id' => $shipmentId,
                    'status'      => 'received',
                    'created_at'  => now(),
                    'updated_at'  => now(),
                ];
            }

            Package::insert($packagesToCreate);

            return response()->json(['success' => true, 'message' => 'Paquetes ingresados correctamente.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al guardar los paquetes.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
