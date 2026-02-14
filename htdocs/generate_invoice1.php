<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require('admin/fpdf/fpdf.php');
@include "include/config.php";

if (!isset($_GET['bookingno'])) {
    die("Invalid Booking");
}

$bookingno = mysqli_real_escape_string($conn, $_GET['bookingno']);

// Booking Details Fetch
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

if (!$result) {
    die("Query Error: " . mysqli_error($conn));
}

$row = mysqli_fetch_assoc($result);
if (!$row) {
    die("Booking Not Found");
}

// Rental Cost Calculation
$rent_type = isset($row['rent_type']) ? $row['rent_type'] : 'Day';
$total_days = isset($row['total_days']) ? $row['total_days'] : 0;
$total_hours = isset($row['total_hours']) ? $row['total_hours'] : 0;
$duration = ($rent_type == 'Day') ? "$total_days Days" : "$total_hours Hours";

$rent_price = ($rent_type == 'Day') ? "Rs. " . $row['price'] : "Rs. " . $row['chprice'];
$car_rate_per_day = $row['price'];
$car_rate_per_hour = $row['chprice'];
$total_car_rent =  ($rent_type == 'Day') ? $car_rate_per_day * $total_days : $car_rate_per_hour * $total_hours;
$driver_status = isset($row['did']) && $row['did'] != 0 ? "Yes" : "No";

//  Driver Details Fetch
$driver_name = "N/A";
$driver_rate_per_day = 0;
$driver_rate_per_hour = 0;
$total_driver_cost = 0;

if ($driver_status == "Yes") {
    $driverQuery = "SELECT dfname, dprice, hprice FROM driver WHERE did = " . $row['did'];
    $driverResult = mysqli_query($conn, $driverQuery);
    if ($driverResult) {
        $driver = mysqli_fetch_assoc($driverResult);
        if ($driver) {
            $driver_name = $driver['dfname'];
            $driver_rate_per_day = $driver['dprice'];
            $driver_rate_per_hour = $driver['hprice'];

            $total_driver_cost = ($rent_type == 'Day') ? $driver_rate_per_day * $total_days : $driver_rate_per_hour * $total_hours;
        }
    }
}

//  Grand Total Calculation
$grand_total = $total_car_rent + $total_driver_cost;
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
$pdf->Ln(3);

//  Driver Details (Only if booked)
if ($driver_status == "Yes") {
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->Cell(190, 8, 'Driver Details', 1, 1, 'C');
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 8, 'Driver Name:', 1);
    $pdf->Cell(140, 8, $driver_name, 1, 1);
    $pdf->Cell(50, 8, 'Total Driver Cost:', 1);
    $pdf->Cell(140, 8, "Rs. " . number_format($total_driver_cost, 2), 1, 1);
    $pdf->Ln(3);
}

// Payment Details
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(190, 8, 'Payment Details', 1, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(50, 8, 'Car Rent:', 1);
$pdf->Cell(140, 8, "Rs. " . number_format($total_car_rent, 2), 1, 1);
$pdf->Cell(50, 8, 'Grand Total:', 1);
$pdf->Cell(140, 8, "Rs. " . number_format($grand_total, 2), 1, 1);
$pdf->Ln(5);

//  Footer
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(190, 8, 'Thank you for choosing our service!', 0, 1, 'C');

//  Output PDF
$pdf->Output();
?>
