document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const forgotPasswordForm = document.getElementById('forgotPasswordForm');
    const showLoginFormButton = document.getElementById('showLoginForm');
    const showRegisterFormButton = document.getElementById('showRegisterForm');
    const forgotPasswordLink = document.getElementById('forgotPasswordLink');

    showLoginFormButton.addEventListener('click', function() {
        loginForm.style.display = 'block';
        registerForm.style.display = 'none';
        forgotPasswordForm.style.display = 'none';
        showLoginFormButton.classList.add('active');
        showRegisterFormButton.classList.remove('active');
    });

    showRegisterFormButton.addEventListener('click', function() {
        loginForm.style.display = 'none';
        registerForm.style.display = 'block';
        forgotPasswordForm.style.display = 'none';
        showLoginFormButton.classList.remove('active');
        showRegisterFormButton.classList.add('active');
    });

    forgotPasswordLink.addEventListener('click', function(event) {
        event.preventDefault();
        loginForm.style.display = 'none';
        registerForm.style.display = 'none';
        forgotPasswordForm.style.display = 'block';
        showLoginFormButton.classList.remove('active');
        showRegisterFormButton.classList.remove('active');
    });

    loginForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const loginUser = document.getElementById('loginUser').value;
        const loginPassword = document.getElementById('loginPassword').value;

        if (!loginUser || !loginPassword) {
            alert('Por favor, complete todos los campos.');
            return;
        }

        const formData = new FormData(loginForm);

        fetch('../php/login.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                alert(data.message);
                if (data.user.role === 'doctor') {
                    window.location.href = 'medico_peticiones.html'; // Redirigir al médico a la página de gestión de peticiones
                } else {
                    window.location.href = 'index.html'; // Redirigir a otros usuarios a la página principal
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud.');
        });
    });

    registerForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const username = document.getElementById('username').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const specialties = document.getElementById('specialties').value;
        const credentials = document.getElementById('credentials').value;
        const phone = document.getElementById('phone').value;
        const gender = document.getElementById('gender').value;
        const language = document.getElementById('language').value;
        const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        const hours = days.map(day => {
            const startTime = document.querySelector(`input[name="startTime${day}"]`).value;
            const endTime = document.querySelector(`input[name="endTime${day}"]`).value;
            return { day, start: startTime, end: endTime };
        });

        if (!username || !email || !password || !specialties || !credentials || !phone || !gender || !language) {
            alert('Por favor, complete todos los campos obligatorios.');
            return;
        }

        // Validar formato del correo electrónico
        const emailPattern = /^[a-zA-Z0-9._%+-]+@(hotmail|gmail|outlook|live)\.(com|ec)$/;
        if (!emailPattern.test(email)) {
            alert('Por favor, ingrese un correo electrónico válido.');
            return;
        }

        const formData = new FormData();
        formData.append('username', username);
        formData.append('email', email);
        formData.append('password', password);
        formData.append('specialties', specialties);
        formData.append('credentials', credentials);
        formData.append('phone', phone);
        formData.append('gender', gender);
        formData.append('language', language);
        hours.forEach(hour => {
            if (hour.start && hour.end) {
                formData.append(`startTime${hour.day}`, hour.start);
                formData.append(`endTime${hour.day}`, hour.end);
            }
        });

        fetch('../php/register_doctor.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                alert(data.message);
                window.location.href = 'Med_Inicio_Sesion.html'; // Redirigir al usuario a la página de inicio de sesión
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud.');
        });
    });

    forgotPasswordForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const email = document.getElementById('forgotEmail').value;
        const username = document.getElementById('forgotUsername').value;
        const phone = document.getElementById('forgotPhone').value;

        if (!email || !username || !phone) {
            alert('Por favor, complete todos los campos.');
            return;
        }

        const formData = new FormData();
        formData.append('email', email);
        formData.append('username', username);
        formData.append('phone', phone);

        fetch('../php/recuperar_contrasena.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al procesar la solicitud.');
        });
    });
});
