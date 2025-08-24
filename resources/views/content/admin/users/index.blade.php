{{-- resources/views/content/admin/users/index.blade.php --}}

@extends('layouts/layoutMaster')

@section('title', 'Gestión de Usuarios')

@section('content')
  <h4 class="fw-bold py-3 mb-4">
    <span class="text-muted fw-light">Administrador /</span> Gestión de Usuarios
  </h4>

  <div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="card-title mb-0">Listado de Usuarios</h5>
      <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="ti ti-plus me-1"></i> Nuevo Usuario
      </a>
    </div>
    <div class="card-body">
      <div class="table-responsive text-nowrap">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>ID</th>
              <th>Nombre</th>
              <th>Email</th>
              <th>Roles</th>
              <th>Acciones</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($users as $user)
              <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                  @foreach ($user->getRoleNames() as $role)
                    <span class="badge bg-label-primary me-1">{{ $role }}</span>
                  @endforeach
                </td>
                <td>
                  <a href="{{ route('admin.users.edit', $user->id) }}" class="btn btn-sm btn-icon"><i
                      class="ti ti-edit"></i></a>
                  <form action="{{ route('admin.users.destroy', $user->id) }}" method="POST"
                    style="display:inline-block;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-icon text-danger"
                      onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');"><i
                        class="ti ti-trash"></i></button>
                  </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection

@section('page-script')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const addPackagesModal = new bootstrap.Modal(document.getElementById('addPackagesModal'));
      const scanInputModal = document.getElementById('scan-input-modal');
      const clientSelectModal = document.getElementById('client-select-modal');
      const statusMessageModal = document.getElementById('modal-status-message');
      const packagesToSaveList = document.getElementById('packages-to-save');
      const saveButton = document.getElementById('save-all-packages');

      let scannedCodes = [];

      // Reiniciar el modal cada vez que se abre
      document.getElementById('addPackagesModal').addEventListener('show.bs.modal', function() {
        scannedCodes = [];
        packagesToSaveList.innerHTML = '';
        statusMessageModal.innerHTML = '';
        clientSelectModal.value = '';
        scanInputModal.focus();
      });

      // Lógica para el campo de escaneo
      scanInputModal.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          const uniqueCode = e.target.value.trim();
          if (uniqueCode && !scannedCodes.includes(uniqueCode)) {
            identifyClient(uniqueCode);
            scannedCodes.push(uniqueCode);
            const li = document.createElement('li');
            li.className = 'list-group-item';
            li.textContent = uniqueCode;
            packagesToSaveList.appendChild(li);
          } else if (scannedCodes.includes(uniqueCode)) {
            statusMessageModal.innerHTML =
              `<div class="alert alert-warning">El código **${uniqueCode}** ya ha sido escaneado.</div>`;
          }
          e.target.value = '';
        }
      });

      // Función para identificar el cliente
      function identifyClient(uniqueCode) {
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
              statusMessageModal.innerHTML =
                `<div class="alert alert-success">Cliente identificado: <strong>${data.client.name}</strong></div>`;
              clientSelectModal.value = data.client.id;
            } else {
              statusMessageModal.innerHTML =
                `<div class="alert alert-warning">No se pudo identificar al cliente para el código: <strong>${uniqueCode}</strong>. Por favor, selecciona uno manualmente.</div>`;
            }
          })
          .catch(error => {
            console.error('Error:', error);
            statusMessageModal.innerHTML =
              `<div class="alert alert-danger">Ocurrió un error al identificar el cliente.</div>`;
          });
      }

      // Lógica para el botón de guardar
      saveButton.addEventListener('click', function() {
        const clientId = clientSelectModal.value;
        if (scannedCodes.length === 0 || !clientId) {
          statusMessageModal.innerHTML =
            `<div class="alert alert-danger">Por favor, escanea al menos un paquete y selecciona un cliente.</div>`;
          return;
        }

        fetch('{{ route('gerente.packages.store') }}', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
              codes: scannedCodes,
              client_id: clientId
            })
          })
          .then(response => response.json())
          .then(data => {
            if (data.success) {
              statusMessageModal.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
              // Aquí podrías recargar la tabla o cerrar el modal
              addPackagesModal.hide();
            } else {
              statusMessageModal.innerHTML = `<div class="alert alert-danger">Error: ${data.message}</div>`;
            }
          })
          .catch(error => {
            console.error('Error:', error);
            statusMessageModal.innerHTML =
              `<div class="alert alert-danger">Ocurrió un error al guardar los paquetes.</div>`;
          });
      });
    });
  </script>
@endsection
