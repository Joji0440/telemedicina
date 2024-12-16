<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $adminUser = $_POST['adminUser'];
    $adminPassword = $_POST['adminPassword'];

    // Verificar en la tabla `users` con rol de administrador
    $sql = "SELECT * FROM users WHERE (email = ? OR name = ?) AND role = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $adminUser, $adminUser);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin && password_verify($adminPassword, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['id']; // Guarda el ID del administrador en la sesión
        echo json_encode(["message" => "Inicio de sesión exitoso"]);
    } else {
        // Verificar en la tabla `admins`
        $sql = "SELECT * FROM admins WHERE email = ? OR name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $adminUser, $adminUser);
        $stmt->execute();
        $result = $stmt->get_result();
        $admin = $result->fetch_assoc();

        if ($admin && password_verify($adminPassword, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id']; // Guarda el ID del administrador en la sesión
            echo json_encode(["message" => "Inicio de sesión exitoso"]);
        } else {
            echo json_encode(["error" => "Usuario o contraseña incorrectos"]);
        }
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>