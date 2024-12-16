<?php
include 'conexion.php';

// Ejecutar la consulta SQL
$sql = "SELECT fp.id, fp.content, fp.created_at, u.name AS author, r.content AS reply, a.name AS admin_name
        FROM forum_posts fp
        JOIN users u ON fp.user_id = u.id
        LEFT JOIN forum_posts r ON fp.id = r.parent_post_id
        LEFT JOIN admins a ON r.user_id = a.id
        WHERE fp.parent_post_id IS NULL
        ORDER BY fp.created_at DESC";
$result = $conn->query($sql);

// Verificar si la consulta SQL fue exitosa
if ($result === false) {
    echo json_encode(["error" => "Error al ejecutar la consulta: " . $conn->error]);
    exit;
}

$comments = [];
while ($row = $result->fetch_assoc()) {
    $comments[] = $row;
}

header('Content-Type: application/json');
echo json_encode($comments);

$conn->close();
?>