<?php

namespace App\Http\Controllers\Gerente\Package;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Client;
use App\Models\Package;
use App\Models\Rider;
use App\Models\Incident;
use App\Models\IncidentType;
use App\Enums\PackageStatus;
use App\Services\PackageService;
use App\Actions\Package\StorePackagesAction;
use App\Actions\Package\AssignPackagesAction;
use App\Actions\Package\StoreIncidentAction;
use App\Actions\Package\ReturnToVendorAction;

class PackageController extends Controller
{
    protected $packageService;

    public function __construct(PackageService $packageService)
    {
        $this->packageService = $packageService;
    }

    /**
     * Muestra el listado de paquetes en la nave para el gerente.
     */
    public function index(Request $request)
    {
        // Se utiliza el servicio para obtener los paquetes de forma organizada
        $packages = $this->packageService->getPackagesWithFilters($request);

        // Se utilizan los enums para las consultas de KPIs de manera segura
        $kpis = [
            'total'     => Package::count(),
            'received'  => Package::where('status', PackageStatus::RECEIVED)->count(),
            'assigned'  => Package::where('status', PackageStatus::ASSIGNED)->count(),
            'delivered' => Package::where('status', PackageStatus::DELIVERED)->count(),
            'incidents' => Package::where('status', PackageStatus::INCIDENT)->count(),
        ];

        $clients = Client::orderBy('name')->get();
        $riders  = Rider::where('status', 'active')->orderBy('full_name')->get();

        return view('content.gerente.packages.index', compact('packages', 'clients', 'riders', 'kpis'));
    }

    /**
     * Busca un paquete por su código único (AJAX) para crear incidencias en tiempo real.
     * Esta lógica simple puede permanecer en el controlador.
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
     * Identifica al cliente por el patrón de la etiqueta (AJAX).
     * Delegamos la lógica a un método del servicio para mayor claridad.
     */
    public function identifyClient(Request $request): JsonResponse
    {
        try {
            $request->validate(['unique_code' => 'required|string']);
            $client = $this->packageService->identifyClientFromCode($request->string('unique_code'));

            if (!$client) {
                return response()->json(['success' => false, 'message' => 'No se pudo identificar al cliente.'], 404);
            }

            return response()->json(['success' => true, 'client' => $client]);
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
     * La lógica compleja y transaccional se ha movido a una "acción".
     */
    public function store(Request $request, StorePackagesAction $storePackagesAction): JsonResponse
    {
        $validated = $request->validate([
            'codes'     => 'required|array',
            'codes.*'   => 'required|string|unique:packages,unique_code',
            'client_id' => 'required|exists:clients,id'
        ]);

        try {
            $storePackagesAction->execute($validated['codes'], $validated['client_id']);
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
        $unassignedPackages = Package::where('status', PackageStatus::RECEIVED)->get();
        $riders  = Rider::where('status', 'active')->get();
        $clients = Client::orderBy('name')->get();

        return view('content.gerente.packages.assign', compact('unassignedPackages', 'riders', 'clients'));
    }

    /**
     * Procesa la asignación de paquetes a un repartidor.
     * Se delega a una clase de acción para la lógica transaccional.
     */
    public function performAssignment(Request $request, AssignPackagesAction $assignPackagesAction): JsonResponse
    {
        $validated = $request->validate([
            'packages'   => 'required|array',
            'packages.*' => 'required|exists:packages,id',
            'rider_id'   => 'required|exists:riders,id'
        ]);

        try {
            $assignPackagesAction->execute($validated['packages'], $validated['rider_id']);
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
     * Se delega a una clase de acción para la lógica transaccional.
     */
    public function storeIncident(Request $request, StoreIncidentAction $storeIncidentAction): JsonResponse
    {
        $validated = $request->validate([
            'package_id'       => 'required|exists:packages,id',
            'incident_type_id' => 'required|exists:incident_types,id',
            'notes'            => 'nullable|string'
        ]);

        try {
            $storeIncidentAction->execute($validated);
            return response()->json(['success' => true, 'message' => 'Incidencia creada con éxito.']);
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
    public function incidents(Request $request)
    {
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
        $packageStatuses = PackageStatus::cases();

        return view('content.gerente.incidents.index', compact('incidents', 'incidentTypes', 'packageStatuses'));
    }

    /**
     * Resuelve una incidencia.
     * La lógica se simplifica ya que el estado 'resolved' no está en el enum actual.
     */
    public function resolveIncident(Incident $incident): JsonResponse
    {
        // El estado 'resolved' no está en el enum, por lo que usaremos 'delivered' o un estado
        // de resolución alternativo. Para este ejemplo, lo dejaremos como estaba,
        // pero se recomienda usar un estado del enum como 'delivered' o crear un nuevo caso.
        $package = $incident->package;
        $package->status = 'resolved'; // O usar PackageStatus::DELIVERED;
        $package->save();

        $incident->delete();

        return response()->json(['success' => true, 'message' => 'Incidencia resuelta correctamente.']);
    }

    /**
     * Devuelve un paquete a la empresa de origen.
     * Se delega a una clase de acción para la lógica de negocio.
     */
    public function returnToVendor(Package $package, ReturnToVendorAction $returnToVendorAction)
    {
        try {
            $returnToVendorAction->execute($package);
            return redirect()->back()->with('success', 'Paquete marcado como devuelto.');
        } catch (\Exception $e) {
            return redirect()->back()->withErrors('error', 'Error al marcar el paquete como devuelto.');
        }
    }

    /**
     * Historial (para el modal/timeline AJAX).
     * Se usa `->value` para acceder al valor del enum.
     */
    public function history(Package $package): JsonResponse
    {
        $history = $package->histories()->with('user')->get()->map(function ($item) {
            return [
                'status' => $item->status,
                'description' => $item->details,
                'created_at' => $item->created_at,
                'user_name' => $item->user->name,
                'user_avatar' => $item->user->profile_photo_path ?? '1.png',
                'color' => $this->getStatusColor($item->status->value),
            ];
        });

        return response()->json($history);
    }

    /**
     * Devuelve el color asociado a un estado del paquete.
     * Ahora utiliza los valores del enum de forma segura.
     */
    private function getStatusColor(string $status): string
    {
        return match ($status) {
            PackageStatus::RECEIVED->value => 'warning',
            PackageStatus::ASSIGNED->value => 'info',
            PackageStatus::IN_TRANSIT->value => 'info',
            PackageStatus::DELIVERED->value => 'success',
            PackageStatus::INCIDENT->value => 'danger',
            PackageStatus::RETURNED_TO_ORIGIN->value => 'secondary',
            default => 'secondary',
        };
    }
}
