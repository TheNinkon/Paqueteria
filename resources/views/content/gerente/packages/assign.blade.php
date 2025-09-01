{{-- File: resources/views/content/gerente/packages/assign.blade.php --}}

{{-- Incluimos el modal de asignación masiva, pasando la lista de repartidores --}}
@include('content.gerente.packages.modals.assign-packages', compact('riders'))

{{-- Incluimos el modal de asignación individual, pasando la lista de repartidores --}}
@include('content.gerente.packages.modals.assign-single', compact('riders'))
@extends('layouts.contentNavbarLayout')

@section('title', 'Asignar Paquetes')

@section('content')
  <div class="row">
    <div class="col-md-12">
      <h4 class="fw-bold py-3 mb-4">Asignar Paquetes a Repartidores</h4>
      @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="mb-0">Paquetes no asignados</h5>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#assignPackagesModal">
            <i class="ti tabler-plus me-sm-1"></i> Asignar Paquetes
          </button>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table" id="unassigned-packages-table">
              <thead>
                <tr>
                  <th>Código de Bulto</th>
                  <th>Envío ID</th>
                  <th>Cliente</th>
                  <th>Fecha de Ingreso</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
                @forelse($unassignedPackages as $package)
                  <tr>
                    <td>{{ $package->unique_code }}</td>
                    <td>{{ $package->shipment_id }}</td>
                    <td>{{ $package->client->name ?? '—' }}</td>
                    <td>{{ $package->created_at?->format('d/m/Y H:i') }}</td>
                    <td>
                      <button class="btn btn-sm btn-label-primary assign-single-package-btn" data-bs-toggle="modal"
                        data-bs-target="#assignSingleModal" data-package-id="{{ $package->id }}"
                        data-package-code="{{ $package->unique_code }}">
                        Asignar
                      </button>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="5" class="text-center text-muted">No hay paquetes sin asignar.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Modales ya incluidos arriba --}}
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
            const assignPackagesModal = $('#assignPackagesModal');
            const scanInputAssign = $('#scan_input_assign');
            const riderSelectMass = $('#rider_id_mass');
            const packagesToAssignList = $('#packages_to_assign_list');
            const saveAssignmentBtn = $('#save-assignment-btn');
            const assignSingleModal = $('#assignSingleModal');
            const assignSingleBtn = $('#save-single-assignment-btn');
            const statusAssignMessage = $('#modal-status-assign');

            let scannedPackages = [];

            // Lógica para el modal de asignación masiva
            assignPackagesModal.on('show.bs.modal', function() {
              scannedPackages = [];
              packagesToAssignList.empty();
              scanInputAssign.val('');
              riderSelectMass.val('');
              statusAssignMessage.empty();
              scanInputAssign.focus();
            });

            scanInputAssign.keypress(function(e) {
              if (e.which === 13) {
                e.preventDefault();
                const uniqueCode = $(this).val().trim();

                if (uniqueCode && !scannedPackages.some(p => p.unique_code === uniqueCode)) {
                  const packageFound = @json($unassignedPackages).find(p => p.unique_code === uniqueCode);
                  if (packageFound) {
                    scannedPackages.push(packageFound);
                    packagesToAssignList.append($('<li class="list-group-item">').text(uniqueCode));
                  } else {
                    statusAssignMessage.html('<div class="alert alert-warning">El paquete ' + uniqueCode +
                      ' no está disponible para asignación.</div>');
                  }
                } else if (scannedCodes.some(p => p.unique_code === uniqueCode)) {
                  statusAssignMessage.html('<div class="alert alert-warning">El código ' + uniqueCode +
                    ' ya ha sido escaneado.</div>');
                }
                $(this).val('');
              }
            });

            saveAssignmentBtn.on('click', function() {
              const riderId = riderSelectMass.val();
              if (scannedPackages.length === 0 || !riderId) {
                statusAssignMessage.html(
                  '<div class="alert alert-danger">Por favor, escanea al menos un paquete y selecciona un repartidor.</div>'
                );
                return;
              }

              const packageIds = scannedPackages.map(p => p.id);

              $.ajax({
                url: '{{ route('gerente.packages.performAssignment') }}',
                method: 'POST',
                data: {
                  packages: packageIds,
                  rider_id: riderId,
                  _token: '{{ csrf_token() }}'
                },
                success: function(response) {
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
                },
                error: function(xhr) {
                  let errorMessage = 'Ocurrió un error al asignar los paquetes.';
                  try {
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                      errorMessage = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                    } else if (xhr.responseJSON && xhr.responseJSON.message) {
                      errorMessage = xhr.responseJSON.message;
                    }
                  } catch (e) {
                    console.error("Error parsing server response:", e);
                  }
                  statusAssignMessage.html('<div class="alert alert-danger">' + errorMessage + '</div>');
                }
              });
            });

            // Lógica para el modal de asignación individual
            $('.assign-single-package-btn').on('click', function() {
              const packageId = $(this).data('package-id');
              const packageCode = $(this).data('package-code');
              $('#single-package-id').val(packageId);
              $('#single-package-code').text(packageCode);
            });

            assignSingleBtn.on('click', function() {
              const packageId = $('#single-package-id').val();
              const riderId = $('#rider_id_single').val();

              if (!packageId || !riderId) {
                alert('Por favor, selecciona un repartidor.');
                return;
              }

              $.ajax({
                url: '{{ route('gerente.packages.performAssignment') }}',
                method: 'POST',
                data: {
                  packages: [packageId],
                  rider_id: riderId,
                  _token: '{{ csrf_token() }}'
                },
                success: function(response) {
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
                },
                error: function(xhr) {
                  alert('Ocurrió un error al asignar el paquete.');
                }
              });
            });
          });
        })(jQuery);
      } else {
        setTimeout(checkJQueryAndRun, 50);
      }
    }
    checkJQueryAndRun();
  </script>
@endsection
