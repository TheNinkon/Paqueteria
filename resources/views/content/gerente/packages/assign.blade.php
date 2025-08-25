@extends('layouts.layoutMaster')

@section('title', 'Asignar Paquetes')

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h4 class="fw-bold py-3 mb-4">Asignar Paquetes a Repartidores</h4>
      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Paquetes no asignados</h5>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignPackagesModal">
            <i class="ti ti-truck me-sm-1"></i> Asignar Paquetes
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table" id="unassigned-packages-table">
              <thead>
                <tr>
                  <th>Código de Bulto</th>
                  <th>Envío ID</th>
                  <th>Cliente</th>
                  <th>Fecha de Ingreso</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($unassignedPackages as $package)
                  <tr>
                    <td>{{ $package->unique_code }}</td>
                    <td>{{ $package->shipment_id }}</td>
                    <td>{{ $package->client->name ?? '—' }}</td>
                    <td>{{ $package->created_at?->format('d/m/Y H:i') }}</td>
                    <td>
                      <button class="btn btn-sm btn-label-primary assign-single-package-btn" data-bs-toggle="modal"
                        data-bs-target="#assignSingleModal" data-package-id="{{ $package->id }}"
                        data-package-code="{{ $package->unique_code }}">
                        Asignar
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted">No hay paquetes sin asignar.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('content.gerente.packages.modals.assign-packages')
  @include('content.gerente.packages.modals.assign-single')
@endsection
