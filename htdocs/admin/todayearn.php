<?php
@include "include/config.php";

// Timezone set karo
date_default_timezone_set('Asia/Kolkata');
// Aaj ki total earnings ka query

$todaydate = date('Y-m-d');
$sql = "SELECT 
            b.bookingno AS booking_number, 
            c.cname, 
            u.name AS user_name, 
            u.mnumber AS user_number, 
            b.amount AS total_amount
        FROM booking b
        JOIN car_list c ON b.vid = c.vid
        JOIN reguser u ON b.userEmail = u.email
        WHERE DATE(b.PostingDate) = '$todaydate'";
        // echo "SQL Query: " . $sql;

$result = mysqli_query($conn,$sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Total Earnings</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007BFF;
            color: white;
        }
        .total {
            font-size: 20px;
            font-weight: bold;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Total Earnings for Today (<?php echo $todaydate; ?>)</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
            <tr>
                <th>Booking Number</th>
                <th>Car Name</th>
                <th>User Name</th>
                <th>User Number</th>
                <th>Total Amount (₹)</th>
            </tr>
            <?php 
            $total_earning = 0;
            while ($row = mysqli_fetch_assoc($result)): 
                $total_earning += $row['total_amount'];
            ?>
                <tr>
                    <td><?php echo $row['booking_number']; ?></td>
                    <td><?php echo $row['cname']; ?></td>
                    <td><?php echo $row['user_name']; ?></td>
                    <td><?php echo $row['user_number']; ?></td>
                    <td><?php echo "₹" . number_format($row['total_amount'], 2); ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
        <p class="total">Total Earnings: ₹<?php echo number_format($total_earning, 2); ?></p>
    <?php else: ?>
        <p>No bookings found for today.</p>
    <?php endif; ?>

</div>

</body>
</html>

<?php 
// Close connection
mysqli_close($conn); 
?>