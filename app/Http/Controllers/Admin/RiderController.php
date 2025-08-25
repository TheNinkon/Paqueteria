<?php
// File: app/Http/Controllers/Admin/RiderController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rider;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class RiderController extends Controller
{
    /**
     * Muestra la lista de repartidores.
     */
    public function index(): View
    {
        $riders = Rider::latest()->paginate(15);
        return view('content.admin.riders.index', compact('riders'));
    }

    /**
     * Muestra el formulario para crear un nuevo repartidor.
     */
    public function create(): View
    {
        return view('content.admin.riders.create');
    }

    /**
     * Almacena un nuevo repartidor en la base de datos.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'required|email|unique:riders,email',
            'password' => 'required|string|min:8',
            'start_date' => 'nullable|date',
            'status' => 'required|string|in:active,inactive',
        ]);

        Rider::create($validated);

        return redirect()->route('admin.riders.index')->with('success', 'Repartidor creado correctamente.');
    }

    /**
     * Muestra la información de un repartidor específico.
     */
    public function show(Rider $rider): View
    {
        return view('content.admin.riders.show', compact('rider'));
    }

    /**
     * Muestra el formulario para editar un repartidor.
     */
    public function edit(Rider $rider): View
    {
        return view('content.admin.riders.edit', compact('rider'));
    }

    /**
     * Actualiza un repartidor en la base de datos.
     */
    public function update(Request $request, Rider $rider): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:255',
            'email' => 'required|email|unique:riders,email,' . $rider->id,
            'start_date' => 'nullable|date',
            'status' => 'required|string|in:active,inactive',
        ]);

        $rider->update($validated);

        return redirect()->route('admin.riders.index')->with('success', 'Repartidor actualizado correctamente.');
    }

    /**
     * Elimina un repartidor de la base de datos.
     */
    public function destroy(Rider $rider): RedirectResponse
    {
        $rider->delete();
        return redirect()->route('admin.riders.index')->with('success', 'Repartidor eliminado correctamente.');
    }
}
