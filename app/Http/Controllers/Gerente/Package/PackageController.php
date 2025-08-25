<?php
// File: app/Http/Controllers/Gerente/Package/PackageController.php

namespace App\Http\Controllers\Gerente\Package;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\Package;
use App\Models\Rider;
use App\Models\IncidentType;
use App\Models\Incident;
use Illuminate\Http\JsonResponse;

class PackageController extends Controller
{
    /**
     * Muestra el listado de paquetes en la nave para el gerente.
     */
    public function index(Request $request)
    {
        // Para filtros y selects en UI
        $clients = Client::orderBy('name')->get();
        $riders  = Rider::orderBy('full_name')->get();

        // Filtros
        $query = Package::query();

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->input('client_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            $query->where(function ($q) use ($searchTerm) {
                $q->where('unique_code', 'like', "%{$searchTerm}%")
                  ->orWhere('shipment_id', 'like', "%{$searchTerm}%");
            });
        }

        // Resultado
        $packages = $query->with('client')->latest()->get();

        // KPIs para cards
        $kpis = [
            'total'     => Package::count(),
            'received'  => Package::where('status', 'received')->count(),
            'assigned'  => Package::where('status', 'assigned')->count(),
            'delivered' => Package::where('status', 'delivered')->count(),
            'incidents' => Package::where('status', 'incident')->count(),
        ];

        return view('content.gerente.packages.index', compact('packages', 'clients', 'riders', 'kpis'));
    }

    /**
     * Busca un paquete por su código único (AJAX) para crear incidencias en tiempo real.
     */
    public function findPackage(Request $request): JsonResponse
    {
        try {
            $request->validate(['unique_code' => 'required|string']);
            $package = Package::with('client')->where('unique_code', $request->input('unique_code'))->first();

            if (!$package) {
                return response()->json(['success' => false, 'message' => 'Paquete no encontrado.'], 404);
            }

            return response()->json(['success' => true, 'package' => $package]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al buscar el paquete.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Identifica al cliente por el patrón de la etiqueta.
     */
    public function identifyClient(Request $request): JsonResponse
    {
        try {
            $request->validate(['unique_code' => 'required|string']);
            $uniqueCode = $request->string('unique_code');

            foreach (Client::all() as $client) {
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
                'error'   => $e->getMessage()
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

            // Reutiliza shipment_id si encuentra uno afín por prefijo
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
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra la interfaz para asignar paquetes a un repartidor.
     */
    public function assign()
    {
        $unassignedPackages = Package::where('status', 'received')->get();
        $riders  = Rider::where('status', 'active')->get();
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
                'packages'   => 'required|array',
                'packages.*' => 'required|exists:packages,id',
                'rider_id'   => 'required|exists:riders,id'
            ]);

            $packages = Package::whereIn('id', $request->input('packages'))->get();

            foreach ($packages as $package) {
                $package->rider_id = $request->input('rider_id');
                $package->status   = 'assigned';
                $package->save();
            }

            return response()->json(['success' => true, 'message' => 'Paquetes asignados correctamente.']);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al asignar los paquetes.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra el formulario para crear una incidencia.
     */
    public function createIncident()
    {
        $incidentTypes = IncidentType::all();
        return view('content.gerente.incidents.create', compact('incidentTypes'));
    }

    /**
     * Guarda la incidencia de un paquete.
     */
    public function storeIncident(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'package_id'       => 'required|exists:packages,id',
                'incident_type_id' => 'required|exists:incident_types,id',
                'notes'            => 'nullable|string'
            ]);

            $package = Package::find($validated['package_id']);

            if ($package->status === 'delivered') {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede crear una incidencia para un paquete ya entregado.'
                ], 400);
            }

            // Actualiza estado y crea la incidencia
            $package->update(['status' => 'incident']);

            Incident::create([
                'package_id'          => $validated['package_id'],
                'incident_type_id'    => $validated['incident_type_id'],
                'notes'               => $validated['notes'] ?? null,
                'reported_by_user_id' => auth()->id(),
            ]);

            return response()->json(['success' => true, 'message' => 'Incidencia creada con éxito.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error interno del servidor al guardar la incidencia.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Listado de incidencias.
     */
    public function incidents()
    {
        $incidents = Incident::with(['package.client', 'package.rider'])
            ->latest()
            ->get();

        return view('content.gerente.incidents.index', compact('incidents'));
    }
}
