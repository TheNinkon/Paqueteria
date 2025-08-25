{{-- File: resources/views/content/gerente/packages/index.blade.php --}}
@extends('layouts/layoutMaster')

@section('title', 'Gestión de Paquetes')

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h4 class="fw-bold py-3 mb-4">Gestión de Paquetes</h4>

      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Paquetes en la nave</h5>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPackagesModal">
            <i class="ti ti-plus me-sm-1"></i> Agregar Paquetes
          </button>
        </div>

        <div class="card-body">
          <div class="table-responsive">
            <table class="table" id="packages-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Código de Bulto</th>
                  <th>Envío ID</th>
                  <th>Cliente</th>
                  <th>Estado</th>
                  <th>Fecha</th>
                </tr>
              </thead>
              <tbody class="table-border-bottom-0">
                @forelse($packages as $p)
                  <tr>
                    <td>{{ $p->id }}</td>
                    <td class="fw-medium">{{ $p->unique_code }}</td>
                    <td>{{ $p->shipment_id }}</td>
                    <td>{{ $p->client->name ?? '—' }}</td>
                    <td>{{ $p->status }}</td>
                    <td>{{ $p->created_at?->format('d/m/Y H:i') }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted">Sin paquetes</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  @include('content.gerente.packages.modals.add-packages')
@endsection

@section('page-script')
  <script>
    // Este script espera hasta que jQuery esté disponible.
    // Esto resuelve problemas con el orden de carga en la plantilla.
    function checkJQueryAndRun() {
      if (typeof jQuery !== 'undefined') {
        (function($) {
          'use strict';

          $(document).ready(function() {
            const addPackagesModal = $('#addPackagesModal');
            const scanInputModal = $('#scan-input-modal');
            const clientSelectModal = $('#client-select-modal');
            const statusMessageModal = $('#modal-status-message');
            const packagesToSaveList = $('#packages-to-save');
            const saveButton = $('#save-all-packages');

            let scannedCodes = [];

            addPackagesModal.on('show.bs.modal', function() {
              scannedCodes = [];
              packagesToSaveList.empty();
              statusMessageModal.empty();
              clientSelectModal.val('');
              scanInputModal.val('');
              scanInputModal.focus();
            });

            // Lógica para el campo de escaneo
            scanInputModal.keypress(function(e) {
              if (e.which === 13) {
                e.preventDefault();
                const uniqueCode = scanInputModal.val().trim();

                if (uniqueCode && !scannedCodes.includes(uniqueCode)) {
                  $.ajax({
                    url: '{{ route('api.clients.identify') }}',
                    method: 'POST',
                    data: {
                      unique_code: uniqueCode,
                      _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                      if (response.success && response.client) {
                        statusMessageModal.html(
                          '<div class="alert alert-success">Cliente identificado: <strong>' + response
                          .client.name + '</strong></div>');
                        clientSelectModal.val(response.client.id);
                      } else {
                        statusMessageModal.html(
                          '<div class="alert alert-warning">No se pudo identificar al cliente para el código: <strong>' +
                          uniqueCode + '</strong>. Por favor, selecciona uno.</div>');
                      }
                    },
                    error: function(xhr) {
                      let errorMessage = 'Ocurrió un error al identificar el cliente.';
                      try {
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                          errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                          errorMessage = xhr.responseJSON.message;
                        }
                      } catch (e) {
                        console.error("Error parsing server response:", e);
                      }
                      statusMessageModal.html('<div class="alert alert-danger">' + errorMessage +
                        '</div>');
                    }
                  });

                  scannedCodes.push(uniqueCode);
                  const listItem = $('<li>').addClass('list-group-item').text(uniqueCode);
                  packagesToSaveList.append(listItem);
                } else if (scannedCodes.includes(uniqueCode)) {
                  statusMessageModal.html('<div class="alert alert-warning">El código <strong>' + uniqueCode +
                    '</strong> ya ha sido escaneado.</div>');
                }
                scanInputModal.val('');
              }
            });

            // Lógica para el botón de guardar
            saveButton.on('click', function() {
              const clientId = clientSelectModal.val();
              if (scannedCodes.length === 0 || !clientId) {
                statusMessageModal.html(
                  '<div class="alert alert-danger">Por favor, escanea al menos un paquete y selecciona un cliente.</div>'
                  );
                return;
              }

              $.ajax({
                url: '{{ route('gerente.packages.store') }}',
                method: 'POST',
                data: {
                  codes: scannedCodes,
                  client_id: clientId,
                  _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                  if (response.success) {
                    if (typeof Swal !== 'undefined') {
                      Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: response.message,
                        customClass: {
                          confirmButton: 'btn btn-success'
                        }
                      }).then(() => {
                        location.reload();
                      });
                    } else {
                      alert(response.message);
                      location.reload();
                    }
                    addPackagesModal.hide();
                  } else {
                    statusMessageModal.html('<div class="alert alert-danger">Error: ' + response.message +
                      '</div>');
                  }
                },
                error: function(xhr) {
                  let errorMessage = 'Ocurrió un error al guardar los paquetes.';
                  try {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                      errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                      errorMessage = xhr.responseJSON.message;
                    }
                  } catch (e) {
                    console.error("Error parsing server response:", e);
                  }
                  statusMessageModal.html('<div class="alert alert-danger">' + errorMessage + '</div>');
                }
              });
            });
          });
        })(jQuery);
      } else {
        // Si jQuery no está disponible, esperamos un poco y lo intentamos de nuevo.
        setTimeout(checkJQueryAndRun, 100);
      }
    }
    checkJQueryAndRun();
  </script>
@endsection
