<?php
session_start();
include 'conexion.php';

if (!isset($_POST['appointment_id'], $_POST['action'])) {
    echo json_encode(["success" => false, "message" => "Datos incompletos."]);
    exit;
}

$appointment_id = intval($_POST['appointment_id']);
$action = $_POST['action'];
$status = '';

switch ($action) {
    case 'accept':
        $status = 'confirmed';
        $message = "Su cita con el Dr. %s ha sido aceptada.";
        break;
    case 'cancel':
        $status = 'cancelled';
        $message = "Su cita con el Dr. %s ha sido cancelada.";
        break;
    case 'complete':
        $status = 'completed';
        $message = "Su cita con el Dr. %s ha sido completada.";
        break;
    default:
        echo json_encode(["success" => false, "message" => "Acción no válida."]);
        exit;
}

$sql = "UPDATE appointments SET status = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["success" => false, "message" => "Error al preparar la consulta: " . $conn->error]);
    exit;
}
$stmt->bind_param("si", $status, $appointment_id);
if ($stmt->execute()) {
    // Obtener el nombre del médico y el ID del paciente
    $sql = "SELECT u.name AS doctor_name, p.user_id AS patient_user_id FROM appointments a
            JOIN doctors d ON a.doctor_id = d.id
            JOIN users u ON d.user_id = u.id
            JOIN patients p ON a.patient_id = p.id
            WHERE a.id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Error al preparar la consulta para obtener el nombre del médico y el user_id del paciente: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $doctor_name = $row['doctor_name'];
    $patient_user_id = $row['patient_user_id'];

    // Crear notificación
    $message = sprintf($message, $doctor_name);
    $sql = "INSERT INTO notifications (user_id, message, link, appointment_id, type) VALUES (?, ?, '', ?, 'estado')";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Error al preparar la consulta de notificación: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("isi", $patient_user_id, $message, $appointment_id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Estado actualizado a $status"]);
    } else {
        echo json_encode(["success" => false, "message" => "Error al crear la notificación: " . $stmt->error]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Error al actualizar el estado."]);
}

$stmt->close();
$conn->close();
?>