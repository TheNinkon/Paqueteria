{{-- File: resources/views/content/gerente/packages/index.blade.php --}}
@extends('layouts/layoutMaster')

@section('title', 'Gestión de Paquetes')

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h4 class="fw-bold py-3 mb-4">Gestión de Paquetes</h4>

      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Paquetes en la nave</h5>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPackagesModal">
            <i class="ti ti-plus me-sm-1"></i> Agregar Paquetes
          </button>
        </div>

        <div class="card-body">
          <div class="table-responsive">
            <table class="table" id="packages-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Código de Bulto</th>
                  <th>Envío ID</th>
                  <th>Cliente</th>
                  <th>Estado</th>
                  <th>Fecha</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                @forelse($packages as $p)
                  <tr>
                    <td>{{ $p->id }}</td>
                    <td class="fw-medium">{{ $p->unique_code }}</td>
                    <td>{{ $p->shipment_id }}</td>
                    <td>{{ $p->client->name ?? '—' }}</td>
                    <td>{{ $p->status }}</td>
                    <td>{{ $p->created_at?->format('d/m/Y H:i') }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted">Sin paquetes</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>

          @if (method_exists($packages, 'links'))
            <div class="mt-3">
              {{ $packages->links() }}
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>

  {{-- IMPORTANTE: ruta correcta del include con prefijo "content." --}}
  @include('content.gerente.packages.modals.add-packages')
@endsection
