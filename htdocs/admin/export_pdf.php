<?php
@include "include/config.php";
require('fpdf/fpdf.php');

$from_date = $_POST['from_date'] ?? '';
$to_date = $_POST['to_date'] ?? '';
$customer_name = $_POST['customer'] ?? '';
$car_id = $_POST['car'] ?? '';
$status = $_POST['status'] ?? '';

// Query filter lagane ke liye
$query = "SELECT b.bookingno, v.cname AS car_name, u.name AS customer_name, 
                 b.FromDate, b.ToDate, b.status
          FROM booking AS b
          JOIN car_list AS v ON b.vid = v.vid
          JOIN reguser AS u ON b.userEmail = u.email
          WHERE 1";  // WHERE 1 use karne se dynamic conditions easy ho jati hain

if (!empty($from_date) && !empty($to_date)) {
    $query .= " AND (b.FromDate BETWEEN '$from_date' AND '$to_date' 
                 OR b.ToDate BETWEEN '$from_date' AND '$to_date')";
}

if (!empty($car_id)) {
    $query .= " AND b.vid = '$car_id'";
}

if (!empty($customer_name)) {
    $query .= " AND u.name LIKE '%$customer_name%'";
}

if ($status !== "") {
    $query .= " AND b.status = '$status'";
}

$result = mysqli_query($conn, $query);

class PDF extends FPDF
{
    function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(190, 10, 'Booking Report', 0, 1, 'C');
        $this->Ln(5);
    }

    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Page ' . $this->PageNo(), 0, 0, 'C');
    }
}

$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetFillColor(220, 220, 220);

$widths = [25, 35, 35, 40, 40, 30];
$total_width = array_sum($widths); // Total width nikalna
$start_x = (210 - $total_width) / 2; // Center karne ke liye X position calculate karna

$pdf->SetX($start_x); // Table ko center karna
$headers = ['Booking ID', 'Car', 'Customer', 'FromDate', 'ToDate', 'Status'];

foreach ($headers as $index => $header) {
    $pdf->Cell($widths[$index], 10, $header, 1, 0, 'C', true);
}
$pdf->Ln();

$pdf->SetFont('Arial', '', 10);

$status_labels = [
    0 => "In Progress",
    1 => "Success",
    2 => "Cancelled",
    3 => "Returned"
];

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $pdf->SetX($start_x); // Har row ke liye table ko center karna

        $pdf->Cell($widths[0], 10, $row['bookingno'], 1);
        $pdf->Cell($widths[1], 10, $row['car_name'], 1);
        $pdf->Cell($widths[2], 10, $row['customer_name'], 1);
        $pdf->Cell($widths[3], 10, $row['FromDate'], 1);
        $pdf->Cell($widths[4], 10, $row['ToDate'], 1);
        $pdf->Cell($widths[5], 10, $status_labels[$row['status']], 1, 1);
    }
} else {
    $pdf->SetX($start_x); 
    $pdf->Cell($total_width, 10, 'No records found.', 1, 1, 'C'); 
}

$pdf->Output('D', 'filtered_booking_report.pdf');
exit();
