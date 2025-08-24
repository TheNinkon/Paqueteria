<?php

namespace App\Http\Controllers\Gerente;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Muestra el dashboard del Gerente con sus proveedores asignados.
     */
    public function index()
    {
        // Obtiene el usuario autenticado (que es el Gerente)
        $gerente = Auth::user();

        // Carga los proveedores que este gerente administra a través de la relación
        $proveedores = $gerente->managedVendors;

        // Devuelve la vista del dashboard del gerente, pasándole los proveedores
        return view('content.gerente.dashboard', compact('proveedores'));
    }
}
