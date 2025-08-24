{{-- File: resources/views/content/gerente/packages/modals/add-packages.blade.php --}}
<div class="modal fade" id="addPackagesModal" tabindex="-1" aria-labelledby="addPackagesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <form id="add-packages-form" action="{{ route('gerente.packages.store') }}" method="POST" class="modal-content">
      @csrf

      <div class="modal-header">
        <h5 class="modal-title" id="addPackagesModalLabel">Ingresar nuevos paquetes</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>

      <div class="modal-body">
        <div class="mb-3">
          <label class="form-label" for="client-select-modal">Cliente Corporativo</label>
          <select id="client-select-modal" class="form-select" name="client_id" required>
            @foreach ($clients as $client)
              <option value="{{ $client->id }}">{{ $client->name }}</option>
            @endforeach
          </select>
        </div>

        <div class="mb-3">
          <label class="form-label" for="codes_text">Códigos de bulto (uno por línea)</label>
          <textarea id="codes_text" class="form-control" rows="8"
            placeholder="PE1234567890&#10;PE1234567891&#10;PE1234567892"></textarea>
          <small class="text-muted">Se crearán varios paquetes a la vez.</small>
        </div>

        {{-- Aquí se insertarán inputs hidden codes[] antes de enviar --}}
        <div id="codes-hidden" class="d-none"></div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary" id="save-all-packages">Guardar Paquetes</button>
      </div>
    </form>
  </div>
</div>

<script>
  (function() {
    const form = document.getElementById('add-packages-form');
    const textarea = document.getElementById('codes_text');
    const hiddenBox = document.getElementById('codes-hidden');

    form.addEventListener('submit', function(e) {
      hiddenBox.innerHTML = '';

      const lines = (textarea.value || '').split(/\r?\n/).map(s => s.trim()).filter(Boolean);
      if (!lines.length) {
        e.preventDefault();
        alert('Añade al menos un código.');
        return false;
      }

      for (const code of lines) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'codes[]';
        input.value = code;
        hiddenBox.appendChild(input);
      }
    });
  })();
</script>
