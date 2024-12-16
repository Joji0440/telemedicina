<?php

include 'conexion.php';

function obtenerDisponibilidad($doctorId) {
    global $conn;

    // Construir la consulta SQL para obtener la disponibilidad del médico
    $sql = "SELECT day_of_week, start_time, end_time FROM doctor_availability WHERE doctor_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $doctorId);
    $stmt->execute();
    $result = $stmt->get_result();

    $disponibilidad = [];
    while ($row = $result->fetch_assoc()) {
        $disponibilidad[] = $row;
    }

    $stmt->close();

    // Devolver la disponibilidad en formato JSON
    header('Content-Type: application/json');
    echo json_encode($disponibilidad);
}

if (isset($_GET['doctor_id'])) {
    obtenerDisponibilidad($_GET['doctor_id']);
} else {
    echo json_encode([]);
}

?>