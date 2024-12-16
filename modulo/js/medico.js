document.addEventListener('DOMContentLoaded', function() {
    const filterButton = document.getElementById('filterButton');
    const filterModal = document.getElementById('filterModal');
    const closeModal = document.querySelector('.close');
    const filterOptions = document.querySelectorAll('.filter-option');
    const peticionesContainer = document.getElementById('peticiones');

    let allPeticiones = [];

    filterButton.addEventListener('click', function() {
        filterModal.style.display = 'block';
    });

    closeModal.addEventListener('click', function() {
        filterModal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === filterModal) {
            filterModal.style.display = 'none';
        }
    });

    filterOptions.forEach(option => {
        option.addEventListener('click', function() {
            const status = this.dataset.status;
            filterPeticiones(status);
            filterModal.style.display = 'none';
        });
    });

    fetch('../php/obtener_peticiones.php')
        .then(response => response.json())
        .then(peticiones => {
            console.log(peticiones); // Verificar el contenido de la respuesta
            allPeticiones = peticiones;
            displayPeticiones(allPeticiones);
        })
        .catch(error => {
            console.error("Error al obtener las peticiones:", error);
        });

    function displayPeticiones(peticiones) {
        peticionesContainer.innerHTML = ''; // Limpiar peticiones anteriores
        if (peticiones.length === 0) {
            peticionesContainer.innerHTML = '<p>No hay peticiones de citas.</p>';
        } else {
            peticiones.forEach(peticion => {
                const card = document.createElement('div');
                card.classList.add('card');
                card.classList.add(peticion.status); // Añadir clase de estado
                card.innerHTML = `
                    <h3>Petición de ${peticion.patient_name}</h3>
                    <p>Fecha: ${peticion.date}</p>
                    <p>Hora: ${peticion.time}</p>
                    <div class="button-group">
                        <button class="accept-button btn" data-appointment-id="${peticion.appointment_id}">Aceptar</button>
                        <button class="cancel-button btn" data-appointment-id="${peticion.appointment_id}">Cancelar</button>
                        <button class="complete-button btn" data-appointment-id="${peticion.appointment_id}">Completar</button>
                        <button class="delete-button btn" data-appointment-id="${peticion.appointment_id}" disabled>Eliminar</button>
                    </div>
                    <button class="print-button btn" data-appointment-id="${peticion.appointment_id}">Imprimir Comprobante</button>
                `;
                peticionesContainer.appendChild(card);

                // Agregar eventos click a los botones "Aceptar", "Cancelar", "Completar" y "Eliminar"
                const acceptButton = card.querySelector('.accept-button');
                const cancelButton = card.querySelector('.cancel-button');
                const completeButton = card.querySelector('.complete-button');
                const deleteButton = card.querySelector('.delete-button');
                const printButton = card.querySelector('.print-button');

                acceptButton.addEventListener('click', function() {
                    gestionarCita(peticion.appointment_id, 'accept');
                });

                cancelButton.addEventListener('click', function() {
                    gestionarCita(peticion.appointment_id, 'cancel');
                });

                completeButton.addEventListener('click', function() {
                    gestionarCita(peticion.appointment_id, 'complete');
                });

                deleteButton.addEventListener('click', function() {
                    const confirmation = confirm('¿Está seguro de que desea eliminar esta cita?');
                    if (confirmation) {
                        fetch(`../php/eliminar_cita.php?appointment_id=${peticion.appointment_id}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    card.remove();
                                } else {
                                    alert(data.error);
                                }
                            })
                            .catch(error => {
                                console.error("Error al eliminar la cita:", error);
                                alert('Error al procesar la solicitud.');
                            });
                    }
                });

                printButton.addEventListener('click', function() {
                    const appointmentId = this.dataset.appointmentId;
                    window.open(`../php/fpdf/comprobante.php?appointment_id=${appointmentId}`, '_blank');
                });
            });
        }
    }

    function filterPeticiones(status) {
        if (status === 'all') {
            displayPeticiones(allPeticiones);
        } else {
            const filteredPeticiones = allPeticiones.filter(peticion => peticion.status === status);
            displayPeticiones(filteredPeticiones);
        }
    }

    function gestionarCita(appointmentId, action) {
        const confirmation = confirm(`¿Está seguro de que desea ${action === 'accept' ? 'aceptar' : action === 'cancel' ? 'cancelar' : 'completar'} esta cita?`);
        if (!confirmation) {
            return;
        }

        const formData = new FormData();
        formData.append('appointment_id', appointmentId);
        formData.append('action', action);

        fetch('../php/gestionar_cita.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
            if (data.success) {
                const card = document.querySelector(`button[data-appointment-id="${appointmentId}"]`).closest('.card');
                const deleteButton = card.querySelector('.delete-button');
                deleteButton.disabled = false; // Habilitar el botón de eliminar

                // Actualizar el estado de la cita en allPeticiones
                const peticion = allPeticiones.find(p => p.appointment_id === appointmentId);
                if (peticion) {
                    peticion.status = action === 'accept' ? 'confirmed' : action === 'cancel' ? 'cancelled' : 'completed';
                }

                // Filtrar y mostrar las peticiones actualizadas
                filterPeticiones(peticion.status);
            }
        })
        .catch(error => {
            console.error("Error al gestionar la cita:", error);
            alert('Error al procesar la solicitud.');
        });
    }
});