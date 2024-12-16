<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es un administrador
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["error" => "No se encontró el ID del administrador en la sesión"]);
    exit;
}

// Obtener datos de pacientes
$sql_patients = "SELECT p.id, u.name, u.email, p.phone, p.canton, p.localidad 
                 FROM patients p 
                 JOIN users u ON p.user_id = u.id";
$result_patients = $conn->query($sql_patients);
$patients = $result_patients->fetch_all(MYSQLI_ASSOC);

// Obtener datos de doctores
$sql_doctors = "SELECT d.id, u.name, u.email, d.specialty, d.credentials 
                FROM doctors d 
                JOIN users u ON d.user_id = u.id";
$result_doctors = $conn->query($sql_doctors);
$doctors = $result_doctors->fetch_all(MYSQLI_ASSOC);

// Obtener datos de administradores
$sql_admins = "SELECT id, name, email FROM admins";
$result_admins = $conn->query($sql_admins);
$admins = $result_admins->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    "patients" => $patients,
    "doctors" => $doctors,
    "admins" => $admins
]);

$conn->close();
?>