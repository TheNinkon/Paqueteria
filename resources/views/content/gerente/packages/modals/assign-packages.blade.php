{{-- File: resources/views/content/gerente/packages/modals/assign-packages.blade.php --}}
<div class="modal fade" id="assignPackagesModal" tabindex="-1" aria-labelledby="assignPackagesModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="assignPackagesModalLabel">Asignación Masiva de Paquetes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <form id="assign-packages-form">
          <div class="mb-3">
            <label for="rider_id_mass" class="form-label">Seleccionar Repartidor</label>
            <select class="form-select" id="rider_id_mass" name="rider_id" required>
              <option value="">Selecciona un repartidor</option>
              @foreach ($riders as $rider)
                <option value="{{ $rider->id }}">{{ $rider->full_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label for="scan_input_assign" class="form-label">Escanear códigos de bulto</label>
            <input type="text" class="form-control" id="scan_input_assign" placeholder="Escanea aquí" autofocus>
          </div>
          <div id="modal-status-assign" class="mt-3"></div>
          <div class="mt-4">
            <h6>Paquetes a asignar:</h6>
            <ul id="packages-to-assign-list" class="list-group"></ul>
          </div>
        </form>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="save-assignment-btn">Asignar Paquetes</button>
      </div>
    </div>
  </div>
</div>
