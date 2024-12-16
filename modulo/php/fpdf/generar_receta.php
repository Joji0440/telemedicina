<?php
session_start();
include '../conexion.php';
require('./fpdf.php');

class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        $this->SetFont('Arial', 'B', 19); // Tipo de fuente, negrita, tamaño
        $this->Cell(80); // Movernos a la derecha
        $this->SetTextColor(0, 0, 0); // Color
        $this->Cell(110, 15, utf8_decode('Receta Médica'), 1, 1, 'C', 0); // AnchoCelda, AltoCelda, título, borde, saltoLinea, posición, ColorFondo
        $this->Ln(10); // Salto de línea
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15); // Posición: a 1,5 cm del final
        $this->SetFont('Arial', 'I', 8); // Tipo de fuente, cursiva, tamaño
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C'); // Pie de página (número de página)
    }

    // Contenido de la receta
    function Receta($data)
    {
        $this->SetFont('Arial', '', 12);
        $this->SetTextColor(0, 0, 0); // Color de texto

        $this->Cell(0, 10, utf8_decode('Datos del Paciente'), 0, 1, 'L');
        $this->Cell(0, 10, 'Nombre: ' . utf8_decode($data['patient_name']), 0, 1, 'L');
        $this->Ln(10);

        $this->Cell(0, 10, utf8_decode('Receta Médica'), 0, 1, 'L');
        $this->MultiCell(0, 10, utf8_decode($data['receta']), 0, 'L');
        $this->Ln(10);
    }
}

// Obtener los datos de la receta desde la solicitud POST
$data = json_decode(file_get_contents('php://input'), true);

// Verificar que patient_id está presente en los datos
if (!isset($data['patient_id'])) {
    echo json_encode(["error" => "El ID del paciente no está presente en los datos"]);
    exit;
}

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Receta($data);

// Asegurarse de que la carpeta 'recetas' existe
$recetasDir = __DIR__ . '/recetas';
if (!is_dir($recetasDir)) {
    mkdir($recetasDir, 0777, true);
}

// Guardar el PDF en el servidor
$pdfFileName = 'receta_medica_' . time() . '.pdf';
$pdfFilePath = $recetasDir . '/' . $pdfFileName;
$pdf->Output('F', $pdfFilePath);

// Ruta accesible desde el navegador
$pdfFileUrl = '../php/fpdf/recetas/' . $pdfFileName;

// Obtener el ID del doctor desde la sesión
$doctor_id = $_SESSION['user_id'];

// Obtener el nombre del doctor
$sql_doctor = "SELECT name FROM users WHERE id = ?";
$stmt_doctor = $conn->prepare($sql_doctor);
if ($stmt_doctor === false) {
    echo json_encode(["error" => "Error al preparar la consulta de doctor: " . $conn->error]);
    exit;
}
$stmt_doctor->bind_param("i", $doctor_id);
$stmt_doctor->execute();
$result_doctor = $stmt_doctor->get_result();
$doctor = $result_doctor->fetch_assoc();
$doctor_name = $doctor['name'];

// Insertar la notificación en la base de datos
$sql_notificacion = "INSERT INTO notifications (user_id, message, is_read, link, type) VALUES (?, ?, 0, ?, 'receta')";
$stmt_notificacion = $conn->prepare($sql_notificacion);
if ($stmt_notificacion === false) {
    echo json_encode(["error" => "Error al preparar la consulta de notificación: " . $conn->error]);
    exit;
}
$message = "Usted ha recibido una nueva receta médica de parte de " . $doctor_name;
$stmt_notificacion->bind_param("iss", $data['patient_id'], $message, $pdfFileUrl);
if (!$stmt_notificacion->execute()) {
    echo json_encode(["error" => "Error al ejecutar la consulta de notificación: " . $stmt_notificacion->error]);
    exit;
}

echo json_encode(["success" => true]);

$stmt_doctor->close();
$stmt_notificacion->close();
$conn->close();
?>
