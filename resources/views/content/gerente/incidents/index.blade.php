@extends('layouts/layoutMaster')

@php
  use App\Enums\PackageStatus;
@endphp

@section('title', 'Incidencias')

@section('content')
  <h4 class="fw-bold py-3 mb-4">Incidencias</h4>

  {{-- Filters --}}
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Filtros</h5>
      <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#collapseFilters"
        aria-expanded="false" aria-controls="collapseFilters">
        <i class="ti tabler-filter me-sm-1"></i>
        Filtros
      </button>
    </div>
    <div class="card-body collapse" id="collapseFilters">
      <form id="filter-form" action="{{ route('gerente.incidents.index') }}" method="GET">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="incident_type_id" class="form-label">Tipo de Incidencia</label>
            <select id="incident_type_id" name="incident_type_id" class="form-select">
              <option value="">Todos</option>
              @foreach ($incidentTypes as $type)
                <option value="{{ $type->id }}" {{ request('incident_type_id') == $type->id ? 'selected' : '' }}>
                  {{ $type->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Estado del Paquete</label>
            <select id="status" name="status" class="form-select">
              <option value="">Todos</option>
              @foreach (PackageStatus::cases() as $status)
                <option value="{{ $status->value }}" {{ request('status') == $status->value ? 'selected' : '' }}>
                  {{ $status->label() }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12 mt-4 text-end">
            <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
            <a href="{{ route('gerente.incidents.index') }}" class="btn btn-label-secondary">Limpiar</a>
          </div>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Listado de Incidencias</h5>
      <a href="{{ route('gerente.incidents.create') }}" class="btn btn-primary">
        <i class="ti tabler-plus me-sm-1"></i> Crear Incidencia
      </a>
    </div>

    <div class="card-body">
      <div class="table-responsive">
        <table class="table" id="incidents-table">
          <thead>
            <tr>
              <th>#</th>
              <th>Código de Bulto</th>
              <th>Cliente</th>
              <th>Tipo de Incidencia</th>
              <th>Estado del Paquete</th>
              <th>Fecha de Creación</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse($incidents as $incident)
              <tr>
                <td>{{ $incident->id }}</td>
                <td class="fw-medium">{{ $incident->package->unique_code }}</td>
                <td>{{ $incident->package->client->name ?? '—' }}</td>
                <td>{{ $incident->incidentType->name }}</td>
                <td>
                  @php
                    $statusClass = match ($incident->package->status->value) {
                        PackageStatus::RECEIVED->value => 'bg-label-warning',
                        PackageStatus::ASSIGNED->value => 'bg-label-info',
                        PackageStatus::IN_TRANSIT->value => 'bg-label-info',
                        PackageStatus::DELIVERED->value => 'bg-label-success',
                        PackageStatus::INCIDENT->value => 'bg-label-danger',
                        PackageStatus::RETURNED_TO_ORIGIN->value => 'bg-label-secondary',
                        PackageStatus::WAREHOUSE_RECEIVED->value => 'bg-label-info',
                        default => 'bg-label-secondary',
                    };
                  @endphp
                  <span class="badge {{ $statusClass }}">{{ ucfirst($incident->package->status->label()) }}</span>
                </td>
                <td>{{ $incident->created_at?->format('d/m/Y H:i') }}</td>
                <td>
                  {{-- Botón para resolver la incidencia --}}
                  <form action="{{ route('gerente.incidents.resolve', ['incident' => $incident->id]) }}" method="POST"
                    style="display:inline;"
                    onsubmit="return confirm('¿Estás seguro de que quieres resolver esta incidencia?');">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-sm btn-label-success">
                      <i class="ti tabler-check ti-sm"></i>
                    </button>
                  </form>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted">No hay incidencias registradas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
