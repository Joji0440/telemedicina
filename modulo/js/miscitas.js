document.addEventListener('DOMContentLoaded', function() {
    fetch('../php/obtener_citas.php')
        .then(response => response.json())
        .then(citas => {
            if (citas.error) {
                console.error("Error al obtener las citas:", citas.error);
                alert(citas.error);
                return;
            }

            const citasContainer = document.getElementById('citas');
            citasContainer.innerHTML = ''; // Limpiar citas anteriores
            citas.forEach(cita => {
                const card = document.createElement('div');
                card.classList.add('card');
                card.innerHTML = `
                    <h3>Cita con ${cita.doctor_name}</h3>
                    <p>Fecha: ${cita.date}</p>
                    <p>Hora: ${cita.time}</p>
                    <p>Política de cancelación: ${cita.cancellation_notice_hours} horas de antelación</p>
                    <button class="cancel-button" data-cita-id="${cita.appointment_id}">Cancelar</button>
                    <button class="contact-button" data-cita-id="${cita.appointment_id}" data-doctor-id="${cita.doctor_id}">Contactar</button>
                    <button class="print-button" data-cita-id="${cita.appointment_id}">Imprimir Comprobante</button>
                `;
                citasContainer.appendChild(card);
            });

            // Agregar evento click a los botones "Cancelar"
            const cancelButtons = document.querySelectorAll('.cancel-button');
            cancelButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const citaId = this.dataset.citaId;
                    if (confirm("¿Estás seguro de que quieres cancelar esta cita?")) {
                        fetch(`../php/cancelacion_citas.php?cita_id=${citaId}`)
                            .then(response => response.json())
                            .then(data => {
                                if (data.message) {
                                    alert(data.message);
                                    location.reload(); // Recargar la página para actualizar las citas
                                } else if (data.error) {
                                    alert(data.error);
                                }
                            })
                            .catch(error => {
                                console.error("Error al cancelar la cita:", error);
                                alert('Error al procesar la solicitud.');
                            });
                    }
                });
            });

            // Agregar evento click a los botones "Contactar"
            const contactButtons = document.querySelectorAll('.contact-button');
            contactButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const citaId = this.dataset.citaId;
                    const doctorId = this.dataset.doctorId;
                    openChatModal(citaId, doctorId);
                });
            });

            // Agregar evento click a los botones "Imprimir Comprobante"
            const printButtons = document.querySelectorAll('.print-button');
            printButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const citaId = this.dataset.citaId;
                    window.open(`../php/fpdf/comprobante.php?appointment_id=${citaId}`, '_blank');
                });
            });
        })
        .catch(error => {
            console.error("Error al obtener las citas:", error);
        });

    const chatModal = document.getElementById('chatModal');
    const chatForm = document.getElementById('chatForm');
    const chatMessages = document.getElementById('chatMessages');
    const closeModal = document.querySelector('.close');

    closeModal.addEventListener('click', function() {
        chatModal.style.display = 'none';
    });

    window.addEventListener('click', function(event) {
        if (event.target === chatModal) {
            chatModal.style.display = 'none';
        }
    });

    chatForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(chatForm);
        formData.append('receiver_id', chatForm.dataset.doctorId); // Agregar receiver_id al formulario

        fetch('../php/enviar_mensaje.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                chatMessages.innerHTML += `<div class="message patient"><strong>${data.sender_name}:</strong> ${data.message}</div>`;
                chatForm.reset();
            }
        })
        .catch(error => {
            console.error('Error al enviar el mensaje:', error);
        });
    });

    function openChatModal(citaId, doctorId) {
        document.getElementById('chatCitaId').value = citaId;
        chatForm.dataset.doctorId = doctorId; // Guardar doctorId en el formulario
        chatMessages.innerHTML = ''; // Limpiar mensajes anteriores

        fetch(`../php/obtener_mensajes.php?cita_id=${citaId}&doctor_id=${doctorId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    data.messages.forEach(message => {
                        const messageClass = message.sender === 'patient' ? 'patient' : 'doctor';
                        chatMessages.innerHTML += `<div class="message ${messageClass}"><strong>${message.sender_name}:</strong> ${message.text}</div>`;
                    });
                    chatModal.style.display = 'block';

                    // Añadir botón de borrar mensajes solo para el paciente
                    const deleteButton = document.querySelector('.delete-button');
                    deleteButton.addEventListener('click', function() {
                        if (confirm("¿Estás seguro de que quieres borrar todos los mensajes?")) {
                            fetch(`../php/borrar_mensajes.php?cita_id=${citaId}&doctor_id=${doctorId}`)
                                .then(response => response.json())
                                .then(data => {
                                    if (data.success) {
                                        chatMessages.innerHTML = ''; // Limpiar mensajes
                                    } else {
                                        alert(data.error);
                                    }
                                })
                                .catch(error => {
                                    console.error('Error al borrar los mensajes:', error);
                                });
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error al obtener los mensajes:', error);
            });
    }
});