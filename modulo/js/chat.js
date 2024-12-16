document.addEventListener('DOMContentLoaded', function() {
    fetch('../php/obtener_chats.php')
        .then(response => response.json())
        .then(chats => {
            const chatsContainer = document.getElementById('chats');
            chatsContainer.innerHTML = ''; // Limpiar chats anteriores
            chats.forEach(chat => {
                const card = document.createElement('div');
                card.classList.add('card');
                card.innerHTML = `
                    <h3>Chat con ${chat.patient_name}</h3>
                    <p>Fecha de la cita: ${chat.date}</p>
                    <p>Hora de la cita: ${chat.time}</p>
                    <button class="open-chat-button" data-cita-id="${chat.appointment_id}" data-patient-id="${chat.patient_id}">Abrir Chat</button>
                    <button class="emitir-receta-button" data-patient-id="${chat.patient_id}" data-patient-name="${chat.patient_name}">Emitir Receta</button>
                `;
                chatsContainer.appendChild(card);
            });

            // Agregar evento click a los botones "Abrir Chat"
            const openChatButtons = document.querySelectorAll('.open-chat-button');
            openChatButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const citaId = this.dataset.citaId;
                    const patientId = this.dataset.patientId;
                    openChatModal(citaId, patientId);
                });
            });

            // Agregar evento click a los botones "Emitir Receta"
            const emitirRecetaButtons = document.querySelectorAll('.emitir-receta-button');
            emitirRecetaButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const patientId = this.dataset.patientId;
                    const patientName = this.dataset.patientName;
                    openRecetaModal(patientId, patientName);
                });
            });
        })
        .catch(error => {
            console.error("Error al obtener los chats:", error);
        });

    const chatModal = document.getElementById('chatModal');
    const chatForm = document.getElementById('chatForm');
    const chatMessages = document.getElementById('chatMessages');
    const closeModal = document.querySelectorAll('.close');

    closeModal.forEach(span => {
        span.addEventListener('click', function() {
            chatModal.style.display = 'none';
            recetaModal.style.display = 'none';
        });
    });

    window.addEventListener('click', function(event) {
        if (event.target === chatModal || event.target === recetaModal) {
            chatModal.style.display = 'none';
            recetaModal.style.display = 'none';
        }
    });

    chatForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(chatForm);
        formData.append('receiver_id', chatForm.dataset.patientId); // Agregar receiver_id al formulario

        fetch('../php/enviar_mensaje.php', {
            method: 'POST',
            body: formData,
            credentials: 'include' // Esto asegura que las cookies de sesión se envían
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json(); // Procesar como JSON
        })
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                chatMessages.innerHTML += `<div class="message ${data.sender === 'you' ? 'you' : 'other'}"><strong>${data.sender_name}:</strong> ${data.message}</div>`;
                chatForm.reset();
            }
        })
        .catch(error => {
            console.error('Error al enviar el mensaje:', error);
        });
    });

    function openChatModal(citaId, patientId) {
        document.getElementById('chatCitaId').value = citaId;
        chatForm.dataset.patientId = patientId; // Guardar patientId en el formulario
        chatMessages.innerHTML = ''; // Limpiar mensajes anteriores

        fetch(`../php/obtener_mensajes.php?cita_id=${citaId}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                } else {
                    data.messages.forEach(message => {
                        const messageClass = message.sender === 'you' ? 'you' : 'other';
                        chatMessages.innerHTML += `<div class="message ${messageClass}"><strong>${message.sender_name}:</strong> ${message.text}</div>`;
                    });
                    chatModal.style.display = 'block';
                }
            })
            .catch(error => {
                console.error('Error al obtener los mensajes:', error);
            });
    }

    const recetaModal = document.getElementById('recetaModal');
    const recetaForm = document.getElementById('recetaForm');
    const generarRecetaButton = document.getElementById('generarRecetaButton');

    function openRecetaModal(patientId, patientName) {
        recetaForm.dataset.patientId = patientId; // Guardar patientId en el formulario
        recetaForm.dataset.patientName = patientName; // Guardar patientName en el formulario
        recetaModal.style.display = 'block';
    }

    generarRecetaButton.addEventListener('click', function() {
        const recetaTexto = document.getElementById('recetaTexto').value;
        const patientId = recetaForm.dataset.patientId;
        const patientName = recetaForm.dataset.patientName;

        // Crear un objeto con los datos de la receta
        const recetaData = {
            patient_id: patientId,
            patient_name: patientName,
            receta: recetaTexto
        };

        // Enviar los datos al servidor para generar el PDF
        fetch('../php/fpdf/generar_receta.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(recetaData)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Receta generada con éxito');
                recetaModal.style.display = 'none';
            } else {
                alert('Error al generar la receta: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error al generar la receta:', error);
        });
    });

    function showNotification(message) {
        const notification = document.createElement('div');
        notification.classList.add('notification');
        notification.textContent = message;
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
});