<div class="modal fade" id="addPackagesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-add-new-role">
    <div class="modal-content">
      <div class="modal-header bg-transparent">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pb-5 px-sm-4">
        <div class="text-center mb-2">
          <h3 class="mb-2">Ingresar nuevos paquetes</h3>
          <p class="text-muted">Añade paquetes nuevos al sistema.</p>
        </div>

        <form id="addPackagesForm" class="row g-3">
          @csrf
          <div class="col-12">
            <label class="form-label d-block" for="modalAddClient">Cliente Corporativo</label>
            <div class="input-group">
              <select id="modalAddClient" name="client_id" class="form-select select2" data-allow-clear="true" required>
                <option value="">Selecciona un cliente</option>
                @foreach ($clients as $client)
                  <option value="{{ $client->id }}">{{ $client->name }}</option>
                @endforeach
              </select>
            </div>
            <div id="statusMessageModal" class="mt-2"></div>
          </div>
          <div class="col-12">
            <label class="form-label" for="scanInputModal">Escanear o ingresar códigos de bulto</label>
            <input type="text" id="scanInputModal" class="form-control" placeholder="Escanea o escribe el código"
              autofocus />
          </div>
          <div class="col-12">
            <label class="form-label">Códigos a guardar:</label>
            <ul id="scannedCodesList" class="list-group"></ul>
          </div>
          <div class="col-12 text-center">
            <button type="submit" id="submitPackagesBtn" class="btn btn-primary me-sm-3 me-1">Guardar Paquetes</button>
            <button type="reset" class="btn btn-label-secondary" data-bs-dismiss="modal"
              aria-label="Close">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@push('page-script')
  <script>
    $(document).ready(function() {
      const addPackagesForm = $('#addPackagesForm');
      const scannedCodesList = $('#scannedCodesList');
      const scanInputModal = $('#scanInputModal');
      const submitPackagesBtn = $('#submitPackagesBtn');
      const clientSelectModal = $('#modalAddClient');
      const statusMessageModal = $('#statusMessageModal');
      let scannedCodes = [];

      // Reset al abrir
      $('#addPackagesModal').on('show.bs.modal', function() {
        scannedCodes = [];
        scannedCodesList.empty();
        statusMessageModal.empty();
        clientSelectModal.val('');
        scanInputModal.val('');
        setTimeout(() => scanInputModal.trigger('focus'), 200);
      });

      // Escaneo: detectar cliente automáticamente por patrón y apilar código
      scanInputModal.on('keypress', function(e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          const uniqueCode = scanInputModal.val().trim();
          if (!uniqueCode) return;
          if (scannedCodes.includes(uniqueCode)) {
            statusMessageModal.html('<div class="alert alert-warning">El código <strong>' + uniqueCode + '</strong> ya ha sido escaneado.</div>');
            scanInputModal.val('');
            return;
          }

          $.ajax({
            url: '{{ route('gerente.packages.validateCode') }}',
            method: 'POST',
            data: {
              unique_code: uniqueCode,
              _token: '{{ csrf_token() }}'
            },
            success: function(response) {
              if (response.success && response.client) {
                statusMessageModal.html('<div class="alert alert-success">Cliente identificado: <strong>' + response.client.name + '</strong></div>');
                clientSelectModal.val(response.client.id);
              } else {
                statusMessageModal.html('<div class="alert alert-warning">No se pudo identificar el cliente para <strong>' + uniqueCode + '</strong>. Selecciónalo manualmente.</div>');
              }
              scannedCodes.push(uniqueCode);
              scannedCodesList.append('<li class="list-group-item">' + uniqueCode + '</li>');
              scanInputModal.val('');
            },
            error: function(xhr) {
              let msg = 'Error al validar el código.';
              try {
                if (xhr.responseJSON && xhr.responseJSON.message) msg = xhr.responseJSON.message;
              } catch (e) {}
              statusMessageModal.html('<div class="alert alert-danger">' + msg + '</div>');
            }
          });
        }
      });

      addPackagesForm.on('submit', function(e) {
        e.preventDefault();

        const clientId = clientSelectModal.val();
        
        // Desactivar el botón para evitar envíos dobles
        submitPackagesBtn.prop('disabled', true).text('Guardando...');

        // Crear un objeto de datos para la solicitud AJAX
        const formData = {
          _token: $('input[name=_token]').val(),
          client_id: clientId,
          codes: scannedCodes
        };

        if (!formData.client_id || formData.codes.length === 0) {
          statusMessageModal.html('<div class="alert alert-danger">Selecciona un cliente y escanea al menos un código.</div>');
          submitPackagesBtn.prop('disabled', false).text('Guardar Paquetes');
          return;
        }

        $.ajax({
          url: '{{ route('gerente.packages.store') }}',
          method: 'POST',
          data: formData,
          success: function(response) {
            if (response.success) {
              Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: response.message,
                showConfirmButton: false,
                timer: 1500
              }).then(() => {
                window.location.reload();
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: response.message,
                showConfirmButton: true
              });
            }
          },
          error: function(xhr) {
            let errorMessage = 'Error interno del servidor al guardar los paquetes.';
            if (xhr.status === 422) {
              const errors = xhr.responseJSON.errors;
              errorMessage = '<ul>';
              for (const key in errors) {
                if (Object.hasOwnProperty.call(errors, key)) {
                  const element = errors[key];
                  element.forEach(msg => {
                    errorMessage += '<li>' + msg + '</li>';
                  });
                }
              }
              errorMessage += '</ul>';
            }
            Swal.fire({
              icon: 'error',
              title: 'Error de validación',
              html: errorMessage,
              showConfirmButton: true
            });
          },
          complete: function() {
            submitPackagesBtn.prop('disabled', false).text('Guardar Paquetes');
          }
        });
      });
    });
  </script>
@endpush
