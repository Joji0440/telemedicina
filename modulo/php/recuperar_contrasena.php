<?php
include 'conexion.php';
require 'vendor/autoload.php'; // Asegúrate de que la ruta es correcta

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $username = $_POST['username']; 
    $phone = $_POST['phone'];

    // Verificar si el usuario existe
    $sql = "SELECT id, email, name, phone FROM users WHERE email = ? AND name = ? AND phone = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("sss", $email, $username, $phone);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Generar una nueva contraseña
        $newPassword = bin2hex(random_bytes(4)); // Generar una contraseña aleatoria de 8 caracteres
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);

        // Actualizar la contraseña en la base de datos
        $sql = "UPDATE users SET password = ? WHERE email = ? AND name = ? AND phone = ?";
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            echo json_encode(["error" => "Error al preparar la consulta: " . $conn->error]);
            exit;
        }
        $stmt->bind_param("ssss", $hashedPassword, $email, $username, $phone);

        if ($stmt->execute()) {
            // Enviar la nueva contraseña al correo electrónico del usuario usando PHPMailer
            $mail = new PHPMailer(true);
            try {
                // Configuración del servidor SMTP
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Cambia esto al servidor SMTP que estés usando
                $mail->SMTPAuth = true;
                $mail->Username = 'cuentamultiple50@gmail.com'; // Tu correo SMTP
                $mail->Password = 'uveq qopu xqqz kuhp'; // Tu contraseña SMTP
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Configuración del correo
                $mail->setFrom('no-reply@tuplataforma.com', 'Tu Plataforma');
                $mail->addAddress($email, $username);
                $mail->isHTML(true);
                $mail->Subject = 'Recuperación de contraseña';
                $mail->Body = "Hola $username,<br><br>Su nueva contraseña es: <strong>$newPassword</strong><br><br>Por favor, cambie su contraseña después de iniciar sesión.";

                $mail->send();
                echo json_encode(["message" => "Se ha enviado una nueva contraseña a su correo electrónico."]);
            } catch (Exception $e) {
                echo json_encode(["error" => "Error al enviar el correo electrónico: " . $mail->ErrorInfo]);
            }
        } else {
            echo json_encode(["error" => "Error al actualizar la contraseña."]);
        }
    } else {
        echo json_encode(["error" => "No se encontró un usuario con los datos proporcionados."]);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Método no permitido"]);
}

$conn->close();
?>