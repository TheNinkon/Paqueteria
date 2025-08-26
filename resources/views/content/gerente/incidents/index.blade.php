{{-- File: resources/views/content/gerente/incidents/index.blade.php --}}
@extends('layouts.layoutMaster')

@section('title', 'Gestión de Incidencias')

@section('content')
  <h4 class="fw-bold py-3 mb-4">Gestión de Incidencias</h4>

  @if (session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Filtros de Incidencias</h5>
      <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#collapseFilters"
        aria-expanded="false" aria-controls="collapseFilters">
        <i class="ti ti-filter me-sm-1"></i>
        Filtros
      </button>
    </div>
    <div class="card-body collapse" id="collapseFilters">
      <form id="filter-form" action="{{ route('gerente.incidents.index') }}" method="GET">
        <div class="row g-3">
          <div class="col-md-6">
            <label for="incident_type_id" class="form-label">Tipo de Incidencia</label>
            <select id="incident_type_id" name="incident_type_id" class="form-select">
              <option value="">Todos</option>
              @foreach ($incidentTypes as $type)
                <option value="{{ $type->id }}" {{ request('incident_type_id') == $type->id ? 'selected' : '' }}>
                  {{ $type->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label for="status" class="form-label">Estado del Paquete</label>
            <select id="status" name="status" class="form-select">
              <option value="">Todos</option>
              @foreach ($packageStatuses as $status)
                <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                  {{ ucfirst($status) }}</option>
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
        <i class="ti ti-alert-triangle me-sm-1"></i> Crear Incidencia
      </a>
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
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @forelse($incidents as $incident)
              <tr>
                <td>{{ $incident->id }}</td>
                <td><a href="#">{{ $incident->package->unique_code ?? '—' }}</a></td>
                <td>{{ $incident->incidentType->name ?? '—' }}</td>
                <td>{{ $incident->notes ?? '—' }}</td>
                {{-- CORRECCIÓN: Usar la relación 'reporter' que apunta a 'users' --}}
                <td>{{ $incident->reporter->full_name ?? '—' }}</td>
                <td>{{ $incident->created_at?->format('d/m/Y H:i') }}</td>
                <td>
                  <button class="btn btn-sm btn-label-success resolve-incident-btn"
                    data-incident-id="{{ $incident->id }}">
                    <i class="ti ti-check me-1"></i> Resolver
                  </button>
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

@section('page-script')
  <script>
    // Se asegura de que este código se ejecute solo cuando el DOM y las librerías estén disponibles
    function checkAndRunScript() {
      if (typeof jQuery !== 'undefined') {
        (function($) {
          'use strict';

          $(document).ready(function() {
            $('.resolve-incident-btn').on('click', function() {
              const incidentId = $(this).data('incident-id');

              Swal.fire({
                title: '¿Estás seguro?',
                text: "¡No podrás revertir esto!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sí, resolver',
                cancelButtonText: 'Cancelar',
                customClass: {
                  confirmButton: 'btn btn-primary me-3',
                  cancelButton: 'btn btn-label-secondary'
                },
                buttonsStyling: false
              }).then(function(result) {
                if (result.value) {
                  $.ajax({
                    url: '/gerente/incidents/' + incidentId + '/resolve',
                    method: 'POST',
                    data: {
                      _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                      if (response.success) {
                        Swal.fire({
                          icon: 'success',
                          title: '¡Resuelta!',
                          text: response.message,
                          customClass: {
                            confirmButton: 'btn btn-success'
                          }
                        }).then(() => {
                          location.reload();
                        });
                      } else {
                        Swal.fire({
                          icon: 'error',
                          title: 'Error',
                          text: response.message,
                          customClass: {
                            confirmButton: 'btn btn-danger'
                          }
                        });
                      }
                    },
                    error: function(xhr) {
                      let errorMessage = 'Ocurrió un error al resolver la incidencia.';
                      if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                      }
                      Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: errorMessage,
                        customClass: {
                          confirmButton: 'btn btn-danger'
                        }
                      });
                    }
                  });
                }
              });
            });
          });
        })(jQuery);
      } else {
        setTimeout(checkAndRunScript, 50);
      }
    }
    checkAndRunScript();
  </script>
@endsection
