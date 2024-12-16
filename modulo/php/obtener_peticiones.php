<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['doctor_id'])) {
    echo json_encode(["error" => "No se encontró el ID del doctor en la sesión"]);
    exit;
}

$doctor_id = $_SESSION['doctor_id'];

$sql = "SELECT a.id AS appointment_id, a.date, a.time, a.status, p.user_id AS patient_id, u.name AS patient_name
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN users u ON p.user_id = u.id
        WHERE a.doctor_id = ?
        AND a.status IN ('pending', 'confirmed', 'cancelled', 'completed')
        ORDER BY a.date DESC, a.time DESC";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
    exit;
}
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

$peticiones = [];
while ($row = $result->fetch_assoc()) {
    $peticiones[] = $row;
}

echo json_encode($peticiones);

$stmt->close();
$conn->close();
?>