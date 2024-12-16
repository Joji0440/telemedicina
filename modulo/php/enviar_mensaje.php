<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $appointment_id = $_POST['cita_id'] ?? null;
    $receiver_id = $_POST['receiver_id'] ?? null;
    $message = $_POST['message'] ?? null;
    $sender_id = $_SESSION['user_id']; // El usuario actual (doctor o paciente)

    // Validar datos
    if (!$appointment_id || !$receiver_id || !$message) {
        echo json_encode(["error" => "Faltan datos para procesar el mensaje"]);
        exit;
    }

    // Verificar si el sender_id existe en la tabla users
    $sql_check_sender = "SELECT id, name FROM users WHERE id = ?";
    $stmt_check_sender = $conn->prepare($sql_check_sender);
    $stmt_check_sender->bind_param("i", $sender_id);
    $stmt_check_sender->execute();
    $result_check_sender = $stmt_check_sender->get_result();
    if ($result_check_sender->num_rows === 0) {
        echo json_encode(["error" => "El ID del remitente no existe"]);
        exit;
    }
    $sender = $result_check_sender->fetch_assoc();
    $sender_name = $sender['name'];

    // Verificar si el receiver_id existe en la tabla users
    $sql_check_receiver = "SELECT id FROM users WHERE id = ?";
    $stmt_check_receiver = $conn->prepare($sql_check_receiver);
    $stmt_check_receiver->bind_param("i", $receiver_id);
    $stmt_check_receiver->execute();
    $result_check_receiver = $stmt_check_receiver->get_result();
    if ($result_check_receiver->num_rows === 0) {
        echo json_encode(["error" => "El ID del receptor no existe"]);
        exit;
    }

    // Insertar mensaje
    $sql = "INSERT INTO private_comments (appointment_id, sender_id, receiver_id, message, sent_at) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iiis", $appointment_id, $sender_id, $receiver_id, $message);

    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => $message, "sender_name" => $sender_name]);
    } else {
        echo json_encode(["error" => "Error al guardar el mensaje en la base de datos"]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>