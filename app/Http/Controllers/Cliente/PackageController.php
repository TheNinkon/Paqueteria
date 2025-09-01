<?php

namespace App\Http\Controllers\Cliente;

use App\Http\Controllers\Controller;

class PackageController extends Controller
{
    public function index()
    {
        return view('content.cliente.packages.index');
    }

    public function reports()
    {
        return view('content.cliente.reports.index');
    }
}

