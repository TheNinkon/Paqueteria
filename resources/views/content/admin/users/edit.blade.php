@extends('layouts/layoutMaster')
@section('title', 'Editar Usuario')
@section('content')
  <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Administrador / Usuarios /</span> Editar</h4>
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
          <label for="name" class="form-label">Nombre</label>
          <input type="text" class="form-control" id="name" name="name" value="{{ $user->name }}" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" value="{{ $user->email }}" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Nueva Contraseña</label>
          <input type="password" class="form-control" id="password" name="password">
          <small class="text-muted">Dejar en blanco para no cambiar la contraseña.</small>
        </div>
        <div class="mb-3">
          <label for="password_confirmation" class="form-label">Confirmar Nueva Contraseña</label>
          <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
        </div>
        <div class="mb-3">
          <label for="role" class="form-label">Rol</label>
          <select class="form-select" id="role" name="role" required>
            @foreach ($roles as $id => $role)
              <option value="{{ $role }}" @if ($userRole == $role) selected @endif>{{ $role }}
              </option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
      </form>
    </div>
  </div>
@endsection
