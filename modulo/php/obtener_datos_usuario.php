<?php
session_start();

$response = [
    'patient_id' => $_SESSION['patient_id'] ?? null
];

header('Content-Type: application/json');
echo json_encode($response);
?>