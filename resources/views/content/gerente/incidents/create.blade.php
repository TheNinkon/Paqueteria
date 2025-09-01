{{-- File: resources/views/content/gerente/incidents/create.blade.php --}}
@extends('layouts.contentNavbarLayout')

@section('title', 'Crear Incidencia')

@section('content')
  <h4 class="fw-bold py-3 mb-4">Crear Incidencia</h4>
  <div class="card">
    <div class="card-body">
      <form id="create-incident-form">
        @csrf
        <div class="mb-3">
          <label for="unique_code" class="form-label">Escanear o ingresar Código de Bulto</label>
          <input type="text" class="form-control" id="unique_code" placeholder="Escanea el código del paquete" autofocus>
        </div>

        <div id="package-info" class="mb-3" style="display: none;">
          <h5>Paquete Encontrado</h5>
          <div class="alert" id="package-status-alert"></div>
          <p><strong>Código de Bulto:</strong> <span id="package-code-display"></span></p>
          <p><strong>Envío ID:</strong> <span id="shipment-id-display"></span></p>
          <p><strong>Cliente:</strong> <span id="client-name-display"></span></p>
          <input type="hidden" name="package_id" id="package-id-input">
        </div>

        <div class="mb-3" id="incident-form-details" style="display: none;">
          <label for="incident_type_id" class="form-label">Tipo de Incidencia</label>
          <select class="form-select" id="incident_type_id" name="incident_type_id" required>
            <option value="">Selecciona un tipo de incidencia</option>
            @foreach ($incidentTypes as $type)
              <option value="{{ $type->id }}">{{ $type->name }}</option>
            @endforeach
          </select>
          <div class="mb-3 mt-3">
            <label for="notes" class="form-label">Notas Adicionales</label>
            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
          </div>
        </div>

        <div id="form-messages" class="mt-3"></div>

        <button type="button" class="btn btn-primary" id="save-incident-btn" style="display: none;">Guardar
          Incidencia</button>
      </form>
    </div>
  </div>
@endsection

@section('page-script')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const uniqueCodeInput = document.getElementById('unique_code');
      const packageInfo = document.getElementById('package-info');
      const packageIdInput = document.getElementById('package-id-input');
      const incidentFormDetails = document.getElementById('incident-form-details');
      const saveIncidentBtn = document.getElementById('save-incident-btn');
      const formMessages = document.getElementById('form-messages');
      const packageCodeDisplay = document.getElementById('package-code-display');
      const shipmentIdDisplay = document.getElementById('shipment-id-display');
      const clientNameDisplay = document.getElementById('client-name-display');
      const packageStatusAlert = document.getElementById('package-status-alert');

      uniqueCodeInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          const uniqueCode = uniqueCodeInput.value.trim();

          if (uniqueCode) {
            formMessages.innerHTML = `<div class="alert alert-info">Buscando paquete...</div>`;

            fetch('{{ route('gerente.packages.find') }}', {
                method: 'POST',
                headers: {
                  'Content-Type': 'application/json',
                  'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                  unique_code: uniqueCode
                })
              })
              .then(response => {
                if (!response.ok) {
                  return response.json().then(error => {
                    throw error;
                  });
                }
                return response.json();
              })
              .then(data => {
                if (data.success && data.package) {
                  packageCodeDisplay.textContent = data.package.unique_code;
                  shipmentIdDisplay.textContent = data.package.shipment_id;
                  clientNameDisplay.textContent = data.package.client ? data.package.client.name : '—';
                  packageIdInput.value = data.package.id;
                  packageStatusAlert.textContent = `Estado actual: ${data.package.status}`;
                  packageStatusAlert.className = 'alert ' + (data.package.status === 'delivered' ?
                    'alert-success' : 'alert-warning');

                  packageInfo.style.display = 'block';
                  incidentFormDetails.style.display = 'block';
                  saveIncidentBtn.style.display = 'block';
                  formMessages.innerHTML = '';
                } else {
                  formMessages.innerHTML =
                    `<div class="alert alert-warning">Paquete no encontrado o ya tiene una incidencia.</div>`;
                  packageInfo.style.display = 'none';
                  incidentFormDetails.style.display = 'none';
                  saveIncidentBtn.style.display = 'none';
                }
              })
              .catch(error => {
                console.error('Error:', error);
                formMessages.innerHTML = `<div class="alert alert-danger">Error: ${error.message}</div>`;
                packageInfo.style.display = 'none';
                incidentFormDetails.style.display = 'none';
                saveIncidentBtn.style.display = 'none';
              });
          }
        }
      });

      saveIncidentBtn.addEventListener('click', function() {
        const packageId = packageIdInput.value;
        const incidentTypeId = document.getElementById('incident_type_id').value;
        const notes = document.getElementById('notes').value;

        if (!packageId || !incidentTypeId) {
          formMessages.innerHTML =
            `<div class="alert alert-danger">Debes seleccionar un tipo de incidencia.</div>`;
          return;
        }

        fetch('{{ route('gerente.incidents.store') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              package_id: packageId,
              incident_type_id: incidentTypeId,
              notes: notes
            })
          })
          .then(response => {
            if (!response.ok) {
              return response.json().then(error => {
                throw error;
              });
            }
            return response.json();
          })
          .then(data => {
            if (data.success) {
              if (typeof Swal !== 'undefined') {
                Swal.fire({
                  icon: 'success',
                  title: '¡Éxito!',
                  text: data.message,
                  customClass: {
                    confirmButton: 'btn btn-success'
                  }
                }).then(() => {
                  window.location.href = '{{ route('gerente.incidents.index') }}';
                });
              } else {
                alert(data.message);
                window.location.href = '{{ route('gerente.incidents.index') }}';
              }
            } else {
              formMessages.innerHTML = `<div class="alert alert-danger">Error: ${data.message}</div>`;
            }
          })
          .catch(error => {
            let errorMessage = 'Ocurrió un error al guardar la incidencia.';
            console.error('Error:', error);
            if (error.message) {
              errorMessage = error.message;
            }
            formMessages.innerHTML = `<div class="alert alert-danger">${errorMessage}</div>`;
          });
      });
    });
  </script>
@endsection
