<?php
session_start();
include 'conexion.php';

$user_id = $_SESSION['user_id']; // Asumiendo que el ID del usuario está almacenado en la sesión

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Obtener notificaciones
    $sql = "SELECT n.id, n.message, n.is_read, n.link, n.created_at, n.type, u.name AS doctor_name
            FROM notifications n
            LEFT JOIN appointments a ON n.appointment_id = a.id
            LEFT JOIN doctors d ON a.doctor_id = d.id
            LEFT JOIN users u ON d.user_id = u.id
            WHERE n.user_id = ?
            ORDER BY n.created_at DESC";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $notificaciones = [];
    $unreadCount = 0;
    while ($row = $result->fetch_assoc()) {
        $notificaciones[] = $row;
        if ($row['is_read'] == 0) {
            $unreadCount++;
        }
    }

    echo json_encode(['notificaciones' => $notificaciones, 'unreadCount' => $unreadCount]);

    $stmt->close();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Marcar notificación como vista
    $id = $_POST['id'];

    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Error al marcar la notificación como vista: " . $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

$conn->close();
?>