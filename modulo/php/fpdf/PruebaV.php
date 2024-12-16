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
        $this->Cell(110, 15, utf8_decode('Reporte de Usuarios Life & Health'), 1, 1, 'C', 0); // AnchoCelda, AltoCelda, título, borde, saltoLinea, posición, ColorFondo
        $this->Ln(10); // Salto de línea
    }

    // Pie de página
    function Footer()
    {
        $this->SetY(-15); // Posición: a 1,5 cm del final
        $this->SetFont('Arial', 'I', 8); // Tipo de fuente, cursiva, tamaño
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo() . '/{nb}', 0, 0, 'C'); // Pie de página (número de página)
    }

    // Tabla de usuarios
    function UserTable($header, $data)
    {
        $this->SetFillColor(54, 71, 219); // Color de fondo
        $this->SetTextColor(255, 255, 255); // Color de texto
        $this->SetDrawColor(163, 163, 163); // Color de borde
        $this->SetFont('Arial', 'B', 11);

        // Cabecera
        foreach ($header as $col) {
            $this->Cell($col[1], 10, $col[0], 1, 0, 'C', 1);
        }
        $this->Ln();

        // Datos
        $this->SetTextColor(0, 0, 0); // Color de texto
        $this->SetFont('Arial', '', 10);
        foreach ($data as $row) {
            foreach ($row as $col) {
                $this->Cell($col[1], 10, utf8_decode($col[0]), 1, 0, 'C', 0);
            }
            $this->Ln();
        }
    }
}

// Verificar si el usuario es administrador
if (!isset($_SESSION['admin_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit;
}

// Obtener el tipo de reporte desde la solicitud GET
$report_type = $_GET['reportType'] ?? '';

// Inicializar datos y cabeceras
$data = [];
$header = [];
$title = '';

switch ($report_type) {
    case 'patients':
        $query = "SELECT u.name, u.email, p.phone, p.canton, p.localidad
                  FROM patients p
                  JOIN users u ON p.user_id = u.id";
        $result = $conn->query($query);
        if (!$result) {
            die('Error en la consulta: ' . $conn->error);
        }
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                [$row['name'], 40],
                [$row['email'], 60],
                [$row['phone'], 30],
                [$row['canton'], 30],
                [$row['localidad'], 30]
            ];
        }
        $header = [['Nombre', 40], ['Correo', 60], ['Teléfono', 30], ['Cantón', 30], ['Localidad', 30]];
        $title = 'Pacientes Registrados';
        break;
    case 'doctors':
        $query = "SELECT u.name, u.email, d.specialty
                  FROM doctors d
                  JOIN users u ON d.user_id = u.id";
        $result = $conn->query($query);
        if (!$result) {
            die('Error en la consulta: ' . $conn->error);
        }
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                [$row['name'], 40],
                [$row['email'], 60],
                [$row['specialty'], 40]
            ];
        }
        $header = [['Nombre', 40], ['Correo', 60], ['Especialidad', 40]];
        $title = 'Doctores Registrados';
        break;
    case 'admins':
        $query = "SELECT name, email FROM admins";
        $result = $conn->query($query);
        if (!$result) {
            die('Error en la consulta: ' . $conn->error);
        }
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                [$row['name'], 60],
                [$row['email'], 80]
            ];
        }
        $header = [['Nombre', 60], ['Correo', 80]];
        $title = 'Administradores Registrados';
        break;
    case 'forum':
        $query = "SELECT fp.content AS comment, u.name AS author
                  FROM forum_posts fp
                  JOIN users u ON fp.user_id = u.id";
        $result = $conn->query($query);
        if (!$result) {
            die('Error en la consulta: ' . $conn->error);
        }
        while ($row = $result->fetch_assoc()) {
            $data[] = [
                [$row['author'], 40],
                [$row['comment'], 140]
            ];
        }
        $header = [['Autor', 40], ['Comentario', 140]];
        $title = 'Comentarios del Foro';
        break;
    default:
        echo json_encode(['success' => false, 'message' => 'Tipo de reporte no válido']);
        exit;
}

$pdf = new PDF('L', 'mm', 'A4'); // 'L' para orientación horizontal (landscape)
$pdf->AddPage();
$pdf->AliasNbPages();
$pdf->SetFont('Arial', '', 12);
$pdf->SetDrawColor(163, 163, 163); // Color de borde

// Título del reporte
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, $title, 0, 1, 'L');
$pdf->Ln(5);

// Tabla de datos
$pdf->UserTable($header, $data);

$conn->close();
$pdf->Output('I'); // 'I' para abrir en el navegador
?>
