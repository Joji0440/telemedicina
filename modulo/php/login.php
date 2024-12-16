<?php
session_start();
include 'conexion.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $loginUser = $_POST['loginUser'];
    $loginPassword = $_POST['loginPassword'];
    $recaptchaResponse = isset($_POST['g-recaptcha-response']) ? $_POST['g-recaptcha-response'] : '';

    // Verificar reCAPTCHA
    $recaptchaSecret = '6Le6KpYqAAAAALLjV1mrvwdZQzivRT58HcvL6RtT';
    $recaptchaUrl = 'https://www.google.com/recaptcha/api/siteverify';
    $recaptchaData = [
        'secret' => $recaptchaSecret,
        'response' => $recaptchaResponse
    ];

    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => http_build_query($recaptchaData)
        ]
    ];

    $context = stream_context_create($options);
    $verify = file_get_contents($recaptchaUrl, false, $context);
    $captchaSuccess = json_decode($verify);

    if ($captchaSuccess->success) {
        $sql = "SELECT * FROM users WHERE email = ? OR name = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $loginUser, $loginUser);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($loginPassword, $user['password'])) {
            $_SESSION['user_id'] = $user['id']; // Guarda el ID del usuario en la sesión

            if ($user['role'] === 'patient') {
                // Obtener el ID del paciente de la tabla patients
                $sql = "SELECT id FROM patients WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $patient = $result->fetch_assoc();

                if ($patient) {
                    $_SESSION['patient_id'] = $patient['id']; // Guarda el ID del paciente en la sesión
                }

                echo json_encode([
                    "message" => "Inicio de sesión exitoso",
                    "user" => $user,
                    "patient_id" => $patient['id'] ?? null
                ]);
            } elseif ($user['role'] === 'doctor') {
                // Obtener el ID del doctor de la tabla doctors
                $sql = "SELECT id FROM doctors WHERE user_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $user['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $doctor = $result->fetch_assoc();

                if ($doctor) {
                    $_SESSION['doctor_id'] = $doctor['id']; // Guarda el ID del doctor en la sesión
                }

                echo json_encode([
                    "message" => "Inicio de sesión exitoso",
                    "user" => $user,
                    "doctor_id" => $doctor['id'] ?? null
                ]);
            } else {
                echo json_encode(["error" => "Rol de usuario no reconocido"]);
            }
        } else {
            echo json_encode(["error" => "Usuario o contraseña incorrectos"]);
        }

        $stmt->close();
        $conn->close();
    } else {
        echo json_encode(["error" => "Verificación de reCAPTCHA fallida"]);
    }
} else {
    echo json_encode(["error" => "Método no permitido"]);
}
?>