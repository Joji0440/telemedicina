<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es un administrador
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["error" => "No se encontró el ID del administrador en la sesión"]);
    exit;
}

$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$sql = "INSERT INTO admins (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
    exit;
}
$stmt->bind_param("sss", $name, $email, $hashedPassword);

if ($stmt->execute()) {
    echo json_encode(["message" => "Administrador agregado correctamente"]);
} else {
    echo json_encode(["error" => "Error al agregar el administrador: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>