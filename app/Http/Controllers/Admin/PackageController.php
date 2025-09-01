<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::with('client')->orderByDesc('created_at')->paginate(15);
        return view('content.admin.packages.index', compact('packages'));
    }

    public function create()
    {
        return view('content.admin.packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'unique_code' => 'required|string|max:255|unique:packages,unique_code',
        ]);
        Package::create([
            'unique_code' => $request->input('unique_code'),
        ]);
        return redirect()->route('admin.paquetes.index')->with('success', 'Paquete creado.');
    }

    public function show(Package $paquete)
    {
        return view('content.admin.packages.show', ['package' => $paquete]);
    }

    public function edit(Package $paquete)
    {
        return view('content.admin.packages.edit', ['package' => $paquete]);
    }

    public function update(Request $request, Package $paquete)
    {
        $request->validate([
            'unique_code' => 'required|string|max:255|unique:packages,unique_code,' . $paquete->id,
        ]);
        $paquete->update([
            'unique_code' => $request->input('unique_code'),
        ]);
        return redirect()->route('admin.paquetes.index')->with('success', 'Paquete actualizado.');
    }

    public function destroy(Package $paquete)
    {
        $paquete->delete();
        return redirect()->route('admin.paquetes.index')->with('success', 'Paquete eliminado.');
    }

    public function history(Package $package)
    {
        $history = $package->history()->orderBy('created_at', 'asc')->get();
        return response()->json($history);
    }
}

