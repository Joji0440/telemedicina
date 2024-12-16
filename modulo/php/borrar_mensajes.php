<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $appointment_id = $_GET['cita_id'];
    $doctor_id = $_GET['doctor_id'];

    // Verificar que el usuario esté autenticado
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["error" => "Usuario no autenticado"]);
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Verificar que el usuario es el paciente de la cita
    $sql_check_patient = "SELECT patient_id FROM appointments WHERE id = ? AND patient_id = (SELECT id FROM patients WHERE user_id = ?)";
    $stmt_check_patient = $conn->prepare($sql_check_patient);
    if ($stmt_check_patient === false) {
        echo json_encode(["error" => "Error en la consulta SQL: " . $conn->error]);
        exit;
    }

    $stmt_check_patient->bind_param("ii", $appointment_id, $user_id);
    $stmt_check_patient->execute();
    $result_check_patient = $stmt_check_patient->get_result();

    if ($result_check_patient->num_rows === 0) {
        echo json_encode(["error" => "No tienes permiso para borrar los mensajes de esta cita"]);
        exit;
    }

    // Eliminar mensajes
    $sql = "DELETE FROM private_comments WHERE appointment_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Error en la consulta SQL: " . $conn->error]);
        exit;
    }

    $stmt->bind_param("i", $appointment_id);
    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Error al borrar los mensajes: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>