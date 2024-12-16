<?php

include 'conexion.php';

function buscarMedicos($especialidad, $idioma, $genero) {
    global $conn;

    // Construir la consulta SQL
    $sql = "SELECT 
                u.id AS user_id, 
                u.name, 
                u.language, 
                u.gender, 
                d.id AS doctor_id, 
                d.specialty
            FROM users u
            INNER JOIN doctors d ON u.id = d.user_id
            WHERE 1=1"; // Truco para facilitar la adición de condiciones

    if (!empty($especialidad)) {
        $sql .= " AND d.specialty = ?";
    }
    if (!empty($idioma)) {
        $sql .= " AND u.language = ?";
    }
    if (!empty($genero)) {
        $sql .= " AND u.gender = ?";
    }

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die("Error en la consulta SQL: " . $conn->error);
    }

    // Vincular parámetros
    $params = [];
    $types = '';
    if (!empty($especialidad)) {
        $params[] = $especialidad;
        $types .= 's';
    }
    if (!empty($idioma)) {
        $params[] = $idioma;
        $types .= 's';
    }
    if (!empty($genero)) {
        $params[] = $genero;
        $types .= 's';
    }

    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    // Procesar los resultados
    $medicos = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $medicos[] = $row;
        }
    }

    return $medicos;
}

// Obtener los parámetros de la URL
$especialidad = $_GET['especialidad'] ?? '';
$idioma = $_GET['idioma'] ?? '';
$genero = $_GET['genero'] ?? '';

// Llamar a la función buscarMedicos
$medicos = buscarMedicos($especialidad, $idioma, $genero);

// Devolver los resultados en formato JSON
header('Content-Type: application/json');
echo json_encode($medicos);

?>