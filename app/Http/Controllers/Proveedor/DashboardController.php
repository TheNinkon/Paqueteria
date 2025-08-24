<?php

// app/Http/Controllers/Proveedor/DashboardController.php
namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // resources/views/content/proveedor/dashboard.blade.php
        return view('content.proveedor.dashboard', [
            'title' => 'Panel del proveedor',
        ]);
    }
}
