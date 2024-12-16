document.addEventListener('DOMContentLoaded', function() {
    const formEditarPerfil = document.getElementById('form-editar-perfil');
    const formCambiarContrasena = document.getElementById('form-cambiar-contrasena');
    const nombreUsuario = document.getElementById('nombre-usuario');
    const correoUsuario = document.getElementById('correo-usuario');
    const fechaNacimientoUsuario = document.getElementById('fecha-nacimiento-usuario');
    const telefonoUsuario = document.getElementById('telefono-usuario');
    const cantonUsuario = document.getElementById('canton-usuario');
    const localidadUsuario = document.getElementById('localidad-usuario');

    // Obtener los datos del paciente con la sesión activa
    fetch('../php/obtener_datos_paciente.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                nombreUsuario.textContent = data.nombre;
                correoUsuario.textContent = data.correo;
                fechaNacimientoUsuario.textContent = data.fecha_nacimiento;
                telefonoUsuario.textContent = data.telefono;
                cantonUsuario.textContent = data.canton;
                localidadUsuario.textContent = data.localidad;

                // Rellenar el formulario de edición con los datos obtenidos
                document.getElementById('nombre').value = data.nombre;
                document.getElementById('correo').value = data.correo;
                document.getElementById('fecha-nacimiento').value = data.fecha_nacimiento;
                document.getElementById('telefono').value = data.telefono;
                document.getElementById('canton').value = data.canton;
                document.getElementById('localidad').value = data.localidad;
            }
        })
        .catch(error => {
            console.error('Error al obtener los datos del paciente:', error);
        });

    // Manejar la actualización de los datos del perfil
    formEditarPerfil.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(formEditarPerfil);

        fetch('../php/actualizar_datos_paciente.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                alert(data.message);
                // Actualizar los datos mostrados en el perfil
                nombreUsuario.textContent = formData.get('nombre');
                fechaNacimientoUsuario.textContent = formData.get('fecha-nacimiento');
                telefonoUsuario.textContent = formData.get('telefono');
                cantonUsuario.textContent = formData.get('canton');
                localidadUsuario.textContent = formData.get('localidad');
            }
        })
        .catch(error => {
            console.error('Error al actualizar los datos del paciente:', error);
        });
    });

    // Manejar el cambio de contraseña
    formCambiarContrasena.addEventListener('submit', function(event) {
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

        fetch('../php/actualizar_datos_paciente.php', {
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