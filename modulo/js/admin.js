document.addEventListener('DOMContentLoaded', function() {
    fetch('../php/obtener_usuarios.php')
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                populateTable('patientsTable', data.patients);
                populateTable('doctorsTable', data.doctors);
                populateTable('adminsTable', data.admins);
            }
        })
        .catch(error => {
            console.error('Error al obtener los datos:', error);
        });

    const editUserModal = document.getElementById('editUserModal');
    const editUserForm = document.getElementById('editUserForm');
    const closeModal = document.querySelectorAll('.close');

    closeModal.forEach(span => {
        span.addEventListener('click', function() {
            editUserModal.style.display = 'none';
            generateReportModal.style.display = 'none';
        });
    });

    window.addEventListener('click', function(event) {
        if (event.target === editUserModal) {
            editUserModal.style.display = 'none';
        }
        if (event.target === generateReportModal) {
            generateReportModal.style.display = 'none';
        }
    });

    editUserForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(editUserForm);

        fetch('../php/editar_usuario.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                alert(data.message);
                location.reload(); // Recargar la página para actualizar los datos
            }
        })
        .catch(error => {
            console.error('Error al editar el usuario:', error);
        });
    });

    const showPatientsButton = document.getElementById('showPatients');
    const showDoctorsButton = document.getElementById('showDoctors');
    const showAdminsButton = document.getElementById('showAdmins');
    const showForumButton = document.getElementById('showForum');
    const generateReportButton = document.getElementById('generateReportButton');
    const generateReportModal = document.getElementById('generateReportModal');
    const generateReportForm = document.getElementById('generateReportForm');

    showPatientsButton.addEventListener('click', function() {
        document.getElementById('patientsTableContainer').style.display = 'block';
        document.getElementById('doctorsTableContainer').style.display = 'none';
        document.getElementById('adminsTableContainer').style.display = 'none';
        document.getElementById('forumTableContainer').style.display = 'none';
    });

    showDoctorsButton.addEventListener('click', function() {
        document.getElementById('patientsTableContainer').style.display = 'none';
        document.getElementById('doctorsTableContainer').style.display = 'block';
        document.getElementById('adminsTableContainer').style.display = 'none';
        document.getElementById('forumTableContainer').style.display = 'none';
    });

    showAdminsButton.addEventListener('click', function() {
        document.getElementById('patientsTableContainer').style.display = 'none';
        document.getElementById('doctorsTableContainer').style.display = 'none';
        document.getElementById('adminsTableContainer').style.display = 'block';
        document.getElementById('forumTableContainer').style.display = 'none';
    });

    showForumButton.addEventListener('click', function() {
        document.getElementById('patientsTableContainer').style.display = 'none';
        document.getElementById('doctorsTableContainer').style.display = 'none';
        document.getElementById('adminsTableContainer').style.display = 'none';
        document.getElementById('forumTableContainer').style.display = 'block';
        loadForumComments();
    });

    generateReportButton.addEventListener('click', function() {
        generateReportModal.style.display = 'block';
    });

    generateReportForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(generateReportForm);
        const queryString = new URLSearchParams(formData).toString();
        window.open(`../php/fpdf/PruebaV.php?${queryString}`, '_blank');
    });

    const addAdminForm = document.getElementById('addAdminForm');
    addAdminForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(addAdminForm);

        fetch('../php/agregar_admin.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                alert(data.message);
                location.reload(); // Recargar la página para actualizar los datos
            }
        })
        .catch(error => {
            console.error('Error al agregar el administrador:', error);
        });
    });

    function loadForumComments() {
        fetch('../php/obtener_comentario.php')
            .then(response => response.json())
            .then(comments => {
                const forumCommentsContainer = document.getElementById('forumCommentsContainer');
                forumCommentsContainer.innerHTML = ''; // Limpiar comentarios anteriores

                comments.forEach(comment => {
                    const commentElement = document.createElement('div');
                    commentElement.classList.add('comment');
                    commentElement.innerHTML = `
                        <p class="comment-text">${comment.content}</p>
                        <p class="comment-author">Por: ${comment.author}</p>
                        ${comment.reply ? `<p class="comment-reply">Respuesta: ${comment.reply}</p>` : ''}
                        <form class="replyForm">
                            <textarea class="replyText" placeholder="Escribe tu respuesta..."></textarea>
                            <button type="submit" data-comment-id="${comment.id}">Responder</button>
                        </form>
                        <button class="delete-comment" data-comment-id="${comment.id}">Eliminar</button>
                    `;
                    forumCommentsContainer.appendChild(commentElement);

                    const replyForm = commentElement.querySelector('.replyForm');
                    replyForm.addEventListener('submit', function(event) {
                        event.preventDefault();
                        const replyText = replyForm.querySelector('.replyText').value;
                        const commentId = replyForm.querySelector('button').dataset.commentId;

                        const formData = new FormData();
                        formData.append('comment_id', commentId);
                        formData.append('reply', replyText);

                        fetch('../php/responder_comentario.php', {
                            method: 'POST',
                            body: formData
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                loadForumComments(); // Recargar los comentarios
                            } else {
                                alert('Error al responder el comentario: ' + data.error);
                            }
                        })
                        .catch(error => {
                            console.error('Error al responder el comentario:', error);
                        });
                    });

                    const deleteButton = commentElement.querySelector('.delete-comment');
                    deleteButton.addEventListener('click', function() {
                        const commentId = deleteButton.dataset.commentId;
                        deleteComment(commentId);
                    });
                });
            })
            .catch(error => {
                console.error('Error al cargar los comentarios:', error);
            });
    }

    function deleteComment(commentId) {
        if (confirm('¿Está seguro de que desea eliminar este comentario?')) {
            fetch(`../php/eliminar_comentario.php?id=${commentId}`, {
                method: 'DELETE'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    loadForumComments(); // Recargar los comentarios
                } else {
                    alert('Error al eliminar el comentario: ' + data.error);
                }
            })
            .catch(error => {
                console.error('Error al eliminar el comentario:', error);
            });
        }
    }
});

function populateTable(tableId, data) {
    const tableBody = document.getElementById(tableId).querySelector('tbody');
    tableBody.innerHTML = ''; // Limpiar datos anteriores

    data.forEach(item => {
        const row = document.createElement('tr');
        Object.values(item).forEach(value => {
            const cell = document.createElement('td');
            cell.textContent = value;
            row.appendChild(cell);
        });

        // Agregar botones de acción
        const actionsCell = document.createElement('td');
        const editButton = document.createElement('button');
        editButton.textContent = 'Editar';
        editButton.classList.add('edit-button');
        editButton.dataset.id = item.id;
        editButton.dataset.role = tableId;
        editButton.addEventListener('click', () => openEditModal(item, tableId));

        const deleteButton = document.createElement('button');
        deleteButton.textContent = 'Eliminar';
        deleteButton.classList.add('delete-button');
        deleteButton.dataset.id = item.id;
        deleteButton.addEventListener('click', () => deleteUser(item.id, tableId));

        actionsCell.appendChild(editButton);
        actionsCell.appendChild(deleteButton);
        row.appendChild(actionsCell);

        tableBody.appendChild(row);
    });
}

function openEditModal(item, tableId) {
    const editUserModal = document.getElementById('editUserModal');
    const editUserForm = document.getElementById('editUserForm');
    const additionalFields = document.getElementById('additionalFields');

    document.getElementById('editUserId').value = item.id;
    document.getElementById('editUserRole').value = tableId;
    document.getElementById('editUserName').value = item.name;
    document.getElementById('editUserEmail').value = item.email;
    document.getElementById('editUserPassword').value = '';

    additionalFields.innerHTML = ''; // Limpiar campos adicionales

    if (tableId === 'patientsTable') {
        additionalFields.innerHTML = `
            <label for="editUserPhone">Teléfono:</label>
            <input type="text" id="editUserPhone" name="phone" value="${item.phone}" required>
            <label for="editUserCanton">Cantón:</label>
            <input type="text" id="editUserCanton" name="canton" value="${item.canton}" required>
            <label for="editUserLocalidad">Localidad:</label>
            <input type="text" id="editUserLocalidad" name="localidad" value="${item.localidad}" required>
        `;
    } else if (tableId === 'doctorsTable') {
        additionalFields.innerHTML = `
            <label for="editUserSpecialty">Especialidad:</label>
            <input type="text" id="editUserSpecialty" name="specialty" value="${item.specialty}" required>
            <label for="editUserCredentials">Credenciales:</label>
            <textarea id="editUserCredentials" name="credentials" required>${item.credentials}</textarea>
        `;
    }

    editUserModal.style.display = 'block';
}

function deleteUser(id, tableId) {
    const confirmation = confirm(`¿Está seguro de que desea eliminar el usuario con ID: ${id}?`);
    if (confirmation) {
        fetch(`../php/eliminar_usuario.php?id=${id}&table=${tableId}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                alert(data.message);
                location.reload(); // Recargar la página para actualizar los datos
            }
        })
        .catch(error => {
            console.error('Error al eliminar el usuario:', error);
        });
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const generateReportModal = document.getElementById('generateReportModal');
    const generateReportButton = document.getElementById('generateReportButton');
    const closeModal = document.querySelectorAll('.close');
    const generateReportForm = document.getElementById('generateReportForm');

    // Verificar que el botón existe
    if (!generateReportButton) {
        console.error('El botón de generar reporte no se encontró.');
        return;
    }

    // Abrir el modal
    generateReportButton.onclick = function() {
        generateReportModal.style.display = 'block';
    }

    // Cerrar el modal
    closeModal.forEach(span => {
        span.addEventListener('click', function() {
            generateReportModal.style.display = 'none';
        });
    });

    // Cerrar el modal cuando el usuario hace clic fuera del modal
    window.onclick = function(event) {
        if (event.target === generateReportModal) {
            generateReportModal.style.display = 'none';
        }
    }

    // Manejar el envío del formulario
    generateReportForm.onsubmit = function(event) {
        event.preventDefault();
        const formData = new FormData(generateReportForm);
        const queryString = new URLSearchParams(formData).toString();
        window.open(`../php/fpdf/PruebaV.php?${queryString}`, '_blank');
    }
});