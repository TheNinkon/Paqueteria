<?php

namespace App\Http\Controllers\Gerente\Package;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Package;
use App\Enums\PackageStatus;
use App\Actions\Package\StorePackagesAction;
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
}
