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
        $this->Cell(110, 15, utf8_decode('Comprobante Médico'), 1, 1, 'C', 0); // AnchoCelda, AltoCelda, título, borde, saltoLinea, posición, ColorFondo
        $this->Ln(10); // Salto de línea
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15); // Posición: a 1,5 cm del final
        $this->SetFont('Arial', 'I', 8); // Tipo de fuente, cursiva, tamaño
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C'); // Pie de página (número de página)
    }

    // Contenido del comprobante
    function Comprobante($data)
    {
        $this->SetFont('Arial', '', 12);
        $this->SetTextColor(0, 0, 0); // Color de texto

        $this->Cell(0, 10, utf8_decode('Datos del Paciente'), 0, 1, 'L');
        $this->Cell(0, 10, 'Nombre: ' . utf8_decode($data['patient_name']), 0, 1, 'L');
        $this->Cell(0, 10, 'Correo: ' . utf8_decode($data['patient_email']), 0, 1, 'L');
        $this->Cell(0, 10, 'Teléfono: ' . utf8_decode($data['patient_phone']), 0, 1, 'L');
        $this->Ln(10);

        $this->Cell(0, 10, utf8_decode('Datos del Médico'), 0, 1, 'L');
        $this->Cell(0, 10, 'Nombre: ' . utf8_decode($data['doctor_name']), 0, 1, 'L');
        $this->Cell(0, 10, 'Especialidad: ' . utf8_decode($data['doctor_specialty']), 0, 1, 'L');
        $this->Cell(0, 10, 'Correo: ' . utf8_decode($data['doctor_email']), 0, 1, 'L');
        $this->Ln(10);

        $this->Cell(0, 10, utf8_decode('Datos de la Cita'), 0, 1, 'L');
        $this->Cell(0, 10, 'Fecha: ' . utf8_decode($data['appointment_date']), 0, 1, 'L');
        $this->Cell(0, 10, 'Hora: ' . utf8_decode($data['appointment_time']), 0, 1, 'L');
        $this->Cell(0, 10, 'Estado: ' . utf8_decode($data['appointment_status']), 0, 1, 'L');
        $this->Ln(10);
    }
}

// Obtener datos de la cita
$appointment_id = $_GET['appointment_id'];
$sql = "SELECT a.date AS appointment_date, a.time AS appointment_time, a.status AS appointment_status,
               up.name AS patient_name, up.email AS patient_email, p.phone AS patient_phone,
               ud.name AS doctor_name, d.specialty AS doctor_specialty, ud.email AS doctor_email
        FROM appointments a
        JOIN patients p ON a.patient_id = p.id
        JOIN doctors d ON a.doctor_id = d.id
        JOIN users up ON p.user_id = up.id
        JOIN users ud ON d.user_id = ud.id
        WHERE a.id = ?";
$stmt = $conn->prepare($sql);

// Manejo de errores
if ($stmt === false) {
    die('Error en la consulta SQL: ' . $conn->error);
}

$stmt->bind_param("i", $appointment_id);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$pdf = new PDF();
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->Comprobante($data);
$pdf->Output();
?>
