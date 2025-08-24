{{-- File: resources/views/gerente/packages/ingestion.blade.php --}}

@extends('layouts/layoutMaster')

@section('title', 'Ingreso de Paquetes')

@section('content')
  <div class="row">
    <div class="col-md-8">
      <h4 class="fw-bold py-3 mb-4">Ingreso de Paquetes</h4>
      <div class="card mb-4">
        <div class="card-header">
          <h5 class="mb-0">Registrar paquetes por escáner</h5>
        </div>
        <div class="card-body">
          <form id="package-scan-form" class="mb-3">
            <div class="mb-3">
              <label class="form-label" for="scan-input">Código de bulto</label>
              <input type="text" class="form-control" id="scan-input" placeholder="Escanea el código de bulto"
                autofocus>
            </div>
            <div class="mb-3">
              <label class="form-label" for="client-select">Cliente Corporativo</label>
              <select id="client-select" class="form-select">
                <option value="">Identificando cliente...</option>
                @foreach ($clients as $client)
                  <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
              </select>
            </div>
            <div id="status-message" class="mt-3">
            </div>
          </form>
          <button type="button" id="save-scanned-packages" class="btn btn-primary mt-3">Guardar Paquete</button>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('page-script')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const scanInput = document.getElementById('scan-input');
      const clientSelect = document.getElementById('client-select');
      const statusMessageDiv = document.getElementById('status-message');

      scanInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          const uniqueCode = e.target.value.trim();
          if (uniqueCode) {
            identifyClient(uniqueCode);
            e.target.value = '';
          }
        }
      });

      function identifyClient(uniqueCode) {
        statusMessageDiv.innerHTML = '<div class="alert alert-primary">Identificando cliente...</div>';
        clientSelect.value = '';

        fetch('{{ route('api.clients.identify') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              unique_code: uniqueCode
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success && data.client) {
              statusMessageDiv.innerHTML =
                `<div class="alert alert-success">Cliente identificado: <strong>${data.client.name}</strong></div>`;
              clientSelect.value = data.client.id;
            } else {
              statusMessageDiv.innerHTML =
                `<div class="alert alert-warning">No se pudo identificar al cliente para el código: <strong>${uniqueCode}</strong>. Por favor, selecciona uno manualmente.</div>`;
              clientSelect.value = '';
            }
          })
          .catch(error => {
            console.error('Error:', error);
            statusMessageDiv.innerHTML =
              `<div class="alert alert-danger">Ocurrió un error al identificar el cliente.</div>`;
          });
      }
    });
  </script>
@endsection
