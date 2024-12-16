<?php
session_start();
include 'conexion.php';

// Verificar si el ID del usuario está en la sesión
if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "No se encontró el ID del usuario en la sesión"]);
    exit;
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT u.name AS nombre, u.email AS correo, u.gender AS genero, u.language AS idioma, u.phone AS telefono, u.timezone AS zona_horaria, 
        d.id AS doctor_id, d.specialty AS especialidad, d.credentials AS credenciales, 
        cp.cancellation_notice_hours AS horas_cancelacion, cp.max_wait_time_minutes AS max_wait_time
        FROM users u 
        JOIN doctors d ON u.id = d.user_id 
        LEFT JOIN cancellation_policies cp ON d.id = cp.doctor_id
        WHERE u.id = ?";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
    exit;
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$doctor = $result->fetch_assoc();

if (!$doctor) {
    echo json_encode(["error" => "No se encontraron datos del médico"]);
    exit;
}

// Obtener los horarios de atención
$sql_horarios = "SELECT id, day_of_week, start_time, end_time FROM doctor_availability WHERE doctor_id = ?";
$stmt_horarios = $conn->prepare($sql_horarios);
if ($stmt_horarios === false) {
    echo json_encode(["error" => "Error al preparar la consulta de horarios: " . $conn->error]);
    exit;
}
$stmt_horarios->bind_param("i", $doctor['doctor_id']);
$stmt_horarios->execute();
$result_horarios = $stmt_horarios->get_result();
$horarios = [];
while ($row = $result_horarios->fetch_assoc()) {
    $horarios[] = $row;
}

$doctor['horarios'] = $horarios;

echo json_encode($doctor);

$stmt->close();
$conn->close();
?>