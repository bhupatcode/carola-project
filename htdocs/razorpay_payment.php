<?php
include "include/config.php";
// require('vendor/autoload.php');// Include Razorpay SDK


// 🔑 Razorpay API Keys
$keyId = 'rzp_test_lFfdAvwRtocJ83';
$keySecret = 'hzszbJxefW7Otvh7tsaarvf4';

$examount = $_GET['examount']; // Amount from previous page
$booking_id = $_GET['booking_id'];
$new_start_date = $_GET['new_start_date'];
$new_end_date = $_GET['new_end_date'];

// 🎯 Razorpay API Order Create URL
$orderUrl = "https://api.razorpay.com/v1/orders";


$orderData = [
    'receipt'         => "order_" . rand(),
    'amount'          => $examount * 100, // Convert to paisa
    'currency'        => 'INR',
    'payment_capture' => 1 // Auto capture payment
];

// 🛠️ Initialize cURL Request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $orderUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($ch, CURLOPT_USERPWD, $keyId . ":" . $keySecret);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);

$response = curl_exec($ch);
curl_close($ch);

$order = json_decode($response, true);
$order_id = $order['id']; // 🔥 Order ID Razorpay se mil gaya

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Payment</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body>
    <script>
        var options = {
            "key": "<?php echo $keyId; ?>",
            "amount": "<?php echo $orderData['amount']; ?>",
            "currency": "INR",
            "name": "Car Rental Service",
            "description": "Booking Modification Payment",
            "order_id": "<?php echo $order_id; ?>",
            "handler": function (response){
                window.location.href = "update_booking.php?booking_id=<?php echo $booking_id; ?>&new_start_date=<?php echo $new_start_date; ?>&new_end_date=<?php echo $new_end_date; ?>&examount=<?php echo $examount?>";
            }
        };
        var rzp1 = new Razorpay(options);
        rzp1.open();
    </script>
</body>
</html>
