{{-- resources/views/content/admin/users/index.blade.php --}}

@extends('layouts/layoutMaster')

@section('title', 'Gestión de Usuarios')

@section('content')
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Administrador /</span> Gestión de Usuarios
  </h4>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Listado de Usuarios</h5>
      <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Nuevo Usuario
      </a>
    </div>
    <div class="card-body">
      <div class="table-responsive text-nowrap">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Roles</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($users as $user)
              <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  @foreach ($user->getRoleNames() as $role)
                    <span class="badge bg-label-primary me-1">{{ $role }}</span>
                  @endforeach
                </td>
                <td>
                  <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-icon"><i
                      class="ti ti-edit"></i></a>
                  <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                    style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-icon text-danger"
                      onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');"><i
                        class="ti ti-trash"></i></button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
