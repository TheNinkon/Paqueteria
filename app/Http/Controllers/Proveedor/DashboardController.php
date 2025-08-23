<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;

class VendorDashboardController extends Controller
{
    public function index()
    {
        // Ajusta la vista si usas otra diferente
        return view('content.proveedor.dashboard');
    }
}
