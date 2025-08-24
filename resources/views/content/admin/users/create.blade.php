@extends('layouts/layoutMaster')
@section('title', 'Crear Usuario')
@section('content')
  <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Administrador / Usuarios /</span> Crear</h4>
  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('admin.users.store') }}">
        @csrf
        <div class="mb-3">
          <label for="name" class="form-label">Nombre</label>
          <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
          <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
          <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
        </div>
        <div class="mb-3">
          <label for="role" class="form-label">Rol</label>
          <select class="form-select" id="role" name="role" required>
            @foreach ($roles as $id => $role)
              <option value="{{ $role }}">{{ $role }}</option>
            @endforeach
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Crear Usuario</button>
      </form>
    </div>
  </div>
@endsection
