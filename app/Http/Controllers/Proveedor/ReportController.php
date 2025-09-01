<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;

class ReportController extends Controller
{
    public function index()
    {
        return view('content.proveedor.reports.index');
    }
}

