{{-- File: resources/views/content/gerente/packages/index.blade.php --}}
@extends('layouts/layoutMaster')

@section('title', 'Gestión de Paquetes')

@section('content')
  <h4 class="fw-bold py-3 mb-4">Gestión de Paquetes</h4>

  {{-- Resto del contenido del dashboard (KPIs, etc.) --}}
  <div class="row g-4 mb-4">
    <div class="col-sm-6 col-xl-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span>Paquetes Totales</span>
              <div class="d-flex align-items-end mt-2">
                <h4 class="mb-0 me-2">{{ $kpis['total'] }}</h4>
              </div>
              <small class="text-muted">Total de paquetes en el sistema</small>
            </div>
            <span class="badge bg-label-primary rounded p-2">
              <i class="ti ti-box ti-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span>Recibidos</span>
              <div class="d-flex align-items-end mt-2">
                <h4 class="mb-0 me-2">{{ $kpis['received'] }}</h4>
              </div>
              <small class="text-muted">Paquetes en la nave</small>
            </div>
            <span class="badge bg-label-warning rounded p-2">
              <i class="ti ti-package-import ti-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span>Asignados</span>
              <div class="d-flex align-items-end mt-2">
                <h4 class="mb-0 me-2">{{ $kpis['assigned'] }}</h4>
              </div>
              <small class="text-muted">Paquetes en reparto</small>
            </div>
            <span class="badge bg-label-info rounded p-2">
              <i class="ti ti-truck-delivery ti-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
    <div class="col-sm-6 col-xl-3">
      <div class="card">
        <div class="card-body">
          <div class="d-flex align-items-start justify-content-between">
            <div class="content-left">
              <span>Entregados</span>
              <div class="d-flex align-items-end mt-2">
                <h4 class="mb-0 me-2">{{ $kpis['delivered'] }}</h4>
              </div>
              <small class="text-muted">Paquetes entregados con éxito</small>
            </div>
            <span class="badge bg-label-success rounded p-2">
              <i class="ti ti-circle-check ti-sm"></i>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Resto del contenido (Filtros, etc.) --}}
  <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Filtros</h5>
      <button type="button" class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#collapseFilters"
        aria-expanded="false" aria-controls="collapseFilters">
        <i class="ti ti-filter me-sm-1"></i>
        Filtros
      </button>
    </div>
    <div class="card-body collapse" id="collapseFilters">
      <form id="filter-form" action="{{ route('gerente.packages.index') }}" method="GET">
        <div class="row g-3">
          <div class="col-md-4">
            <label for="search" class="form-label">Buscar Código/Envío</label>
            <input type="text" id="search" name="search" class="form-control" placeholder="Buscar..."
              value="{{ request('search') }}">
          </div>
          <div class="col-md-4">
            <label for="client_id" class="form-label">Cliente</label>
            <select id="client_id" name="client_id" class="form-select">
              <option value="">Todos</option>
              @foreach ($clients as $client)
                <option value="{{ $client->id }}" {{ request('client_id') == $client->id ? 'selected' : '' }}>
                  {{ $client->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
            <label for="status" class="form-label">Estado</label>
            <select id="status" name="status" class="form-select">
              <option value="">Todos</option>
              <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>Recibido</option>
              <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Asignado</option>
              <option value="in_delivery" {{ request('status') == 'in_delivery' ? 'selected' : '' }}>En Reparto</option>
              <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Entregado</option>
              <option value="incident" {{ request('status') == 'incident' ? 'selected' : '' }}>Incidencia</option>
            </select>
          </div>
          <div class="col-12 mt-4 text-end">
            <button type="submit" class="btn btn-primary">Aplicar Filtros</button>
            <a href="{{ route('gerente.packages.index') }}" class="btn btn-label-secondary">Limpiar</a>
          </div>
        </div>
      </form>
    </div>
  </div>

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
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @forelse($packages as $p)
              <tr>
                <td>{{ $p->id }}</td>
                <td class="fw-medium">{{ $p->unique_code }}</td>
                <td>{{ $p->shipment_id }}</td>
                <td>{{ $p->client->name ?? '—' }}</td>
                <td>
                  @php
                    $statusClass =
                        [
                            'received' => 'bg-label-warning',
                            'assigned' => 'bg-label-info',
                            'in_delivery' => 'bg-label-info',
                            'delivered' => 'bg-label-success',
                            'incident' => 'bg-label-danger',
                        ][$p->status] ?? 'bg-label-secondary';
                  @endphp
                  <span class="badge {{ $statusClass }}">{{ ucfirst($p->status) }}</span>
                </td>
                <td>{{ $p->created_at?->format('d/m/Y H:i') }}</td>
                <td>
                  <a href="#" class="btn btn-sm btn-label-secondary view-history-btn" data-bs-toggle="modal"
                    data-bs-target="#historyModal" data-bs-id="{{ $p->id }}">
                    <i class="ti ti-history ti-sm"></i>
                  </a>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="7" class="text-center text-muted">Sin paquetes</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>

  @include('content.gerente.packages.modals.add-packages', compact('clients'))
  @include('content.gerente.packages.modals.history-modal')
@endsection

@section('page-script')
  <script>
    // Este script espera hasta que jQuery esté disponible.
    function checkAndRunScript() {
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

            // Lógica para el historial
            $('#historyModal').on('show.bs.modal', function(event) {
              const button = $(event.relatedTarget);
              const packageId = button.data('bs-id');
              const historyList = $('#package-history-list');

              historyList.html('<p class="text-center text-muted">Cargando historial...</p>');

              $.ajax({
                url: '{{ route('gerente.packages.history', ['package' => ':packageId']) }}'.replace(
                  ':packageId', packageId),
                method: 'GET',
                success: function(response) {
                  historyList.empty();
                  if (response.length > 0) {
                    response.forEach(function(history) {
                      const listItem = `
                        <li class="timeline-item">
                            <span class="timeline-point timeline-point-${history.color}"></span>
                            <div class="timeline-event">
                                <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                    <h6>${history.status}</h6>
                                    <span class="timeline-event-time">${history.created_at}</span>
                                </div>
                                <p class="mb-0">${history.description}</p>
                                ${history.extra_info ? `<span class="badge bg-label-secondary mt-2">${history.extra_info}</span>` : ''}
                            </div>
                        </li>`;
                      historyList.append(listItem);
                    });
                  } else {
                    historyList.append(
                      '<p class="text-center text-muted">No hay historial para este paquete.</p>');
                  }
                },
                error: function() {
                  historyList.html(
                    '<p class="text-center text-danger">Error al cargar el historial del paquete.</p>');
                }
              });
            });
          });
        })(jQuery);
      } else {
        setTimeout(checkAndRunScript, 50);
      }
    }
    checkAndRunScript();
  </script>
@endsection
