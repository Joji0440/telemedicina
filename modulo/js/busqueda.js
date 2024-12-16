document.addEventListener('DOMContentLoaded', function() {
    const specialtySelect = document.getElementById('specialty');
    const languageSelect = document.getElementById('language');
    const genderSelect = document.getElementById('gender');
    const searchButton = document.getElementById('searchButton');
    const doctorsSection = document.querySelector('.doctors-section');
    const doctorsContainer = document.getElementById('doctors');
    const appointmentSection = document.querySelector('.appointment-section');
    const appointmentForm = document.getElementById('appointmentForm');
    const doctorIdInput = document.getElementById('doctorId');
    const appointmentDateInput = document.getElementById('appointmentDate');
    const appointmentTimeInput = document.getElementById('appointmentTime');

    // Obtener especialidades y idiomas disponibles
    fetch('../php/obtener_especialidades_idiomas.php')
        .then(response => response.json())
        .then(data => {
            const { especialidades, idiomas } = data;

            especialidades.forEach(especialidad => {
                const option = document.createElement('option');
                option.value = especialidad;
                option.textContent = especialidad;
                specialtySelect.appendChild(option);
            });

            idiomas.forEach(idioma => {
                const option = document.createElement('option');
                option.value = idioma;
                option.textContent = idioma;
                languageSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error al obtener especialidades e idiomas:', error);
        });

    // Manejar la búsqueda de médicos
    searchButton.addEventListener('click', function() {
        const specialty = specialtySelect.value;
        const language = languageSelect.value;
        const gender = genderSelect.value;

        if (!specialty || !language) {
            alert('Por favor, seleccione una especialidad y un idioma.');
            return;
        }

        fetch(`../php/buscar_medicos.php?especialidad=${specialty}&idioma=${language}&genero=${gender}`)
            .then(response => response.json())
            .then(doctors => {
                doctorsContainer.innerHTML = ''; // Limpiar médicos anteriores

                doctors.forEach(doctor => {
                    const card = document.createElement('div');
                    card.classList.add('doctor-card');
                    card.innerHTML = `
                        <h3>${doctor.name}</h3>
                        <p>Especialidad: ${doctor.specialty}</p>
                        <p>Idioma: ${doctor.language}</p>
                        <p>Género: ${doctor.gender === 'male' ? 'Hombre' : 'Mujer'}</p>
                    `;

                    // Obtener la disponibilidad del médico
                    fetch(`../php/obtener_disponibilidad.php?doctor_id=${doctor.doctor_id}`)
                        .then(response => response.json())
                        .then(disponibilidad => {
                            let disponibilidadHTML = '';
                            disponibilidad.forEach(d => {
                                disponibilidadHTML += `<p>${d.day_of_week}: ${d.start_time} - ${d.end_time}</p>`;
                            });

                            card.innerHTML += `
                                <div>${disponibilidadHTML}</div>
                                <button class="select-button" data-doctor-id="${doctor.doctor_id}">Seleccionar</button>
                            `;

                            doctorsContainer.appendChild(card);

                            const selectButtons = document.querySelectorAll('.select-button');
                            selectButtons.forEach(button => {
                                button.addEventListener('click', function() {
                                    const doctorId = this.dataset.doctorId;
                                    doctorIdInput.value = doctorId;

                                    fetch(`../php/obtener_disponibilidad.php?doctor_id=${doctorId}`)
                                        .then(response => response.json())
                                        .then(disponibilidad => {
                                            const proximaFechaDisponible = calcularProximaFechaDisponible(disponibilidad);
                                            if (proximaFechaDisponible) {
                                                const [fecha, hora] = proximaFechaDisponible.split('T');
                                                appointmentDateInput.value = fecha;
                                                appointmentTimeInput.value = hora;
                                                appointmentSection.style.display = 'block';
                                                appointmentSection.scrollIntoView({ behavior: 'smooth' });
                                            } else {
                                                alert('No hay disponibilidad para este médico en los próximos días.');
                                            }
                                        })
                                        .catch(error => {
                                            console.error("Error al obtener la disponibilidad del médico:", error);
                                            const resultados = document.getElementById('resultados');
                                            resultados.innerHTML = "<p>Error al obtener la disponibilidad del médico. Por favor, inténtalo de nuevo más tarde.</p>";
                                        });
                                });
                            });
                        })
                        .catch(error => {
                            console.error("Error al obtener la disponibilidad del médico:", error);
                            card.innerHTML += `
                                <p>Error al obtener la disponibilidad del médico. Por favor, inténtalo de nuevo más tarde.</p>
                            `;
                            doctorsContainer.appendChild(card);
                        });
                });

                doctorsSection.style.display = 'block';
                doctorsSection.scrollIntoView({ behavior: 'smooth' });
            })
            .catch(error => {
                console.error('Error al buscar médicos:', error);
            });
    });

    // Manejar la solicitud de cita
    appointmentForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData(appointmentForm);

        fetch('../php/solicitar_cita.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cita solicitada correctamente');
                appointmentSection.style.display = 'none';
                document.querySelector('.search-section').scrollIntoView({ behavior: 'smooth' });
            } else {
                alert('Error al solicitar la cita: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error al solicitar la cita:', error);
        });
    });

    function calcularProximaFechaDisponible(disponibilidad) {
        const daysOfWeek = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        const today = new Date();
        let proximaFecha = null;
        let proximaHora = null;

        for (let i = 0; i < 7; i++) {
            const dayIndex = (today.getDay() + i) % 7;
            const dayName = daysOfWeek[dayIndex];

            const disponibilidadDia = disponibilidad.find(d => d.day_of_week === dayName);
            if (disponibilidadDia) {
                // Verificar si la hora actual está dentro del horario de atención
                const horaActual = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
                if (disponibilidadDia.start_time <= horaActual && disponibilidadDia.end_time >= horaActual) {
                    proximaFecha = new Date(today);
                    proximaFecha.setDate(today.getDate() + i);
                    proximaHora = disponibilidadDia.start_time; // Usar la hora de inicio del médico
                    break;
                } else {
                    // Si la hora actual no está dentro del horario, calcular la próxima fecha
                    proximaFecha = new Date(today);
                    proximaFecha.setDate(today.getDate() + i + (dayIndex === today.getDay() ? 7 : 0)); // Sumar 7 días si es el mismo día
                    proximaHora = disponibilidadDia.start_time; // Usar la hora de inicio del médico
                    break;
                }
            }
        }

        // Devolver la fecha y hora en el formato correcto
        if (proximaFecha) {
            const year = proximaFecha.getFullYear();
            const month = ('0' + (proximaFecha.getMonth() + 1)).slice(-2);
            const day = ('0' + proximaFecha.getDate()).slice(-2);
            return `${year}-${month}-${day}T${proximaHora}`; 
        } else {
            return null;
        }
    }
});