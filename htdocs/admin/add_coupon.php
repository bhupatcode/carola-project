<?php
include 'include/config.php'; // Database Connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $code = $_POST['code'];
    $discount = $_POST['discount'];
    $discount_type = $_POST['discount_type'];
    $expiry_date = $_POST['expiry_date'];
    $usage_limit = $_POST['usage_limit'];

    // Insert Query
    $query = "INSERT INTO coupons (code, discount, discount_type, expiry_date, usage_limit) 
              VALUES ('$code', '$discount', '$discount_type', '$expiry_date', '$usage_limit')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Coupon Created Successfully!'); window.location.href='add_coupon.php';</script>";
    } else {
        echo "<script>alert('Error Creating Coupon: " . mysqli_error($conn) . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Coupon</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
        }

        h2 {
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }

        label {
            font-weight: 500;
            display: block;
            margin-top: 10px;
            text-align: left;
            color: #555;
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            transition: all 0.3s ease-in-out;
        }

        input:focus, select:focus {
            border-color: #4CAF50;
            outline: none;
            box-shadow: 0 0 5px rgba(76, 175, 80, 0.5);
        }

        .generate-btn {
            background: #007bff;
            color: white;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            transition: 0.3s;
        }

        .generate-btn:hover {
            background: #0056b3;
        }

        button {
            background: #28a745;
            color: white;
            padding: 12px;
            margin-top: 15px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s;
            width: 100%;
        }

        button:hover {
            background: #218838;
        }

        @media (max-width: 768px) {
            .container {
                width: 90%;
            }
        }
    </style>
</head>

<body>

    <div class="container">
        <h2>Create Discount Coupon</h2>
        <form method="POST" onsubmit="return validateForm()">
            <label>Coupon Code:</label>
            <div style="display: flex; gap: 5px;">
                <input type="text" id="code" name="code" required>
                <button type="button" class="generate-btn" onclick="generateCoupon()">Generate</button>
            </div>

            <label>Discount Amount:</label>
            <input type="number" name="discount" id="discount" required>

            <label>Discount Type:</label>
            <select name="discount_type">
                <option value="fixed">Fixed (₹)</option>
                <option value="percentage">Percentage (%)</option>
            </select>

            <label>Expiry Date:</label>
            <input type="date" name="expiry_date" required>

            <label>Usage Limit:</label>
            <input type="number" name="usage_limit" value="1" required>

            <button type="submit">Generate Coupon</button>
        </form>
    </div>

    <script>
        function generateCoupon() {
            let characters = "ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            let couponCode = "";
            for (let i = 0; i < 8; i++) {
                couponCode += characters.charAt(Math.floor(Math.random() * characters.length));
            }
            document.getElementById("code").value = couponCode;
        }

        function validateForm() {
            let discount = document.getElementById("discount").value;
            if (discount <= 0) {
                alert("Discount amount must be greater than zero.");
                return false;
            }
            return true;
        }
    </script>

</body>

</html>
