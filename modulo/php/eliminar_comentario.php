<?php
include 'conexion.php';

// Verificar si se ha proporcionado un ID de comentario
if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'error' => 'ID de comentario no proporcionado']);
    exit;
}

$comment_id = $_GET['id'];

// Iniciar una transacción
$conn->begin_transaction();

try {
    // Eliminar respuestas anidadas si el comentario es un comentario padre
    $delete_replies = "DELETE FROM forum_posts WHERE parent_post_id = ?";
    $stmt = $conn->prepare($delete_replies);
    $stmt->bind_param('i', $comment_id);
    $stmt->execute();

    // Eliminar el comentario
    $delete_comment = "DELETE FROM forum_posts WHERE id = ?";
    $stmt = $conn->prepare($delete_comment);
    $stmt->bind_param('i', $comment_id);
    $stmt->execute();

    // Confirmar la transacción
    $conn->commit();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Revertir la transacción en caso de error
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close();
?>