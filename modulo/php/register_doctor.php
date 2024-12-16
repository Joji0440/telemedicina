<?php
include 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $specialties = $_POST['specialties'];
    $credentials = $_POST['credentials'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $language = $_POST['language'];

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

    // Insertar en la tabla users
    $sql = "INSERT INTO users (name, email, password, role, gender, language, phone) VALUES (?, ?, ?, 'doctor', ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $username, $email, $password, $gender, $language, $phone);

    if ($stmt->execute()) {
        $user_id = $stmt->insert_id;

        // Insertar en la tabla doctors
        $sql = "INSERT INTO doctors (user_id, specialty, credentials) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $specialties, $credentials);

        if ($stmt->execute()) {
            $doctor_id = $stmt->insert_id;

            // Insertar en la tabla doctor_availability
            $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            foreach ($days as $day) {
                $startTimeKey = "startTime$day";
                $endTimeKey = "endTime$day";
                if (isset($_POST[$startTimeKey]) && isset($_POST[$endTimeKey])) {
                    $startTime = $_POST[$startTimeKey];
                    $endTime = $_POST[$endTimeKey];
                    if (!empty($startTime) && !empty($endTime)) {
                        $sql = "INSERT INTO doctor_availability (doctor_id, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)";
                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param("isss", $doctor_id, $day, $startTime, $endTime);
                        $stmt->execute();
                    }
                }
            }

            echo json_encode(["message" => "Registro exitoso"]);
        } else {
            echo json_encode(["error" => "Error al registrar el doctor"]);
        }
    } else {
        echo json_encode(["error" => "Error al registrar el usuario"]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>