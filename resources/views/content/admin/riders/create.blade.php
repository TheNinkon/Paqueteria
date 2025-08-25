{{-- File: resources/views/content/admin/riders/create.blade.php --}}
@extends('layouts.layoutMaster')

@section('title', 'Crear Repartidor')

@section('content')
  <h4 class="fw-bold py-3 mb-4">Crear nuevo repartidor</h4>
  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.riders.store') }}" method="POST">
        @csrf
        <div class="mb-3">
          <label for="full_name" class="form-label">Nombre Completo</label>
          <input type="text" class="form-control" id="full_name" name="full_name" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="phone" class="form-label">Teléfono</label>
          <input type="text" class="form-control" id="phone" name="phone">
        </div>
        <div class="mb-3">
          <label for="password" class="form-label">Contraseña</label>
          <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="mb-3">
          <label for="start_date" class="form-label">Fecha de Inicio</label>
          <input type="date" class="form-control" id="start_date" name="start_date">
        </div>
        <div class="mb-3">
          <label for="status" class="form-label">Estado</label>
          <select class="form-select" id="status" name="status" required>
            <option value="active">Activo</option>
            <option value="inactive">Inactivo</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Guardar Repartidor</button>
      </form>
    </div>
  </div>
@endsection
