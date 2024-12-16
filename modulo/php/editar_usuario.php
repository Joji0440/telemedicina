<?php
session_start();
include 'conexion.php';

// Verificar si el usuario es un administrador
if (!isset($_SESSION['admin_id'])) {
    echo json_encode(["error" => "No se encontró el ID del administrador en la sesión"]);
    exit;
}

$id = $_POST['id'];
$role = $_POST['role'];
$name = $_POST['name'];
$email = $_POST['email'];
$password = $_POST['password'];

if ($role === 'patientsTable') {
    $phone = $_POST['phone'];
    $canton = $_POST['canton'];
    $localidad = $_POST['localidad'];

    $sql = "UPDATE users u 
            JOIN patients p ON u.id = p.user_id 
            SET u.name = ?, u.email = ?, u.phone = ?, p.phone = ?, p.canton = ?, p.localidad = ? 
            WHERE p.id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("ssssssi", $name, $email, $phone, $phone, $canton, $localidad, $id);
} elseif ($role === 'doctorsTable') {
    $specialty = $_POST['specialty'];
    $credentials = $_POST['credentials'];

    $sql = "UPDATE users u 
            JOIN doctors d ON u.id = d.user_id 
            SET u.name = ?, u.email = ?, d.specialty = ?, d.credentials = ? 
            WHERE d.id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("ssssi", $name, $email, $specialty, $credentials, $id);
} elseif ($role === 'adminsTable') {
    $sql = "UPDATE admins SET name = ?, email = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("ssi", $name, $email, $id);
} else {
    echo json_encode(["error" => "Rol no válido"]);
    exit;
}

if ($stmt->execute()) {
    if (!empty($password)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        if ($role === 'patientsTable' || $role === 'doctorsTable') {
            $sql_password = "UPDATE users SET password = ? WHERE id = (SELECT user_id FROM patients WHERE id = ?)";
        } elseif ($role === 'adminsTable') {
            $sql_password = "UPDATE admins SET password = ? WHERE id = ?";
        }
        $stmt_password = $conn->prepare($sql_password);
        if ($stmt_password === false) {
            echo json_encode(["error" => "Error al preparar la consulta de contraseña: " . $conn->error]);
            exit;
        }
        $stmt_password->bind_param("si", $hashedPassword, $id);
        $stmt_password->execute();
        $stmt_password->close();
    }
    echo json_encode(["message" => "Datos actualizados correctamente"]);
} else {
    echo json_encode(["error" => "Error al actualizar los datos: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>