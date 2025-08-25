{{-- File: resources/views/content/gerente/packages/modals/assign-single.blade.php --}}
<div class="modal fade" id="assignSingleModal" tabindex="-1" aria-labelledby="assignSingleModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="assignSingleModalLabel">Asignar Paquete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body">
        <form id="assign-single-package-form">
          <input type="hidden" name="packages[]" id="single-package-id">
          <p>Paquete: <strong id="single-package-code"></strong></p>
          <div class="mb-3">
            <label for="rider_id_single" class="form-label">Seleccionar Repartidor</label>
            <select class="form-select" id="rider_id_single" name="rider_id" required>
              <option value="">Selecciona un repartidor</option>
              @foreach ($riders as $rider)
                <option value="{{ $rider->id }}">{{ $rider->full_name }}</option>
              @endforeach
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="save-single-assignment-btn">Asignar</button>
      </div>
    </div>
  </div>
</div>
