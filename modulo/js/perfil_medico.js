document.addEventListener('DOMContentLoaded', function() {
    const nombreUsuario = document.getElementById('nombre-usuario');
    const correoUsuario = document.getElementById('correo-usuario');
    const especialidadUsuario = document.getElementById('especialidad-usuario');
    const credencialesUsuario = document.getElementById('credenciales-usuario');
    const generoUsuario = document.getElementById('genero-usuario');
    const idiomaUsuario = document.getElementById('idioma-usuario');
    const zonaHorariaUsuario = document.getElementById('zona-horaria-usuario');
    const telefonoUsuario = document.getElementById('telefono-usuario');
    const horasCancelacion = document.getElementById('horas-cancelacion');
    const maxWaitTime = document.getElementById('max-wait-time');
    const horariosContainer = document.getElementById('horarios-container');

    // Obtener los datos del médico con la sesión activa
    fetch('../php/obtener_datos_medico.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                nombreUsuario.textContent = data.nombre;
                correoUsuario.textContent = data.correo;
                especialidadUsuario.textContent = data.especialidad;
                credencialesUsuario.textContent = data.credenciales;
                generoUsuario.textContent = data.genero;
                idiomaUsuario.textContent = data.idioma;
                zonaHorariaUsuario.textContent = data.zona_horaria;
                telefonoUsuario.textContent = data.telefono;
                horasCancelacion.value = data.horas_cancelacion || 0;
                maxWaitTime.value = data.max_wait_time || 0;

                // Rellenar el formulario de edición con los datos obtenidos
                document.getElementById('nombre').value = data.nombre;
                document.getElementById('correo').value = data.correo;
                document.getElementById('especialidad').value = data.especialidad;
                document.getElementById('credenciales').value = data.credenciales;
                document.getElementById('genero').value = data.genero;
                document.getElementById('idioma').value = data.idioma;
                document.getElementById('zona-horaria').value = data.zona_horaria;
                document.getElementById('telefono').value = data.telefono;
                document.getElementById('horas-cancelacion').value = data.horas_cancelacion || 0;
                document.getElementById('max-wait-time').value = data.max_wait_time || 0;

                // Rellenar los horarios de atención
                horariosContainer.innerHTML = ''; // Limpiar horarios anteriores
                data.horarios.forEach(horario => {
                    const horarioElement = document.createElement('div');
                    horarioElement.classList.add('horario');
                    horarioElement.innerHTML = `
                        <div class="horario-info">
                            <p><strong>Día:</strong> ${horario.day_of_week}</p>
                            <p><strong>Inicio:</strong> ${horario.start_time}</p>
                            <p><strong>Fin:</strong> ${horario.end_time}</p>
                        </div>
                        <div class="horario-acciones">
                            <button class="delete-schedule-button" data-schedule-id="${horario.id}">Eliminar</button>
                        </div>
                    `;
                    horariosContainer.appendChild(horarioElement);
                });

                // Añadir eventos a los botones de eliminar horario
                const deleteScheduleButtons = document.querySelectorAll('.delete-schedule-button');
                deleteScheduleButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const scheduleId = this.dataset.scheduleId;

                        if (!confirm('¿Está seguro de que desea eliminar este horario?')) {
                            return;
                        }

                        fetch(`../php/eliminar_horario.php?id=${scheduleId}`, {
                            method: 'DELETE'
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                alert('Horario eliminado correctamente');
                                location.reload(); // Recargar la página para reflejar los cambios
                            } else {
                                alert('Error al eliminar el horario: ' + data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error al eliminar el horario:', error);
                        });
                    });
                });
            }
        })
        .catch(error => {
            console.error('Error al obtener los datos del médico:', error);
        });

    // Manejar la actualización de los datos del médico
    const updateForm = document.getElementById('form-editar-perfil');
    updateForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(updateForm);

        // Asegurarse de que todos los campos requeridos están presentes
        if (!formData.has('horas-cancelacion')) {
            formData.append('horas-cancelacion', horasCancelacion.value || 0);
        }
        if (!formData.has('max-wait-time')) {
            formData.append('max-wait-time', maxWaitTime.value || 0);
        }

        fetch('../php/actualizar_datos_medico.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Datos actualizados correctamente');
                location.reload(); // Recargar la página para reflejar los cambios
            } else {
                alert('Error al actualizar los datos: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error al actualizar los datos del médico:', error);
        });
    });

    // Manejar la adición de nuevos horarios de atención
    const addScheduleButton = document.getElementById('addScheduleButton');
    addScheduleButton.addEventListener('click', function() {
        const dayOfWeek = document.getElementById('dayOfWeek').value;
        const startTime = document.getElementById('startTime').value;
        const endTime = document.getElementById('endTime').value;

        if (!dayOfWeek || !startTime || !endTime) {
            alert('Por favor, complete todos los campos para agregar un nuevo horario');
            return;
        }

        const formData = new FormData();
        formData.append('day_of_week', dayOfWeek);
        formData.append('start_time', startTime);
        formData.append('end_time', endTime);

        fetch('../php/agregar_horario.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Horario agregado correctamente');
                location.reload(); // Recargar la página para reflejar los cambios
            } else {
                alert('Error al agregar el horario: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error al agregar el horario:', error);
        });
    });

    // Manejar el cambio de contraseña
    const changePasswordForm = document.getElementById('form-cambiar-contrasena');
    changePasswordForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const currentPassword = document.getElementById('contrasena-actual').value;
        const newPassword = document.getElementById('nueva-contrasena').value;
        const confirmPassword = document.getElementById('confirmar-contrasena').value;

        if (newPassword !== confirmPassword) {
            alert('Las nuevas contraseñas no coinciden');
            return;
        }

        const formData = new FormData();
        formData.append('current_password', currentPassword);
        formData.append('new_password', newPassword);

        fetch('../php/actualizar_datos_medico.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Contraseña cambiada correctamente');
                location.reload(); // Recargar la página para reflejar los cambios
            } else {
                alert('Error al cambiar la contraseña: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error al cambiar la contraseña:', error);
        });
    });
});