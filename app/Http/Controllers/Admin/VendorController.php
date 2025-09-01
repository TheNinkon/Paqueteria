<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Vendor;
use Illuminate\Http\Request;

class VendorController extends Controller
{
    public function index()
    {
        $vendors = Vendor::orderBy('name')->paginate(15);
        return view('content.admin.vendors.index', compact('vendors'));
    }

    public function create()
    {
        return view('content.admin.vendors.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
        ]);
        Vendor::create($data);
        return redirect()->route('admin.proveedores.index')->with('success', 'Proveedor creado.');
    }

    public function show(Vendor $proveedore)
    {
        return view('content.admin.vendors.show', ['vendor' => $proveedore]);
    }

    public function edit(Vendor $proveedore)
    {
        return view('content.admin.vendors.edit', ['vendor' => $proveedore]);
    }

    public function update(Request $request, Vendor $proveedore)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:50',
            'contact_person' => 'nullable|string|max:255',
        ]);
        $proveedore->update($data);
        return redirect()->route('admin.proveedores.index')->with('success', 'Proveedor actualizado.');
    }

    public function destroy(Vendor $proveedore)
    {
        $proveedore->delete();
        return redirect()->route('admin.proveedores.index')->with('success', 'Proveedor eliminado.');
    }
}

