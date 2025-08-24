<?php
// File: app/Http/Controllers/Gerente/Package/PackageController.php

namespace App\Http\Controllers\Gerente\Package;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Client;
use App\Models\Package;

class PackageController extends Controller
{
    /**
     * Listado de paquetes (GET /gerente/packages)
     */
    public function index()
    {
        $packages = Package::with('client')->latest()->paginate(15);
        $clients  = Client::orderBy('name')->get();

        return view('content.gerente.packages.index', compact('packages','clients'));
    }

    /**
     * Muestra el formulario de ingreso de paquetes (GET /gerente/packages/ingest)
     */
    public function create()
    {
        $clients = Client::orderBy('name')->get();
        return view('content.gerente.packages.ingestion', compact('clients'));
    }

    /**
     * Identifica al cliente por el patrón de la etiqueta (POST /api/clients/identify)
     */
    public function identifyClient(Request $request)
    {
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
    }

    /**
     * Guarda paquetes ingresados (POST /gerente/packages)
     */
    public function store(Request $request)
    {
        $request->validate([
            'codes'     => 'required|array',
            'codes.*'   => 'required|string|unique:packages,unique_code',
            'client_id' => 'required|exists:clients,id'
        ]);

        $packagesToCreate = [];
        $shipmentId = 'SHIP-' . now()->format('YmdHis') . Str::random(5);

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

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Paquetes ingresados correctamente.']);
        }

        return redirect()->route('gerente.packages.index')->with('success', 'Paquetes ingresados correctamente.');
    }
}
