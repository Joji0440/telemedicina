<?php
session_start();
$patient_id = $_SESSION['patient_id']; 

include 'conexion.php';

// Consulta SQL para obtener el historial de citas del paciente
$sql = "SELECT 
            a.id AS appointment_id, 
            a.date, 
            a.time, 
            a.status,
            d.user_id AS doctor_id, 
            u.name AS doctor_name
        FROM appointments a
        INNER JOIN doctors d ON a.doctor_id = d.id
        INNER JOIN users u ON d.user_id = u.id
        WHERE a.patient_id = ?";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Error en la preparación de la consulta: " . $conn->error);
}
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

$historial = [];
while ($row = $result->fetch_assoc()) {
  $historial[] = $row;
}

$stmt->close();

// Devolver el historial en formato JSON
header('Content-Type: application/json');
echo json_encode($historial); 
?>