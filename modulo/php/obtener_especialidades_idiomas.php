<?php
include 'conexion.php';

// Obtener especialidades únicas
$sql_especialidades = "SELECT DISTINCT specialty FROM doctors";
$result_especialidades = $conn->query($sql_especialidades);
$especialidades = [];
while ($row = $result_especialidades->fetch_assoc()) {
    $especialidades[] = $row['specialty'];
}

// Obtener idiomas únicos
$sql_idiomas = "SELECT DISTINCT language FROM users WHERE role = 'doctor'";
$result_idiomas = $conn->query($sql_idiomas);
$idiomas = [];
while ($row = $result_idiomas->fetch_assoc()) {
    $idiomas[] = $row['language'];
}

echo json_encode([
    'especialidades' => $especialidades,
    'idiomas' => $idiomas
]);

$conn->close();
?>