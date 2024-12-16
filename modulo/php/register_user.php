<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $phone = $_POST['phone'];
    $canton = $_POST['canton'];
    $localidad = $_POST['localidad'];
    $role = 'patient'; // Rol de usuario

    // Validar formato del correo electrónico
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/@(hotmail|gmail|outlook|live)\.(com|ec)$/', $email)) {
        echo json_encode(["error" => "Por favor, ingrese un correo electrónico válido."]);
        exit;
    }

    // Verificar si el correo electrónico ya está registrado
    $sql = "SELECT id FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        echo json_encode(["error" => "El correo electrónico ya está registrado."]);
        exit;
    }
    $stmt->close();

    $sql = "INSERT INTO users (name, email, password, role, gender, phone) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $email, $password, $role, $gender, $phone);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;
        $sql_patient = "INSERT INTO patients (user_id, dob, canton, localidad, phone) VALUES (?, ?, ?, ?, ?)";
        $stmt_patient = $conn->prepare($sql_patient);
        $stmt_patient->bind_param("issss", $user_id, $dob, $canton, $localidad, $phone);
        if ($stmt_patient->execute()) {
            echo json_encode(["message" => "Registro exitoso"]);
        } else {
            echo json_encode(["error" => "Error en el registro de paciente"]);
        }
        $stmt_patient->close();
    } else {
        echo json_encode(["error" => "Error en el registro"]);
    }

    $stmt->close();
}
?>