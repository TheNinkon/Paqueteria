{{-- File: resources/views/content/admin/riders/edit.blade.php --}}
@extends('layouts.layoutMaster')

@section('title', 'Editar Repartidor')

@section('content')
  <h4 class="fw-bold py-3 mb-4">Editar Repartidor: {{ $rider->full_name }}</h4>
  <div class="card">
    <div class="card-body">
      <form action="{{ route('admin.riders.update', $rider) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
          <label for="full_name" class="form-label">Nombre Completo</label>
          <input type="text" class="form-control" id="full_name" name="full_name" value="{{ $rider->full_name }}"
            required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email</label>
          <input type="email" class="form-control" id="email" name="email" value="{{ $rider->email }}" required>
        </div>
        <div class="mb-3">
          <label for="phone" class="form-label">Teléfono</label>
          <input type="text" class="form-control" id="phone" name="phone" value="{{ $rider->phone }}">
        </div>
        <div class="mb-3">
          <label for="start_date" class="form-label">Fecha de Inicio</label>
          <input type="date" class="form-control" id="start_date" name="start_date"
            value="{{ $rider->start_date?->format('Y-m-d') }}">
        </div>
        <div class="mb-3">
          <label for="status" class="form-label">Estado</label>
          <select class="form-select" id="status" name="status" required>
            <option value="active" {{ $rider->status === 'active' ? 'selected' : '' }}>Activo</option>
            <option value="inactive" {{ $rider->status === 'inactive' ? 'selected' : '' }}>Inactivo</option>
          </select>
        </div>
        <button type="submit" class="btn btn-primary">Actualizar Repartidor</button>
      </form>
    </div>
  </div>
@endsection
