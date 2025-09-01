<?php

namespace App\Http\Controllers\Repartidor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        return view('content.repartidor.packages.index');
    }

    public function scan()
    {
        return view('content.repartidor.packages.scan');
    }

    public function updateStatus($packageId)
    {
        return response()->json(['success' => true, 'message' => 'Estado actualizado (stub).']);
    }

    public function reportIncident($packageId)
    {
        return response()->json(['success' => true, 'message' => 'Incidencia reportada (stub).']);
    }
}
