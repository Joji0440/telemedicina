<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['doctor_id'])) {
    echo json_encode(["error" => "No se encontró el ID del doctor en la sesión"]);
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

$sql = "SELECT DISTINCT a.id AS appointment_id, a.date, a.time, p.user_id AS patient_id, u.name AS patient_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN users u ON p.user_id = u.id
        JOIN private_comments pc ON a.id = pc.appointment_id
        WHERE a.doctor_id = ? AND a.status = 'confirmed'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$chats = [];
while ($row = $result->fetch_assoc()) {
    $chats[] = $row;
}

echo json_encode($chats);

$stmt->close();
$conn->close();
?>