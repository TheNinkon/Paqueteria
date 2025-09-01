@extends('layouts/layoutMaster')

@section('title', 'Proveedores')

@section('content')
  <h4 class="fw-bold py-3 mb-4">Proveedores</h4>
  <div class="card"><div class="card-body">Listado de proveedores ({{ $vendors->total() }})</div></div>
@endsection

