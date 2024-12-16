<?php
session_start();
include 'conexion.php';

if (!isset($_SESSION['patient_id'])) {
    echo json_encode(["error" => "No se encontró el ID del paciente en la sesión"]);
    exit;
}

$patient_id = $_SESSION['patient_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['current_password']) && isset($_POST['new_password'])) {
        // Cambiar contraseña
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];

        // Verificar la contraseña actual
        $sql = "SELECT u.password FROM users u 
                JOIN patients p ON u.id = p.user_id 
                WHERE p.id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
            exit;
        }
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $stmt->bind_result($hashed_password);
        $stmt->fetch();
        $stmt->close();

        if (!password_verify($current_password, $hashed_password)) {
            echo json_encode(["error" => "La contraseña actual es incorrecta"]);
            exit;
        }

        // Actualizar la contraseña
        $new_hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
        $sql = "UPDATE users u 
                JOIN patients p ON u.id = p.user_id 
                SET u.password = ? 
                WHERE p.id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
            exit;
        }
        $stmt->bind_param("si", $new_hashed_password, $patient_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Contraseña cambiada correctamente"]);
        } else {
            echo json_encode(["error" => "Error al actualizar la contraseña"]);
        }

        $stmt->close();
    } else {
        // Actualizar los datos del usuario
        if (isset($_POST['nombre']) && isset($_POST['fecha-nacimiento']) && isset($_POST['telefono']) && isset($_POST['canton']) && isset($_POST['localidad'])) {
            $nombre = $_POST['nombre'];
            $fecha_nacimiento = $_POST['fecha-nacimiento'];
            $telefono = $_POST['telefono'];
            $canton = $_POST['canton'];
            $localidad = $_POST['localidad'];

            $sql = "UPDATE users u 
                    JOIN patients p ON u.id = p.user_id 
                    SET u.name = ?, u.phone = ?, p.dob = ?, p.phone = ?, p.canton = ?, p.localidad = ? 
                    WHERE p.id = ?";
            $stmt = $conn->prepare($sql);
            if ($stmt === false) {
                echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
                exit;
            }
            $stmt->bind_param("ssssssi", $nombre, $telefono, $fecha_nacimiento, $telefono, $canton, $localidad, $patient_id);

            if ($stmt->execute()) {
                echo json_encode(["message" => "Datos actualizados correctamente"]);
            } else {
                echo json_encode(["error" => "Error al actualizar los datos"]);
            }

            $stmt->close();
        } else {
            echo json_encode(["error" => "Faltan datos para actualizar el perfil"]);
        }
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

$conn->close();
?>