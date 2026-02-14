<?php
// Razorpay API Credentials
$api_key = 'rzp_test_lFfdAvwRtocJ83';
$api_secret = 'hzszbJxefW7Otvh7tsaarvf4';

// Payment ID jiska refund karna hai
$payment_id = 'pay_Q1rqsbFUl4Dmsp';
$refund_amount = 1000 * 100; // Amount in paisa (1 INR = 100 paisa)

// Razorpay API URL for refund
$url = "https://api.razorpay.com/v1/payments/$payment_id/refund";

// cURL request setup
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_USERPWD, "$api_key:$api_secret");
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode([
    "amount" => $refund_amount
]));

// API Response
$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Response handling
if ($http_code == 200) {
    $refund_response = json_decode($response, true);
    echo "Refund Successful! Refund ID: " . $refund_response['id'];
} else {
    echo "Refund Failed! Response: " . $response;
}
?>
