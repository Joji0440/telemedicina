<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es un administrador
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["error" => "No se encontró el ID del administrador en la sesión"]);
    exit;
}

$id = $_GET['id'];
$table = $_GET['table'];

if ($table === 'patientsTable') {
    $sql = "DELETE FROM patients WHERE id = ?";
} elseif ($table === 'doctorsTable') {
    $sql = "DELETE FROM doctors WHERE id = ?";
} elseif ($table === 'adminsTable') {
    $sql = "DELETE FROM admins WHERE id = ?";
} else {
    echo json_encode(["error" => "Tabla no válida"]);
    exit;
}

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
    exit;
}
$stmt->bind_param("i", $id);

if ($stmt->execute()) {
    echo json_encode(["message" => "Usuario eliminado correctamente"]);
} else {
    echo json_encode(["error" => "Error al eliminar el usuario: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>