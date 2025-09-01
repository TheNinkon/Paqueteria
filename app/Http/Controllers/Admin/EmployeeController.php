<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    public function index()
    {
        return view('content.admin.employees.index');
    }

    public function create()
    {
        return view('content.admin.employees.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('admin.empleados.index')->with('success', 'Empleado creado (stub).');
    }

    public function show($id)
    {
        return redirect()->route('admin.empleados.index');
    }

    public function edit($id)
    {
        return view('content.admin.employees.edit');
    }

    public function update(Request $request, $id)
    {
        return redirect()->route('admin.empleados.index')->with('success', 'Empleado actualizado (stub).');
    }

    public function destroy($id)
    {
        return redirect()->route('admin.empleados.index')->with('success', 'Empleado eliminado (stub).');
    }
}

