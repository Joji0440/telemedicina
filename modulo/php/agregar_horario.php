<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id']; // Asumiendo que el ID del usuario está almacenado en la sesión
    $day_of_week = $_POST['day_of_week'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    // Obtener el ID del doctor
    $sql_doctor = "SELECT id FROM doctors WHERE user_id = ?";
    $stmt_doctor = $conn->prepare($sql_doctor);
    if ($stmt_doctor === false) {
        echo json_encode(["error" => "Error al preparar la consulta de doctor: " . $conn->error]);
        exit;
    }
    $stmt_doctor->bind_param("i", $user_id);
    $stmt_doctor->execute();
    $result_doctor = $stmt_doctor->get_result();
    $doctor = $result_doctor->fetch_assoc();
    $doctor_id = $doctor['id'];

    // Insertar el nuevo horario
    $sql = "INSERT INTO doctor_availability (doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("isss", $doctor_id, $day_of_week, $start_time, $end_time);

    if ($stmt->execute()) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "Error al agregar el horario: " . $stmt->error]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>