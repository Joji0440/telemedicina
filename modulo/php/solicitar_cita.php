<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $doctor_id = $_POST['doctor_id'];
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];
    $user_id = $_SESSION['user_id']; // Asumiendo que el ID del usuario está almacenado en la sesión

    // Verificar que el usuario es un paciente registrado
    $sql_check_patient = "SELECT id FROM patients WHERE user_id = ?";
    $stmt_check_patient = $conn->prepare($sql_check_patient);
    if ($stmt_check_patient === false) {
        echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
        exit;
    }
    $stmt_check_patient->bind_param("i", $user_id);
    $stmt_check_patient->execute();
    $result_check_patient = $stmt_check_patient->get_result();
    $patient = $result_check_patient->fetch_assoc();

    if (!$patient) {
        echo json_encode(["error" => "El usuario no es un paciente registrado"]);
        exit;
    }

    $patient_id = $patient['id'];

    // Verificar disponibilidad del médico
    $sql_disponibilidad = "SELECT * FROM doctor_availability WHERE doctor_id = ? AND day_of_week = DAYNAME(?) AND start_time <= ? AND end_time >= ?";
    $stmt_disponibilidad = $conn->prepare($sql_disponibilidad);
    if ($stmt_disponibilidad === false) {
        echo json_encode(["error" => "Error al preparar la consulta de disponibilidad: " . $conn->error]);
        exit;
    }
    $stmt_disponibilidad->bind_param("isss", $doctor_id, $fecha, $hora, $hora);
    $stmt_disponibilidad->execute();
    $result_disponibilidad = $stmt_disponibilidad->get_result();

    if ($result_disponibilidad->num_rows === 0) {
        echo json_encode(["error" => "El médico no está disponible en la fecha y hora seleccionadas"]);
        exit;
    }

    // Insertar la cita en la base de datos
    $sql = "INSERT INTO appointments (doctor_id, patient_id, date, time, status) VALUES (?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("iiss", $doctor_id, $patient_id, $fecha, $hora);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Error al solicitar la cita: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>