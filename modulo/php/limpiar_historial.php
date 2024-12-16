<?php
include 'conexion.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $patient_id = $_SESSION['patient_id'] ?? null;

    if ($patient_id) {
        $stmt = $conn->prepare("DELETE FROM appointment_history WHERE patient_id = ?");
        if ($stmt) {
            $stmt->bind_param("i", $patient_id);
            if ($stmt->execute()) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to prepare statement']);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'Patient ID not provided']);
    }

    $conn->close();
}
?>