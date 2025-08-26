// resources/assets/js/gerente-packages.js
document.addEventListener('DOMContentLoaded', function () {
  const historyModal = document.getElementById('historyModal');

  historyModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;
    const packageId = button.getAttribute('data-bs-id');
    const historyList = document.getElementById('package-history-list');
    historyList.innerHTML =
      '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';

    fetch(`/gerente/packages/${packageId}/history`)
      .then(response => {
        if (!response.ok) {
          throw new Error('Error al cargar el historial del paquete.');
        }
        return response.json();
      })
      .then(data => {
        if (data.length > 0) {
          historyList.innerHTML = '';
          data.forEach(item => {
            const timelineItem = document.createElement('li');
            timelineItem.classList.add('timeline-item');
            timelineItem.innerHTML = `
                            <span class="timeline-point timeline-point-${item.color} timeline-point-indicator"></span>
                            <div class="timeline-event">
                                <div class="d-flex justify-content-between flex-sm-row flex-column mb-sm-0 mb-1">
                                    <h6>${item.status}</h6>
                                    <span class="timeline-event-time">${new Date(item.created_at).toLocaleString()}</span>
                                </div>
                                <p>${item.description}</p>
                                <div class="d-flex flex-wrap align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <img src="/assets/img/avatars/${item.user_avatar}" alt="Avatar" class="rounded-circle">
                                    </div>
                                    <h6 class="mb-0">${item.user_name}</h6>
                                </div>
                            </div>
                        `;
            historyList.appendChild(timelineItem);
          });
        } else {
          historyList.innerHTML =
            '<div class="alert alert-info" role="alert">No se encontró historial para este paquete.</div>';
        }
      })
      .catch(error => {
        console.error('Error:', error);
        historyList.innerHTML = `<div class="alert alert-danger" role="alert">Error: ${error.message}</div>`;
      });
  });
});
