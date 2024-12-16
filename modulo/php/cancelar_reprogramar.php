<?php
include 'conexion.php';

function cancelarCita($appointment_id) {
    global $conn;
    $sql = "UPDATE appointments SET status = 'cancelled' WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $appointment_id);
    $stmt->execute();
    $stmt->close();
}

function reprogramarCita($appointment_id, $nueva_fecha, $nueva_hora) {
    global $conn;
    $sql = "UPDATE appointments SET date = ?, time = ?, status = 'pending' WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $nueva_fecha, $nueva_hora, $appointment_id);
    $stmt->execute();
    $stmt->close();
}
?>