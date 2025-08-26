<?php
// File: app/Http/Controllers/Gerente/Package/PackageController.php

namespace App\Http\Controllers\Gerente\Package;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\Client;
use App\Models\Package;
use App\Models\Rider;
use App\Models\IncidentType;
use App\Models\Incident;
use App\Models\PackageHistory;
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
     * Guarda paquetes ingresados en lote + crea historial ("received").
     */
    public function store(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'codes'     => 'required|array',
                'codes.*'   => 'required|string|unique:packages,unique_code',
                'client_id' => 'required|exists:clients,id'
            ]);

            $codes    = $request->input('codes');
            $clientId = $request->input('client_id');
            $userId   = auth()->id();

            // Reutiliza shipment_id si encuentra uno afín por prefijo
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

            // Crear paquete + historial por cada código
            foreach ($codes as $code) {
                $package = Package::create([
                    'unique_code' => $code,
                    'client_id'   => $clientId,
                    'shipment_id' => $shipmentId,
                    'status'      => 'received',
                ]);

                // Historial
                $package->histories()->create([
                    'status'      => 'received',
                    'description' => 'Paquete recibido en la nave.',
                    'user_id'     => $userId,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Paquetes ingresados correctamente.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
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
     * Procesa la asignación de paquetes a un repartidor + historial ("assigned").
     */
    public function performAssignment(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $request->validate([
                'packages'   => 'required|array',
                'packages.*' => 'required|exists:packages,id',
                'rider_id'   => 'required|exists:riders,id'
            ]);

            $userId  = auth()->id();
            $rider   = Rider::findOrFail($request->input('rider_id'));
            $packages = Package::whereIn('id', $request->input('packages'))->lockForUpdate()->get();

            foreach ($packages as $package) {
                $package->update([
                    'rider_id' => $rider->id,
                    'status'   => 'assigned',
                ]);

                $package->histories()->create([
                    'status'      => 'assigned',
                    'description' => 'Asignado al repartidor: ' . ($rider->full_name ?? ('#'.$rider->id)),
                    'user_id'     => $userId,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Paquetes asignados correctamente.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
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
     * Guarda la incidencia de un paquete + historial ("incident").
     */
    public function storeIncident(Request $request): JsonResponse
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'package_id'       => 'required|exists:packages,id',
                'incident_type_id' => 'required|exists:incident_types,id',
                'notes'            => 'nullable|string'
            ]);

            $package = Package::lockForUpdate()->find($validated['package_id']);

            if ($package->status === 'delivered') {
                DB::rollBack();
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

            // Historial
            $package->histories()->create([
                'status'      => 'incident',
                'description' => 'Incidencia creada: ' . IncidentType::find($validated['incident_type_id'])->name,
                'user_id'     => auth()->id(),
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Incidencia creada con éxito.']);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error de validación.',
                'errors'  => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            DB::rollBack();
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
    public function incidents(Request $request)
    {
        // Lógica de los filtros para las incidencias
        $query = Incident::query();

        if ($request->filled('incident_type_id')) {
            $query->where('incident_type_id', $request->input('incident_type_id'));
        }

        if ($request->filled('status')) {
            $query->whereHas('package', function ($q) use ($request) {
                $q->where('status', $request->input('status'));
            });
        }

        $incidents = $query->with(['package.client', 'incidentType', 'reporter'])->latest()->get();

        $incidentTypes = IncidentType::all();
        $packageStatuses = ['received', 'assigned', 'in_delivery', 'delivered', 'incident', 'returned'];

        return view('content.gerente.incidents.index', compact('incidents', 'incidentTypes', 'packageStatuses'));
    }

    /**
     * Resuelve una incidencia.
     * (Opcional: podrías añadir historial "resolved" si lo manejas como estado)
     */
    public function resolveIncident(Incident $incident): JsonResponse
    {
        $package = $incident->package;
        $package->status = 'resolved'; // O el estado que prefieras
        $package->save();

        $incident->delete(); // Eliminamos la incidencia una vez resuelta

        return response()->json(['success' => true, 'message' => 'Incidencia resuelta correctamente.']);
    }

    /**
     * Devuelve un paquete a la empresa de origen + historial ("returned").
     */
    public function returnToVendor(Package $package)
    {
        $package->update(['status' => 'returned']);

        $package->histories()->create([
            'status'      => 'returned',
            'description' => 'Devuelto a la empresa de origen.',
            'user_id'     => auth()->id()
        ]);

        return redirect()->back()->with('success', 'Paquete marcado como devuelto.');
    }

    /**
     * Historial (para el modal/timeline AJAX).
     */
    public function history(Package $package)
    {
        $history = $package->histories()->with('user')->orderBy('created_at', 'asc')->get()->map(function ($item) {
            $statusTitles = [
                'received'    => 'Paquete Recibido',
                'assigned'    => 'Paquete Asignado',
                'in_delivery' => 'En Reparto',
                'delivered'   => 'Paquete Entregado',
                'incident'    => 'Incidencia Creada',
                'returned'    => 'Devuelto a Origen',
                'resolved'    => 'Incidencia Resuelta',
            ];

            $statusClasses = [
                'received'    => 'warning',
                'assigned'    => 'info',
                'in_delivery' => 'info',
                'delivered'   => 'success',
                'incident'    => 'danger',
                'returned'    => 'dark',
                'resolved'    => 'primary',
            ];

            return [
                'status_title' => $statusTitles[$item->status] ?? ucfirst($item->status),
                'status_class' => $statusClasses[$item->status] ?? 'secondary',
                'description'  => $item->description,
                'created_at'   => $item->created_at->format('d/m/Y H:i'),
                'extra_info'   => optional($item->user)->full_name,
            ];
        });

        return response()->json($history);
    }
}
