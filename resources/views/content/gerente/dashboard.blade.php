@extends('layouts/layoutMaster')

@section('title', 'Dashboard de Gerente')

@section('content')
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Dashboard /</span> Gerente
  </h4>

  <div class="card">
    <div class="card-header">
      <h5 class="card-title">Proveedores a mi cargo</h5>
    </div>
    <div class="card-body">
      @if ($proveedores->isEmpty())
        <p>No tienes proveedores asignados en este momento.</p>
      @else
        <ul class="list-group">
          @foreach ($proveedores as $proveedor)
            <li class="list-group-item">{{ $proveedor->name }} ({{ $proveedor->email }})</li>
          @endforeach
        </ul>
      @endif
    </div>
  </div>
@endsection
