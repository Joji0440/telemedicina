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
        const recaptchaResponse = grecaptcha.getResponse();

        if (!loginUser || !loginPassword || !recaptchaResponse) {
            alert('Por favor, complete todos los campos y verifique el reCAPTCHA.');
            return;
        }

        const formData = new FormData(loginForm);
        formData.append('g-recaptcha-response', recaptchaResponse);

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
                window.location.href = 'index.html'; // Redirigir al usuario a la página principal
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
        const gender = document.getElementById('gender').value;
        const dob = document.getElementById('dob').value;
        const phone = document.getElementById('phone').value;
        const canton = document.getElementById('canton').value;
        const localidad = document.getElementById('localidad').value;

        if (!username || !email || !password || !gender || !dob || !phone || !canton || !localidad) {
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
        formData.append('gender', gender);
        formData.append('dob', dob);
        formData.append('phone', phone);
        formData.append('canton', canton);
        formData.append('localidad', localidad);

        fetch('../php/register_user.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                alert(data.message);
                window.location.href = 'User_Inicio_Sesion.html'; // Redirigir al usuario a la página de inicio de sesión
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
