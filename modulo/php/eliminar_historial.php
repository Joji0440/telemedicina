<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"), true);
    $id = $data['id'] ?? null;

    if ($id) {
        // Verificar que la cita está concluida o cancelada
        $stmt = $conn->prepare("SELECT status FROM appointments WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $cita = $result->fetch_assoc();
        $stmt->close();

        if ($cita && ($cita['status'] == 'completed' || $cita['status'] == 'cancelled')) {
            // Eliminar la cita de appointment_history
            $stmt = $conn->prepare("DELETE FROM appointment_history WHERE appointment_id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $stmt->close();

            // Eliminar la cita de appointments
            $stmt = $conn->prepare("DELETE FROM appointments WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Cita no concluida o cancelada']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'ID not provided']);
    }

    $conn->close();
}
?>