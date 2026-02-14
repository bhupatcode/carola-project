<?php
require('admin/fpdf/fpdf.php');
@include "include/config.php";

if (!isset($_GET['bookingno'])) {
    die("Invalid Booking");
}

$bookingno = $_GET['bookingno'];

$query = "SELECT booking.*, 
                 car_list.cname AS car_name, 
                 car_list.image, 
                 car_list.chprice, 
                 car_list.price, 
                 reguser.name AS customer_name, 
                 reguser.mnumber AS customer_phone, 
                 reguser.email AS customer_email, 
                 reguser.address AS customer_address, 
                 booking.payment AS payment_status,
                 DATEDIFF(booking.ToDate, booking.FromDate) AS total_days, 
                 TIMESTAMPDIFF(HOUR, booking.FromDate, booking.ToDate) AS total_hours
          FROM booking 
          JOIN car_list ON booking.vid = car_list.vid 
          JOIN reguser ON booking.userEmail = reguser.email
          WHERE booking.bookingno = '$bookingno'";

$result = mysqli_query($conn, $query);
$row = mysqli_fetch_assoc($result);

if (!$row) {
    die("Booking Not Found");
}

// Rental Cost Calculation
$duration = ($row['rent_type'] == 'Day') ? $row['total_days'] . " Days" : $row['total_hours'] . " Hours";
$rent_price = ($row['rent_type'] == 'Day') ? "Rs. " . $row['price'] : "Rs. " . $row['chprice'];
$driver_status = ($row['did'] == 0) ? "No" : "Yes";
$total_amount = "Rs. " . number_format($row['amount'], 2);

//  Payment Status (0 = Pending, 1 = Success)
$payment_status = ($row['payment_status'] == 1) ? "Success" : "Pending";

//  Invoice PDF Generation
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true);

//  Logo
$logo = 'carolalogo (1)-7.png'; 
if (file_exists($logo)) {
    $pdf->Image($logo, 10, 10, 30);
}

//  Invoice Title
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(190, 10, 'CarOla Invoice', 0, 1, 'C');
$pdf->Ln(5);

//  Company Details
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(190, 5, 'CarOla Rentals', 0, 1, 'C');
$pdf->Cell(190, 5, '57, opp. Hotel Lotus Botad, Gujarat - 364710', 0, 1, 'C');
$pdf->Cell(190, 5, 'Email: carolarental3@gmail.com | Phone: +91-7359509387', 0, 1, 'C');
$pdf->Ln(10);

//  Customer Details
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 8, 'Customer Details', 1, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 8, 'Name:', 1);
$pdf->Cell(140, 8, $row['customer_name'], 1, 1);
$pdf->Cell(50, 8, 'Email:', 1);
$pdf->Cell(140, 8, $row['customer_email'], 1, 1);
$pdf->Cell(50, 8, 'Phone:', 1);
$pdf->Cell(140, 8, $row['customer_phone'], 1, 1);
$pdf->Cell(50, 8, 'Address:', 1);
$pdf->MultiCell(140, 8, $row['customer_address'], 1);
$pdf->Ln(3);

//  Booking Details
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 8, 'Booking Details', 1, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 8, 'Booking No:', 1);
$pdf->Cell(140, 8, $row['bookingno'], 1, 1);
$pdf->Cell(50, 8, 'Car Name:', 1);
$pdf->Cell(140, 8, $row['car_name'], 1, 1);
$pdf->Cell(50, 8, 'From Date:', 1);
$pdf->Cell(140, 8, $row['FromDate'], 1, 1);
$pdf->Cell(50, 8, 'To Date:', 1);
$pdf->Cell(140, 8, $row['ToDate'], 1, 1);
$pdf->Cell(50, 8, 'Duration:', 1);
$pdf->Cell(140, 8, $duration, 1, 1);
$pdf->Cell(50, 8, 'Rent Type:', 1);
$pdf->Cell(140, 8, $row['rent_type'], 1, 1);
$pdf->Cell(50, 8, 'Driver Booked:', 1);
$pdf->Cell(140, 8, $driver_status, 1, 1);
$pdf->Ln(3);

//  Payment Details
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 8, 'Payment Details', 1, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 8, 'Per Day/Hour Price:', 1);
$pdf->Cell(140, 8, $rent_price, 1, 1);
$pdf->Cell(50, 8, 'Total Amount:', 1);
$pdf->Cell(140, 8, $total_amount, 1, 1);
$pdf->Cell(50, 8, 'Payment Status:', 1);
$pdf->Cell(140, 8, $payment_status, 1, 1);
$pdf->Ln(5);

//  Footer
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(190, 8, 'Thank you for choosing our service!', 0, 1, 'C');

//  Set Headers for Direct PDF Display
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="Invoice_' . $row['bookingno'] . '.pdf"');

//  Output PDF Directly in Browser
$pdf->Output();
?>
