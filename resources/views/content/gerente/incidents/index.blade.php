{{-- File: resources/views/content/gerente/incidents/index.blade.php --}}
@extends('layouts.layoutMaster')

@section('title', 'Gestión de Incidencias')

@section('content')
  <h4 class="fw-bold py-3 mb-4">Gestión de Incidencias</h4>
  <div class="card">
    <div class="card-header">
      <h5 class="mb-0">Listado de Incidencias</h5>
    </div>
    <div class="card-body">
      <div class="table-responsive">
        <table class="table">
          <thead>
            <tr>
              <th>#</th>
              <th>Paquete</th>
              <th>Tipo</th>
              <th>Notas</th>
              <th>Reportado por</th>
              <th>Fecha</th>
            </tr>
          </thead>
          <tbody>
            @forelse($incidents as $incident)
              <tr>
                <td>{{ $incident->id }}</td>
                <td><a href="#">{{ $incident->package->unique_code ?? '—' }}</a></td>
                <td>{{ $incident->type }}</td>
                <td>{{ $incident->notes ?? '—' }}</td>
                <td>{{ $incident->rider->full_name ?? '—' }}</td>
                <td>{{ $incident->created_at?->format('d/m/Y H:i') }}</td>
              </tr>
            @empty
              <tr>
                <td colspan="6" class="text-center text-muted">No hay incidencias registradas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
