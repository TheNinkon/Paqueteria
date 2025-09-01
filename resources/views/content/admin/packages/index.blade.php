@extends('layouts/layoutMaster')

@section('title', 'Paquetes')

@section('content')
  <h4 class="fw-bold py-3 mb-4">Paquetes</h4>
  <div class="card"><div class="card-body">Listado de paquetes ({{ $packages->total() }})</div></div>
@endsection

