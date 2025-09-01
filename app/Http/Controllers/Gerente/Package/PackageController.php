<?php

namespace App\Http\Controllers\Gerente\Package;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Package;
use App\Enums\PackageStatus;
use App\Actions\Package\StorePackagesAction;
use App\Models\Rider;
use App\Models\Incident;
use App\Models\IncidentType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PackageController extends Controller
{
  // Muestra el listado de paquetes con filtros
  public function index(Request $request)
  {
    $clients = Client::all();
    $packages = Package::with('client')
      ->when($request->filled('search'), function ($query) use ($request) {
        $search = $request->input('search');
        $query->where('unique_code', 'like', "%{$search}%")
              ->orWhere('shipment_id', 'like', "%{$search}%");
      })
      ->when($request->filled('client_id'), function ($query) use ($request) {
        $query->where('client_id', $request->input('client_id'));
      })
      ->when($request->filled('status'), function ($query) use ($request) {
        $query->where('status', $request->input('status'));
      })
      ->orderByDesc('created_at')
      ->paginate(15);

    $kpis = [
      'total' => Package::count(),
      'received' => Package::where('status', PackageStatus::RECEIVED)->count(),
      'assigned' => Package::where('status', PackageStatus::ASSIGNED)->count(),
      'delivered' => Package::where('status', PackageStatus::DELIVERED)->count(),
    ];

    return view('content.gerente.packages.index', compact('packages', 'clients', 'kpis'));
  }

  // Nuevo endpoint para validar el código de bulto y sugerir cliente
  public function validateCode(Request $request)
  {
    $request->validate(['unique_code' => 'required|string|max:255']);
    $uniqueCode = $request->input('unique_code');

    $client = Client::where('label_pattern', '!=', '')
      ->get()
      ->first(function ($c) use ($uniqueCode) {
        return preg_match('/' . $c->label_pattern . '/', $uniqueCode);
      });

    if ($client) {
      return response()->json(['success' => true, 'client' => $client]);
    }

    return response()->json(['success' => false, 'message' => 'No se encontró un cliente para este código.']);
  }

  // Endpoint para guardar múltiples paquetes
  public function store(Request $request, StorePackagesAction $storePackagesAction)
  {
    $request->validate([
        'client_id' => 'required|exists:clients,id',
        'codes' => 'required|array|min:1',
        'codes.*' => 'required|string|distinct|max:255',
    ]);

    $result = $storePackagesAction->execute($request->input('codes'), $request->input('client_id'));

    if ($result['success']) {
        return response()->json(['success' => true, 'message' => 'Paquetes agregados exitosamente.']);
    }

    return response()->json(['success' => false, 'message' => $result['message']], 500);
  }

  // Muestra el historial de un paquete específico
  public function history(Package $package)
  {
    $history = $package->history()->orderBy('created_at', 'asc')->get();
    return response()->json($history);
  }

  // Muestra la vista para asignar paquetes a repartidores
  public function assign()
  {
    $riders = Rider::orderBy('full_name')->get();
    $unassignedPackages = Package::with('client')
      ->whereNull('rider_id')
      ->orderByDesc('created_at')
      ->get();

    return view('content.gerente.packages.assign', compact('riders', 'unassignedPackages'));
  }

  // Realiza la asignación de paquetes a un repartidor
  public function performAssignment(Request $request)
  {
    $data = $request->validate([
      'packages' => 'required|array|min:1',
      'packages.*' => 'integer|exists:packages,id',
      'rider_id' => 'required|integer|exists:riders,id',
    ]);

    $updated = Package::whereIn('id', $data['packages'])
      ->update([
        'rider_id' => $data['rider_id'],
        'status' => PackageStatus::ASSIGNED->value,
      ]);

    return response()->json([
      'success' => true,
      'message' => $updated > 1 ? 'Paquetes asignados correctamente.' : 'Paquete asignado correctamente.'
    ]);
  }

  // Alias para compatibilidad de API: identifica cliente según código (usa validateCode)
  public function identifyClient(Request $request)
  {
    return $this->validateCode($request);
  }

  // Busca un paquete por código único
  public function findPackage(Request $request)
  {
    $request->validate(['unique_code' => 'required|string']);
    $package = Package::with('client')
      ->where('unique_code', $request->input('unique_code'))
      ->first();

    if (!$package) {
      return response()->json(['success' => false, 'message' => 'Paquete no encontrado'], 404);
    }

    return response()->json(['success' => true, 'package' => $package]);
  }

  // Listado de incidencias
  public function incidents(Request $request)
  {
    $incidentTypes = IncidentType::orderBy('name')->get();
    $incidents = Incident::with(['package.client', 'incidentType'])
      ->when($request->filled('incident_type_id'), fn($q) => $q->where('incident_type_id', $request->input('incident_type_id')))
      ->orderByDesc('created_at')
      ->paginate(15);

    return view('content.gerente.incidents.index', compact('incidents', 'incidentTypes'));
  }

  // Formulario para crear incidencia
  public function createIncident()
  {
    $incidentTypes = IncidentType::orderBy('name')->get();
    return view('content.gerente.incidents.create', compact('incidentTypes'));
  }

  // Guarda una incidencia
  public function storeIncident(Request $request)
  {
    $data = $request->validate([
      'package_id' => 'required|exists:packages,id',
      'incident_type_id' => 'required|exists:incident_types,id',
      'notes' => 'nullable|string',
    ]);

    $incident = Incident::create([
      'package_id' => $data['package_id'],
      'incident_type_id' => $data['incident_type_id'],
      'notes' => $data['notes'] ?? null,
      'reported_by_user_id' => Auth::id(),
    ]);

    // Opcional: actualiza estado del paquete a incidencia
    Package::where('id', $data['package_id'])->update(['status' => PackageStatus::INCIDENT->value]);

    return response()->json(['success' => true, 'message' => 'Incidencia creada correctamente', 'incident' => $incident]);
  }

  // Resuelve una incidencia (marcar como resuelta)
  public function resolveIncident(Incident $incident)
  {
    // Aquí podrías cambiar el estado del paquete, o marcar la incidencia como resuelta si tuviera un campo.
    return response()->json(['success' => true, 'message' => 'Incidencia resuelta']);
  }
}
