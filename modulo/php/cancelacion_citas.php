<?php

include 'conexion.php';

// Obtener el ID de la cita
$citaId = $_GET['cita_id'];

// Obtener la fecha y hora actual
$fechaHoraActual = date('Y-m-d H:i:s');

// Obtener la fecha y hora de la cita y la política de cancelación
$sql = "SELECT a.date, a.time, a.patient_id, cp.cancellation_notice_hours 
        FROM appointments a
        INNER JOIN doctors d ON a.doctor_id = d.id
        LEFT JOIN cancellation_policies cp ON d.id = cp.doctor_id
        WHERE a.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $citaId);
$stmt->execute();
$result = $stmt->get_result();
$cita = $result->fetch_assoc();
$stmt->close();

if (!$cita) {
    echo json_encode(["error" => "Cita no encontrada."]);
    exit;
}

// Calcular la fecha límite de cancelación
$fechaHoraCita = $cita['date'] . ' ' . $cita['time'];
$fechaLimite = date('Y-m-d H:i:s', strtotime('-' . $cita['cancellation_notice_hours'] . ' hours', strtotime($fechaHoraCita)));

// Verificar si la cancelación cumple con la política
if ($fechaHoraActual < $fechaLimite) {
    // Actualizar el estado de la cita a "cancelada"
    $sql = "UPDATE appointments SET status = 'cancelled' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $citaId);

    if ($stmt->execute()) {
        // Guardar el historial en la tabla appointment_history
        $sqlInsert = "INSERT INTO appointment_history (appointment_id, patient_id, doctor_id, date, time, status) 
                      SELECT id, patient_id, doctor_id, date, time, status FROM appointments WHERE id = ?";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bind_param("i", $citaId);
        $stmtInsert->execute();
        $stmtInsert->close();

        echo json_encode(["message" => "Cita cancelada con éxito."]);
    } else {
        echo json_encode(["error" => "Error al cancelar la cita."]);
    }
    $stmt->close();
} else {
    echo json_encode(["error" => "La cita no se puede cancelar. La fecha límite para cancelar era " . $fechaLimite]);
}
?>