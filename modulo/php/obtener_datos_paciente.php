<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['patient_id'])) {
    echo json_encode(["error" => "No se encontró el ID del paciente en la sesión"]);
    exit;
}

$patient_id = $_SESSION['patient_id'];

$sql = "SELECT u.name AS nombre, u.email AS correo, p.dob AS fecha_nacimiento, p.phone AS telefono, 
        p.canton, p.localidad 
        FROM users u 
        JOIN patients p ON u.id = p.user_id 
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $data = $result->fetch_assoc();
    echo json_encode($data);
} else {
    echo json_encode(["error" => "No se encontraron datos del paciente"]);
}

$stmt->close();
$conn->close();
?>