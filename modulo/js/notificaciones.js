document.addEventListener('DOMContentLoaded', function() {
    fetch('../php/obtener_notificaciones.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                console.error("Error al obtener las notificaciones:", data.error);
                return;
            }

            const notificaciones = data.notificaciones;
            const unreadCount = data.unreadCount;
            const notificacionesContainer = document.getElementById('notificaciones');
            const notificationDot = document.getElementById('notification-dot');

            // Mostrar el punto de notificación si hay notificaciones no leídas
            if (unreadCount > 0) {
                notificationDot.style.display = 'block';
            } else {
                notificationDot.style.display = 'none';
            }

            notificacionesContainer.innerHTML = ''; // Limpiar notificaciones anteriores
            notificaciones.forEach(notificacion => {
                const card = document.createElement('div');
                card.classList.add('notification-card');
                card.classList.add(notificacion.is_read ? 'read' : 'unread');
                card.innerHTML = `
                    <p>${notificacion.message}</p>
                    <p>Estado: ${notificacion.is_read ? 'Leído' : 'No leído'}</p>
                    <p>Médico: ${notificacion.doctor_name}</p>
                    ${notificacion.type === 'receta' ? `<a href="${notificacion.link}" target="_blank" class="btn"><ion-icon name="download-outline"></ion-icon>Imprimir Receta</a>` : ''}
                    <button class="mark-as-read-button" data-id="${notificacion.id}">Marcar como vista</button>
                    <button class="delete-button" data-id="${notificacion.id}">Eliminar</button>
                `;
                notificacionesContainer.appendChild(card);
            });

            // Agregar evento click a los botones "Marcar como vista"
            const markAsReadButtons = document.querySelectorAll('.mark-as-read-button');
            markAsReadButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const notificationId = this.dataset.id;
                    markAsRead(notificationId);
                });
            });

            // Agregar evento click a los botones "Eliminar"
            const deleteButtons = document.querySelectorAll('.delete-button');
            deleteButtons.forEach(button => {
                button.addEventListener('click', function() {
                    const notificationId = this.dataset.id;
                    deleteNotification(notificationId);
                });
            });
        })
        .catch(error => {
            console.error("Error al obtener las notificaciones:", error);
        });
});

function markAsRead(notificationId) {
    fetch('../php/obtener_notificaciones.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `id=${notificationId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Notificación marcada como vista');
            location.reload(); // Recargar la página para reflejar los cambios
        } else {
            alert('Error al marcar la notificación como vista: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error al marcar la notificación como vista:', error);
    });
}

function deleteNotification(notificationId) {
    fetch(`../php/eliminar_notificacion.php?id=${notificationId}`, {
        method: 'DELETE'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Notificación eliminada con éxito');
            location.reload(); // Recargar la página para reflejar los cambios
        } else {
            alert('Error al eliminar la notificación: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error al eliminar la notificación:', error);
    });
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.classList.add('notification');
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => {
        notification.remove();
    }, 3000);
}