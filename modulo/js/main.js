document.addEventListener('DOMContentLoaded', function() {
    const patientIdInput = document.getElementById('patient_id');

    // Obtener el ID del paciente de la sesión y asignarlo al campo patient_id
    fetch('../php/obtener_datos_usuario.php')
        .then(response => response.json())
        .then(data => {
            if (data.patient_id) {
                if (patientIdInput) {
                    patientIdInput.value = data.patient_id;
                }
            } else {
                console.error("Error al obtener el ID del paciente:", data.error);
            }
        })
        .catch(error => {
            console.error("Error al obtener el ID del paciente:", error);
        });

    const adminBubble = document.getElementById('adminBubble');
    const adminFormContainer = document.getElementById('adminFormContainer');
    const adminLoginForm = document.getElementById('adminLoginForm');

    adminBubble.addEventListener('click', function() {
        adminFormContainer.style.display = 'block';
    });

    adminLoginForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const adminUser = document.getElementById('adminUser').value;
        const adminPassword = document.getElementById('adminPassword').value;

        if (!adminUser || !adminPassword) {
            alert('Por favor, complete todos los campos.');
            return;
        }

        const formData = new FormData();
        formData.append('adminUser', adminUser);
        formData.append('adminPassword', adminPassword);

        fetch('../php/admin_login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                alert(data.message);
                window.location.href = 'admin.html'; // Redirigir a la página de administración
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud.');
        });
    });

    // Cerrar el formulario de administrador si se hace clic fuera de él
    window.addEventListener('click', function(event) {
        if (event.target !== adminFormContainer && !adminFormContainer.contains(event.target) && event.target !== adminBubble) {
            adminFormContainer.style.display = 'none';
        }
    });

    const loginForm = document.getElementById('login-form');
    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(loginForm);
        fetch('https://<tu-dominio>.railway.app/modulo/php/login.php', { // Asegúrate de usar la URL correcta
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = 'dashboard.html';
            } else {
                alert('Error al iniciar sesión: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error al iniciar sesión:', error);
        });
    });
});