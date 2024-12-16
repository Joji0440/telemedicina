<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id']; // Asumiendo que el ID del usuario está almacenado en la sesión

    if (isset($_POST['current_password']) && isset($_POST['new_password'])) {
        // Cambiar contraseña
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];

        // Verificar la contraseña actual
        $sql = "SELECT password FROM users WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
            exit;
        }
        $stmt->bind_param("i", $user_id);
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
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
            exit;
        }
        $stmt->bind_param("si", $new_hashed_password, $user_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Contraseña cambiada correctamente"]);
        } else {
            echo json_encode(["error" => "Error al actualizar la contraseña"]);
        }

        $stmt->close();
    } else {
        // Actualizar los datos del médico
        if (isset($_POST['nombre']) && isset($_POST['telefono']) && isset($_POST['especialidad']) && isset($_POST['credenciales']) && isset($_POST['horas-cancelacion']) && isset($_POST['max-wait-time'])) {
            $nombre = $_POST['nombre'];
            $telefono = $_POST['telefono'];
            $especialidad = $_POST['especialidad'];
            $credenciales = $_POST['credenciales'];
            $horas_cancelacion = $_POST['horas-cancelacion'] ?: 0;
            $max_wait_time = $_POST['max-wait-time'] ?: 0;

            // Iniciar una transacción
            $conn->begin_transaction();

            try {
                // Actualizar los datos en la tabla users y doctors
                $sql = "UPDATE users u 
                        JOIN doctors d ON u.id = d.user_id 
                        SET u.name = ?, u.phone = ?, d.specialty = ?, d.credentials = ?
                        WHERE u.id = ?";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    throw new Exception("Error al preparar la consulta: " . $conn->error);
                }
                $stmt->bind_param("ssssi", $nombre, $telefono, $especialidad, $credenciales, $user_id);
                $stmt->execute();
                $stmt->close();

                // Verificar si ya existe una política de cancelación para el médico
                $sql = "SELECT id FROM cancellation_policies WHERE doctor_id = (SELECT id FROM doctors WHERE user_id = ?)";
                $stmt = $conn->prepare($sql);
                if ($stmt === false) {
                    throw new Exception("Error al preparar la consulta: " . $conn->error);
                }
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->bind_result($policy_id);
                $stmt->fetch();
                $stmt->close();

                if ($policy_id) {
                    // Actualizar la política de cancelación existente
                    $sql = "UPDATE cancellation_policies 
                            SET cancellation_notice_hours = ?, max_wait_time_minutes = ?
                            WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        throw new Exception("Error al preparar la consulta: " . $conn->error);
                    }
                    $stmt->bind_param("iii", $horas_cancelacion, $max_wait_time, $policy_id);
                } else {
                    // Insertar una nueva política de cancelación
                    $sql = "INSERT INTO cancellation_policies (doctor_id, cancellation_notice_hours, max_wait_time_minutes) 
                            VALUES ((SELECT id FROM doctors WHERE user_id = ?), ?, ?)";
                    $stmt = $conn->prepare($sql);
                    if ($stmt === false) {
                        throw new Exception("Error al preparar la consulta: " . $conn->error);
                    }
                    $stmt->bind_param("iii", $user_id, $horas_cancelacion, $max_wait_time);
                }

                $stmt->execute();
                $stmt->close();

                // Confirmar la transacción
                $conn->commit();

                echo json_encode(["success" => true, "message" => "Datos actualizados correctamente"]);
            } catch (Exception $e) {
                // Revertir la transacción en caso de error
                $conn->rollback();
                echo json_encode(["error" => $e->getMessage()]);
            }
        } else {
            echo json_encode(["error" => "Faltan datos para actualizar el perfil"]);
        }
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

$conn->close();
?>