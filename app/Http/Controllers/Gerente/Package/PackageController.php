<?php
// File: app/Http/Controllers/Gerente/Package/PackageController.php

namespace App\Http\Controllers\Gerente\Package;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\Package;
use App\Models\Rider;
use Illuminate\Http\JsonResponse;

class PackageController extends Controller
{
    /**
     * Muestra el listado de paquetes en la nave para el gerente.
     */
    public function index()
    {
        $packages = Package::with('client')->latest()->get();
        $clients  = Client::orderBy('name')->get(); // Necesario para el modal
        return view('content.gerente.packages.index', compact('packages', 'clients'));
    }

    /**
     * Identifica al cliente por el patrón de la etiqueta.
     * Esta es una API usada por el modal.
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
     * Guarda paquetes ingresados en lote.
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

            foreach ($request->input('codes') as $code) {
                $existingPackage = Package::where('unique_code', 'like', substr($code, 0, -3) . '%')
                                          ->where('client_id', $request->input('client_id'))
                                          ->first();

                if ($existingPackage && $existingPackage->shipment_id) {
                    $foundShipmentId = $existingPackage->shipment_id;
                    break;
                }
            }

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

    /**
     * Muestra la interfaz para asignar paquetes a un repartidor.
     */
    public function assign()
    {
        $unassignedPackages = Package::where('status', 'received')->get();
        $riders = Rider::where('status', 'active')->get();
        $clients = Client::orderBy('name')->get();

        return view('content.gerente.packages.assign', compact('unassignedPackages', 'riders', 'clients'));
    }

    /**
     * Procesa la asignación de paquetes a un repartidor.
     */
    public function performAssignment(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'packages' => 'required|array',
                'packages.*' => 'required|exists:packages,id',
                'rider_id' => 'required|exists:riders,id'
            ]);

            $packages = Package::whereIn('id', $request->input('packages'))->get();

            foreach ($packages as $package) {
                $package->rider_id = $request->input('rider_id');
                $package->status = 'assigned';
                $package->save();
            }

            return response()->json(['success' => true, 'message' => 'Paquetes asignados correctamente.']);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al asignar los paquetes.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
