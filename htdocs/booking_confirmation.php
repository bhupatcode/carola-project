<?php
session_start();
error_reporting(0);
include "include/config.php";
// require('vendor/autoload.php');

// Razorpay API Keys
$keyId = 'rzp_test_lFfdAvwRtocJ83';
$keySecret = 'hzszbJxefW7Otvh7tsaarvf4';



// Retrieve booking details
$bookingData = $_SESSION['booking_data'];
// $did=$_SESSION['driver_id'];

// Default Values
$discount_amount = 0;
$final_price = $bookingData['amount']; // Original Price
$coupon_applied = false;

// Apply Coupon Logic
if (isset($_POST['apply_coupon'])) {
    $coupon_code = mysqli_real_escape_string($conn, $_POST['coupon_code']);

    // Check Coupon Exists and is Valid
    $coupon_query = "SELECT * FROM coupons WHERE code='$coupon_code' AND expiry_date >= CURDATE() AND usage_limit > 0 LIMIT 1";
    $coupon_result = mysqli_query($conn, $coupon_query);

    if (mysqli_num_rows($coupon_result) > 0) {
        $coupon = mysqli_fetch_assoc($coupon_result);

        // Discount Calculation
        if ($coupon['discount_type'] == 'fixed') {
            $discount_amount = $coupon['discount'];
        } elseif ($coupon['discount_type'] == 'percentage') {
            $discount_amount = ($bookingData['amount'] * $coupon['discount']) / 100;
        }

        // Update Final Price
        $final_price = $bookingData['amount'] - $discount_amount;
        $coupon_applied = true;
         $_SESSION['booking_data']['amount']=$final_price;

        // Reduce Usage Limit
        $update_usage = "UPDATE coupons SET usage_limit = usage_limit - 1 WHERE cpid = '".$coupon['cpid']."'";
        mysqli_query($conn, $update_usage);

        echo "<script>alert('Coupon Applied! You saved ₹$discount_amount');</script>";
    } else {
        echo "<script>alert('Invalid or Expired Coupon!');</script>";
    }
}

// Ensure amount is in paise
$amount_in_paise = $_SESSION['booking_data']['amount'] * 100;

// 🎯 Razorpay API Order Create URL
$orderUrl = "https://api.razorpay.com/v1/orders";

// Create Razorpay order
$orderData = [
    'receipt'         => 'rcptid_' . $bookingData['bookingno'],
    'amount'          => $amount_in_paise,
    'currency'        => 'INR',
    'payment_capture' => 1
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
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="carolalogo-8.png">
    <title>Booking Confirmation</title>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <style>
       
       @font-face {
            font-family: 'pop-regular';
            src: url('font/Poppins-Regular.ttf');
        }
        body {
            font-family: 'pop-regular';
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom,rgb(221, 65, 91),rgb(202, 156, 28));
            color: #fff;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            overflow-x: hidden;
        
        }

        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
            padding: 20px;
        }

        .booking-box {
            background: #ffffff;
            color: #333;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
            padding-top:10px;
        }

        .car-image {
            width: 100%;
            max-height: 270px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .booking-details {
            text-align: left;
            /* margin-top: 15px; */
        }

        .booking-details p strong {
            min-width: 150px;
        }

        .booking-details p {
            background: rgba(0, 0, 0, 0.05);
            padding: 5px;
            display: flex;
            /* justify-content: space-between; */
            align-items: center;
        }

        .pay-btn, .back-btn, .apply-btn {
            background: #e63946;
            color: #fff;
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: 0.3s;
            margin-top: 15px;
        }

        

        footer {
            text-align: center;
            padding: 10px;
            background: #4e1163;
            color: white;
        }
        .apply-btn {
            background: #28a745;
        }
        .pay-btn:hover { background: #cc2f39; }
        .apply-btn:hover { background: #218838; }
        input[type="text"] {
            padding: 8px;
            width: 60%;
            margin-right: 10px;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <header>
        <?php include('navbar.php'); ?>
    </header>

    <?php
    $vid = $bookingData['vid'];
    $query = "SELECT * from car_list where vid=$vid";

    // $query = "select * from car_list where vid=$vid";

    $exquery = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_array($exquery)) {
        $image = explode(",", $row['image']);

    ?>
        <div class="container">
            <div class="booking-box">
                <h1>Booking Confirmation</h1>
                <img src="admin/<?php echo $image[0]; ?>" alt="Car Image" class="car-image">
                <div class="booking-details">
                    <p><strong>Booking No:</strong> <?php echo $bookingData['bookingno']; ?></p>
                    <p><strong>Car Name  :</strong> <?php echo $row['cname']; ?></p>
                    <?php } ?>
                <p><strong>User Email:</strong> <?php echo $bookingData['useremail']; ?></p>
                <p><strong>Pickup Date:</strong> <?php echo date('d-m-Y H:i:s', strtotime($bookingData['fdate'])); ?>
                <p>
                <p><strong>Drop-off Date:</strong> <?php echo date('d-m-Y H:i:s', strtotime($bookingData['tdate'])); ?>
                <p>
                <p><strong>Pickup Location:</strong> <?php echo $bookingData['pick_up_loc']; ?></p>
                <p><strong>Drop-off Location:</strong> <?php echo $bookingData['drop_of_loc']; ?></p>
                <p><strong>Rent Type:</strong> <?php echo $bookingData['rent_type']; ?></p>
                <?php if($bookingData['did']) { ?>
                <p><strong>Driver Name:</strong> <?php echo $bookingData['dname']; ?></p>
                <?php } ?>
                <!-- Coupon Apply Form -->
        <form method="POST" action="">
            <input type="text" name="coupon_code" placeholder="Enter Coupon Code" required>
            <button type="submit" name="apply_coupon" class="apply-btn">Apply Coupon</button>
        </form>

                <!-- Price Summary -->
        <p><strong>Original Amount:</strong> ₹<?php echo $bookingData['amount']; ?></p>
        <p><strong>Discount:</strong> ₹<?php echo $discount_amount; ?></p>
        <p><strong>Final Amount:</strong> ₹<?php echo $final_price; ?></p>
     

            </div>

            <div class="payment-section">
                <!-- <b class="h2">Payment</b> -->
                <button class="back-btn" onclick="goBack()"><i style="font-weight:900;margin-right:10px;"class="fas fa-arrow-left"></i>Back</button>
                <button class="pay-btn" onclick="payNow()">Pay ₹<?php echo $final_price; ?></button>

            </div>
        </div>

        <?php include('footer.php'); ?>

        <script>
            function payNow() {
                console.log('Payment function called');

                var options = {
                    "key": "<?php echo $keyId; ?>",
                    "amount": "<?php echo $amount_in_paise; ?>",
                    "currency": "INR",
                    "name": "Carola",
                    "description": "Payment for Booking Car",
                    "order_id": "<?php echo $order_id; ?>",
                    "handler": function(response) {
                        console.log('Payment successful');
                        window.location.href = 'payment_success.php?payment_id=' + response.razorpay_payment_id + '&order_id=' + response.razorpay_order_id + '&signature=' + response.razorpay_signature;
                    },
                    "prefill": {
                        "name": "<?php echo $bookingData['useremail']; ?>",
                        "email": "<?php echo $bookingData['useremail']; ?>",
                        "contact": "<?php echo $_SESSION["number"]; ?>"
                    },
                    "theme": {
                        "color": "#631579"
                    }
                };

                var rzp1 = new Razorpay(options);
                rzp1.open();
            }
        </script>

        <!--  for  back button -->
        <script>
            function goBack() {
                window.history.back();
            }
        </script>


</body>

</html>