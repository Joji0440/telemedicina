<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["error" => "No se encontró el ID del usuario en la sesión"]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Verificar que el usuario existe en la tabla patients
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

$sql = "SELECT a.id AS appointment_id, a.date, a.time, d.user_id AS doctor_id, u.name AS doctor_name
        FROM appointments a
        JOIN doctors d ON a.doctor_id = d.id
        JOIN users u ON d.user_id = u.id
        WHERE a.patient_id = ? AND a.status IN ('confirmed', 'completed')";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
    exit;
}
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$citas = [];
while ($row = $result->fetch_assoc()) {
    $citas[] = $row;
}

echo json_encode($citas);

$stmt->close();
$conn->close();
?>