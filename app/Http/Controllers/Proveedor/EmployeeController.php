<?php

namespace App\Http\Controllers\Proveedor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('content.proveedor.employees.index');
    }

    public function create()
    {
        return view('content.proveedor.employees.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('proveedor.repartidores.index')->with('success', 'Repartidor creado (stub).');
    }

    public function edit($id)
    {
        return view('content.proveedor.employees.edit');
    }
}

