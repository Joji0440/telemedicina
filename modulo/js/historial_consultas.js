document.addEventListener('DOMContentLoaded', function() {
  const historialContainer = document.getElementById('historial').getElementsByTagName('tbody')[0];
  const clearHistorialButton = document.getElementById('clearHistorialButton');

  fetch('../php/historial_consultas.php')
    .then(response => response.json())
    .then(data => {
      data.forEach(cita => {
        const row = historialContainer.insertRow();
        const fechaCell = row.insertCell();
        const horaCell = row.insertCell();
        const doctorCell = row.insertCell();
        const estadoCell = row.insertCell();
        const accionesCell = row.insertCell();

        fechaCell.textContent = cita.date;
        horaCell.textContent = cita.time;
        doctorCell.textContent = cita.doctor_name;
        estadoCell.textContent = cita.status;

        if (cita.status === 'completed' || cita.status === 'cancelled') {
          const deleteButton = document.createElement('button');
          deleteButton.textContent = 'Eliminar';
          deleteButton.addEventListener('click', function() {
            if (confirm('¿Estás seguro de que deseas eliminar esta entrada del historial?')) {
              fetch(`../php/eliminar_historial.php`, {
                method: 'DELETE',
                headers: {
                  'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: cita.appointment_id })
              })
                .then(response => response.json())
                .then(result => {
                  if (result.success) {
                    historialContainer.removeChild(row);
                  } else {
                    alert('Error al eliminar la entrada del historial.');
                  }
                })
                .catch(error => {
                  console.error('Error al eliminar la entrada del historial:', error);
                  alert('Error al eliminar la entrada del historial.');
                });
            }
          });
          accionesCell.appendChild(deleteButton);
        } else {
          accionesCell.textContent = 'No se puede eliminar';
        }
      });
    })
    .catch(error => {
      console.error('Error al obtener el historial de citas:', error);
    });

  clearHistorialButton.addEventListener('click', function() {
    if (confirm('¿Estás seguro de que deseas limpiar todo el historial? Esto podría causar la pérdida de información sobre citas pendientes.')) {
      if (confirm('Esta acción es irreversible. ¿Realmente deseas limpiar todo el historial?')) {
        fetch('../php/limpiar_historial.php', { method: 'DELETE' })
          .then(response => response.json())
          .then(result => {
            if (result.success) {
              historialContainer.innerHTML = '';
            } else {
              alert('Error al limpiar el historial.');
            }
          })
          .catch(error => {
            console.error('Error al limpiar el historial:', error);
            alert('Error al limpiar el historial.');
          });
      }
    }
  });
});