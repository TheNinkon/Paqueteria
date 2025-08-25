{{-- File: resources/views/content/admin/riders/index.blade.php --}}
@extends('layouts.layoutMaster')

@section('title', 'Gestión de Repartidores')

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h4 class="fw-bold py-3 mb-4">Gestión de Repartidores</h4>

      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Listado de Repartidores</h5>
          <a href="{{ route('admin.riders.create') }}" class="btn btn-primary">
            <i class="ti ti-user-plus me-sm-1"></i> Crear Repartidor
          </a>
        </div>

        <div class="card-body">
          <div class="table-responsive">
            <table class="table">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre Completo</th>
                  <th>Email</th>
                  <th>Teléfono</th>
                  <th>Estado</th>
                  <th>Fecha de Inicio</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                @forelse($riders as $rider)
                  <tr>
                    <td>{{ $rider->id }}</td>
                    <td>{{ $rider->full_name }}</td>
                    <td>{{ $rider->email }}</td>
                    <td>{{ $rider->phone ?? '—' }}</td>
                    <td>
                      <span class="badge bg-label-{{ $rider->status == 'active' ? 'success' : 'danger' }}">
                        {{ ucfirst($rider->status) }}
                      </span>
                    </td>
                    <td>{{ $rider->start_date?->format('d/m/Y') ?? '—' }}</td>
                    <td>
                      <div class="dropdown">
                        <button type="button" class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i
                            class="ti ti-dots-vertical"></i></button>
                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="{{ route('admin.riders.edit', $rider) }}"><i
                              class="ti ti-pencil me-1"></i> Editar</a>
                          <form action="{{ route('admin.riders.destroy', $rider) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item"><i class="ti ti-trash me-1"></i>
                              Eliminar</button>
                          </form>
                        </div>
                      </div>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="7" class="text-center text-muted">No hay repartidores registrados.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
          <div class="mt-3">
            {{ $riders->links() }}
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection
