document.addEventListener('DOMContentLoaded', function() {
    const commentsContainer = document.getElementById('comments-container');
    const commentForm = document.getElementById('commentForm');
    const commentText = document.getElementById('commentText');

    // Función para cargar los comentarios
    function loadComments() {
        fetch('../php/obtener_comentario.php')
            .then(response => response.json())
            .then(comments => {
                commentsContainer.innerHTML = ''; // Limpiar comentarios anteriores

                comments.forEach(comment => {
                    const commentElement = document.createElement('div');
                    commentElement.classList.add('comment');
                    commentElement.innerHTML = `
                        <p class="comment-author">Por: ${comment.author}</p>
                        <p class="comment-text">${comment.content}</p>
                        ${comment.reply ? `<div class="admin-response"><p class="admin-author">Respuesta por: ${comment.admin_name}</p><p class="admin-text">${comment.reply}</p></div>` : ''}
                    `;
                    commentsContainer.appendChild(commentElement);
                });
            })
            .catch(error => {
                console.error('Error al cargar los comentarios:', error);
            });
    }

    // Cargar los comentarios al cargar la página
    loadComments();

    // Manejar el envío del formulario de comentarios
    commentForm.addEventListener('submit', function(event) {
        event.preventDefault();

        const formData = new FormData();
        formData.append('content', commentText.value);

        fetch('../php/enviar_comentario.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                commentText.value = ''; // Limpiar el campo de texto
                loadComments(); // Recargar los comentarios
            } else {
                alert('Error al enviar el comentario: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error al enviar el comentario:', error);
        });
    });
});