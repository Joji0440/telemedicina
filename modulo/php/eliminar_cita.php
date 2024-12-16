<?php
session_start();
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $appointment_id = $_GET['appointment_id'];

    // Verificar el estado de la cita antes de eliminarla
    $sql_check = "SELECT status FROM appointments WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("i", $appointment_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();
    $appointment = $result_check->fetch_assoc();

    if ($appointment['status'] === 'completed' || $appointment['status'] === 'cancelled') {
        $sql = "DELETE FROM appointments WHERE id = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
            exit;
        }
        $stmt->bind_param("i", $appointment_id);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Cita eliminada correctamente"]);
        } else {
            echo json_encode(["error" => "Error al eliminar la cita: " . $stmt->error]);
        }

        $stmt->close();
    } else {
        echo json_encode(["error" => "Solo se pueden eliminar citas completadas o canceladas"]);
    }

    $stmt_check->close();
    $conn->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>