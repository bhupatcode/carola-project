<?php
session_start();
@include "include/config.php";

$uid = $_SESSION['alogin'];

$limit = 5; // Per page records
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;
$from_date = isset($_GET['from_date']) ? $_GET['from_date'] : '';
$to_date = isset($_GET['to_date']) ? $_GET['to_date'] : '';

$whereClause = " WHERE booking.userEmail = '$uid' ";
if (!empty($from_date) && !empty($to_date)) {
    $whereClause .= " AND (booking.FromDate >= '$from_date' AND booking.ToDate <= '$to_date')";
}


// Total bookings count
$countQuery = "SELECT COUNT(*) AS total FROM booking $whereClause";
$countResult = mysqli_query($conn, $countQuery);
$rowCount = mysqli_fetch_assoc($countResult);
$totalRecords = $rowCount['total'];
$totalPages = ceil($totalRecords / $limit); // Calculate total pages

$booking = "SELECT booking.*, 
                  car_list.cname AS car_name, 
                  car_list.image, 
                  booking.rent_type, 
                  booking.did,
                  DATEDIFF(booking.ToDate, booking.FromDate) AS total_days, 
                  TIMESTAMPDIFF(HOUR, booking.FromDate, booking.ToDate) AS total_hours 
           FROM booking 
           JOIN car_list ON booking.vid = car_list.vid 
           $whereClause 
           ORDER BY booking.PostingDate DESC 
           LIMIT $limit OFFSET $offset";

$exbooking = mysqli_query($conn, $booking);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recent Bookings</title>
    <link rel="stylesheet" href="css/my_booking.css">
    <style>
        @font-face {
            font-family: 'pop-regular';
            src: url('../font/Poppins-Regular.ttf');
        }

        body {
            font-family: 'pop-regular';
        }

        .status {
            font-size: 17px;
        }

        /* Pagination Styling */
        .pagination {
            margin-top: 10px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 5px;
            text-decoration: none;
            color: white;
            background-color: #007bff;
            border-radius: 5px;
        }

        .pagination a.active {
            background-color: #0056b3;
            font-weight: bold;
        }

        .pagination a:hover {
            background-color: #0056b3;
        }

        .invoice-btn {
            padding: 5px 10px;
            background-color: green;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .invoice-btn:hover {
            background-color: darkgreen;
        }

        .search-form {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .search-form label {
            font-weight: bold;
            color: #333;
        }

        .search-form input {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
        }

        .search-form button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 16px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s ease-in-out;
        }

        .search-form button:hover {
            background-color: #0056b3;
        }

        @media screen and (max-width: 600px) {
            .search-form {
                flex-direction: column;
                gap: 12px;
            }

            .search-form input,
            .search-form button {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <div class="booking-container">

        <div class="booking-header">
            <span>Bookings History</span>
        </div>
        <form method="GET" action="" class="search-form">
            <label for="from_date">From Date:</label>
            <input type="date" name="from_date" id="from_date" value="<?php echo isset($_GET['from_date']) ? $_GET['from_date'] : ''; ?>">

            <label for="to_date">To Date:</label>
            <input type="date" name="to_date" id="to_date" value="<?php echo isset($_GET['to_date']) ? $_GET['to_date'] : ''; ?>">

            <button type="submit">Search</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>From Date</th>
                    <th>To Date</th>
                    <th>Rent Type</th>
                    <th>Driver</th>
                    <th>Days/Hours</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Invoice</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($exbooking)) {
                    $image = explode(",", $row['image']);
                ?>
                    <tr class="clickable-row" data-href="booking_details.php?bookingno=<?php echo $row['bookingno']; ?>">
                        <td><img src="../admin/<?php echo $image[0]; ?>" alt="Car Image" class="booking-img"></td>
                        <td><?php echo $row['car_name']; ?></td>
                        <td><?php echo $row['FromDate']; ?></td>
                        <td><?php echo $row['ToDate']; ?></td>
                        <td><?php echo $row['rent_type']; ?></td>
                        <td><?php echo ($row['did'] == 0) ? "No" : "Yes"; ?></td>
                        <td>
                            <?php
                            if ($row['rent_type'] == 'Day') {
                                echo $row['total_days'] . " Days";
                            } else if ($row['rent_type'] == 'hour') {
                                echo $row['total_hours'] . " Hours";
                            }
                            ?>
                        </td>
                        <td><?php echo $row['amount']; ?></td>
                        <td class="status">
                            <?php echo ($row['status'] == 0) ? 'In Progress' : (($row['status'] == 1) ? 'Success' : (($row['status'] == 2) ? 'Rejected' : 'Returned')); ?>
                        </td>
                        <td>
                            <a href="generate_invoice.php?bookingno=<?php echo $row['bookingno']; ?>"
                                class="invoice-btn" target="_blank">
                                Download
                            </a>
                            <!-- <a href="generate_invoice1.php?bookingno=<?php echo $row['bookingno']; ?>"
                                class="invoice-btn" target="_blank">
                                Download1
                            </a> -->
                        </td>

                    </tr>

                <?php } ?>
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="pagination">
            <?php if ($page > 1) { ?>
                <a href="?page=<?php echo $page - 1; ?>&from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>">← Prev</a>
            <?php } ?>

            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <a href="?page=<?php echo $i; ?>&from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>" class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php } ?>

            <?php if ($page < $totalPages) { ?>
                <a href="?page=<?php echo $page + 1; ?>&from_date=<?php echo $from_date; ?>&to_date=<?php echo $to_date; ?>">Next →</a>
            <?php } ?>
        </div>


        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const rows = document.querySelectorAll('.clickable-row');
                rows.forEach(row => {
                    row.addEventListener('click', function(event) {
                        // Agar click "Download" button par nahi hua to booking details page par redirect karo
                        if (!event.target.classList.contains('invoice-btn')) {
                            const href = this.getAttribute('data-href');
                            window.location.href = href;
                        }
                    });
                });

                // Download button ke click event ke liye stopPropagation
                const invoiceButtons = document.querySelectorAll('.invoice-btn');
                invoiceButtons.forEach(button => {
                    button.addEventListener('click', function(event) {
                        event.stopPropagation(); // Puri row ke click event ko rok do
                    });
                });
            });
        </script>

</body>

</html>