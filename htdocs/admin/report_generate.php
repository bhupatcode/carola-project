<?php
@include "include/config.php";

$from_date = $to_date = $customer_name = $car_id = $status =$bno= "";

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Report Generation</title>
    <style>
         @font-face {
            font-family: 'pop-regular';
            src: url('../font/Poppins-Regular.ttf');
        }
        *
        {
            font-family: 'pop-regular';
        }

        body {
            
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
        }

        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .filters select,
        .filters input {
            padding: 8px;
            width: 200px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .btn {
            background-color:rgb(189, 44, 44);
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
            margin-left: 365px;
        }

        .btn:hover {
            background-color:rgb(197, 93, 93);
        }
        .btn-generate {
            background-color: #28a745;
            color: white;
            padding: 10px 15px;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }

        .btn-generate:hover {
            background-color: #218838;
        }

        table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #343a40;
            color: white;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>Generate Report</h2>
        <form method="POST">
            <div class="filters">
                <input type="date" name="from_date" value="<?php echo $from_date; ?>">
                <input type="date" name="to_date" value="<?php echo $to_date; ?>">
                <input type="text" name="customer" placeholder="Enter Customer Name" value="<?php echo $customer_name; ?>">
                <input type="text" name="bno" placeholder="Enter Booking NO " value="<?php echo $bno; ?>">
                <select name="car">
                    <option value="">Select Car</option>
                    <?php
                    $car_query = mysqli_query($conn, "SELECT * FROM car_list");
                    while ($car = mysqli_fetch_assoc($car_query)) {
                        $selected = ($car['vid'] == $car_id) ? "selected" : "";
                        echo "<option value='{$car['vid']}' $selected>{$car['cname']}</option>";
                    }
                    ?>
                </select>
                <select name="status">
                    <option value="">Select Status</option>
                    <option value="0" <?php if ($status == "0") echo "selected"; ?>>In Progress</option>
                    <option value="1" <?php if ($status == "1") echo "selected"; ?>>Success</option>
                    <option value="2" <?php if ($status == "2") echo "selected"; ?>>Cancelled</option>
                    <option value="3" <?php if ($status == "3") echo "selected"; ?>>Returned</option>
                </select>
                <button type="submit" class="btn-generate" name="generate">Generate Report</button>
            </div>
        </form>
    

        <?php





        if (isset($_POST['generate'])) {
            $from_date = $_POST['from_date'];
            $bno=$_POST['bno'];
            $to_date = $_POST['to_date'];
            $car_id = $_POST['car'];
            $customer_name = $_POST['customer'];
            $status = $_POST['status'];
            $query = "SELECT b.id, b.bookingno, b.FromDate, b.ToDate, b.status, b.amount, 
                 v.cname AS car_name, u.name AS customer_name
          FROM booking AS b
          JOIN car_list AS v ON b.vid = v.vid
          JOIN reguser AS u ON b.userEmail = u.email
          WHERE 1";  // Ye 1 condition lagane se baad me dynamically WHERE clause add kar sakte hain

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
            if ($bno !== "") {
                $query .= " AND b.bookingno = '$bno'";
            }
            $result = mysqli_query($conn, $query);
            echo "<table>
                    <tr>
                        <th>Booking ID</th>
                        <th>Car</th>
                        <th>Customer</th>
                        <th>FromDate</th>
                        <th>ToDate</th>
                        <th>Status</th>
                    </tr>";
            $status_labels = [
                0 => "In Progress",
                1 => "Success",
                2 => "Cancelled",
                3 => "Returned"
            ];
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>{$row['bookingno']}</td>
                            <td>{$row['car_name']}</td>
                            <td>{$row['customer_name']}</td>
                            <td>{$row['FromDate']}</td>
                            <td>{$row['ToDate']}</td>
                            <td>{$status_labels[$row['status']]}</td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='6' style='text-align:center;'>No records found</td></tr>";
            }
            echo "</table>";
        }
        ?>
    </div>
        <!-- PDF Export Button (Table ke upar) -->
        <form action="export_pdf.php" method="POST" style="margin-top: 10px;">
    <input type="hidden" name="from_date" value="<?php echo $from_date; ?>">
    <input type="hidden" name="to_date" value="<?php echo $to_date; ?>">
    <input type="hidden" name="customer" value="<?php echo $customer_name; ?>">
    <input type="hidden" name="car" value="<?php echo $car_id; ?>">
    <input type="hidden" name="status" value="<?php echo $status; ?>">
    <input type="hidden" name="bno" value="<?php echo $bno; ?>">
    <button type="submit" class="btn">Export PDF</button>
</form> 
</body>

</html>