<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $appointment_id = $_GET['cita_id'] ?? null;

    if (!$appointment_id) {
        echo json_encode(["error" => "Faltan datos para obtener los mensajes"]);
        exit;
    }

    $current_user_id = $_SESSION['user_id']; // El usuario actual (paciente o doctor)

    $sql = "SELECT pc.message, pc.sender_id, pc.receiver_id, u.name AS sender_name
            FROM private_comments pc
            JOIN users u ON pc.sender_id = u.id
            WHERE pc.appointment_id = ?
            ORDER BY pc.sent_at ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $messages = [];
    while ($row = $result->fetch_assoc()) {
        // Determinar el rol del remitente en relación con el usuario actual
        $sender_role = ($row['sender_id'] == $current_user_id) ? 'you' : 'other';
        $messages[] = [
            "text" => $row['message'],
            "sender" => $sender_role,
            "sender_name" => $row['sender_name']
        ];
    }

    echo json_encode(["messages" => $messages]);

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>