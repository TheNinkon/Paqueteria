{{-- File: resources/views/content/gerente/packages/modals/add-packages.blade.php --}}
<div class="modal fade" id="addPackagesModal" tabindex="-1" aria-labelledby="addPackagesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="addPackagesModalLabel">Ingresar nuevos paquetes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <form id="add-packages-form">
          <div class="mb-3">
            <label class="form-label" for="client-select-modal">Cliente Corporativo</label>
            <select id="client-select-modal" class="form-select" name="client_id" required>
              <option value="">Identificando cliente...</option>
              @foreach ($clients as $client)
                <option value="{{ $client->id }}">{{ $client->name }}</option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label" for="scan-input-modal">Escanear o ingresar códigos de bulto</label>
            <input type="text" class="form-control" id="scan-input-modal" placeholder="Escanea aquí" autofocus>
          </div>

          <div id="modal-status-message" class="mt-3"></div>

          <div class="mt-4">
            <h6>Códigos a guardar:</h6>
            <ul id="packages-to-save" class="list-group"></ul>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="save-all-packages">Guardar Paquetes</button>
      </div>
    </div>
  </div>
</div>
