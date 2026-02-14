<?php

function downloadFile($url, $path) {
    $content = file_get_contents($url);
    if ($content === FALSE) {
        die("Error fetching file: $url");
    }
    file_put_contents($path, $content);
}

// Download PHPMailer files (once per execution)
downloadFile("https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/PHPMailer.php", "PHPMailer.php");
downloadFile("https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/SMTP.php", "SMTP.php");
downloadFile("https://raw.githubusercontent.com/PHPMailer/PHPMailer/master/src/Exception.php", "Exception.php");

// Include downloaded files
require "PHPMailer.php";
require "SMTP.php";
require "Exception.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

error_reporting(0);
@include "include/config.php";
// for approve 
$did = $_GET['did'];
$email = $_GET['userEmail'];

// confirm booking 
if (isset($_REQUEST['aid'])) {
    $aid = intval($_GET['aid']);
    $vid = $_GET['vid'];
    $did = $_GET['did'];
    $email = $_GET['userEmail'];




    $status = 1;
    $update = "update booking set status=$status where bookingno=$aid";
    $q = mysqli_query($conn, $update);

    // $update1 = "update car_list set status=$status where vid=$vid";
    // $q1 = mysqli_query($conn, $update1);

    // $updatedriver="UPDATE  driver SET status=1 where  did=$did";
    // $exupdatedriver=mysqli_query($conn,$updatedriver);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Change to your SMTP provider
        $mail->SMTPAuth = true;
        $mail->Username = 'carolarental3@gmail.com';
        $mail->Password = 'xiysjnbhlejdkyok';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('carolarental3@gmail.com', 'CarOla');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Booking confirm!';
        $mail->Body    = "
        Welcome To Carola. <br>
Your Booking is done!<br>    
        <h2>Thank You</h2>";
        $mail->send();
        echo "<script>alert('Booking Done email sent!');</script>";


        echo "<script>alert('Approve success')
window.open('confirmed-booking.php', 'second');</script>";
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
}


// for cancel booking
// for cancel booking
// for User cancel booking

// Check if form is submitted via POST
if (isset($_REQUEST['canid'])) {
    $canid = intval($_GET['canid']);
    $vid = $_GET['vid'];
    $did = $_GET['did'];
    $email = $_GET['userEmail'];

   
    $status = 2;

    // Update booking with cancellation reason
    $update = "UPDATE booking SET status=$status WHERE bookingno=$canid";
    if (mysqli_query($conn, $update)) {
        echo "Booking cancelled successfully!";
    } else {
        die("Error updating booking: " . mysqli_error($conn));
    }

    // Update car status
    $update1 = "UPDATE car_list SET status=0 WHERE vid=$vid";
    mysqli_query($conn, $update1);

    // Update driver status
    if (!is_null($did)) {
        $updatedriver = "UPDATE driver SET status=0 WHERE did=$did";
        mysqli_query($conn, $updatedriver);
    }

    // Email Notification
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'carolarental3@gmail.com';
        $mail->Password = 'xiysjnbhlejdkyok';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('carolarental3@gmail.com', 'CarOla');
        $mail->addAddress($email); // User's email from form

        $mail->isHTML(true);
        $mail->Subject = 'Booking Cancellation Notice';
        $mail->Body    = "
        <p>Dear User,</p>
        <p>We regret to inform you that your booking has been <b>cancelled</b>.</p>
     
        <p>We apologize for the inconvenience. Please try booking again.</p>
        <h2>Thank You</h2>";

        $mail->send();
        echo "<script>alert('Booking Cancelled and Email Sent!');</script>";
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
}
// Check if form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eaid'])) {
    $eaid = $_POST['eaid'];
    $vid = $_POST['vid'];
    $did = isset($_POST['did']) ? $_POST['did'] : null;
    $reason = mysqli_real_escape_string($conn, trim($_POST['reason']));

    if (empty($reason)) {
        die("Error: Cancellation reason is required.");
    }

    $status = 2;

    // Update booking with cancellation reason
    $update = "UPDATE booking SET status=$status, cancel_reason='$reason' WHERE bookingno=$eaid";
    if (mysqli_query($conn, $update)) {
        echo "Booking cancelled successfully!";
    } else {
        die("Error updating booking: " . mysqli_error($conn));
    }

    // Update car status
    $update1 = "UPDATE car_list SET status=0 WHERE vid=$vid";
    mysqli_query($conn, $update1);

    // Update driver status
    if (!is_null($did)) {
        $updatedriver = "UPDATE driver SET status=0 WHERE did=$did";
        mysqli_query($conn, $updatedriver);
    }

    // Email Notification
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'carolarental3@gmail.com';
        $mail->Password = 'xiysjnbhlejdkyok';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('carolarental3@gmail.com', 'CarOla');
        $mail->addAddress($_POST['userEmail']); // User's email from form

        $mail->isHTML(true);
        $mail->Subject = 'Booking Cancellation Notice';
        $mail->Body    = "
        <p>Dear User,</p>
        <p>We regret to inform you that your booking has been <b>cancelled</b>.</p>
        <p><b>Reason:</b> $reason</p>
        <p>We apologize for the inconvenience. Please try booking again.</p>
        <h2>Thank You</h2>";

        $mail->send();
        echo "<script>alert('Booking Cancelled and Email Sent!');</script>";
        echo "<script>window.location.href='canceled-booking.php';</script>";
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
}



// For Return Car booking
if (isset($_REQUEST['returnid'])) {
    $returnid = $_GET['returnid'];
    $vid = $_GET['vid'];
    $did = isset($_GET['did']) ? $_GET['did'] : null; // Check if did exists

    // Fetch booking details
    $query = "SELECT b.ToDate, b.amount, c.price 
              FROM booking b 
              JOIN car_list c ON b.vid = c.vid 
              WHERE b.bookingno = $returnid";
    $result = mysqli_query($conn, $query);
    $booking = mysqli_fetch_assoc($result);

    $returnDate = date('Y-m-d'); // Current date (return date)
    $toDate = $booking['ToDate']; // Original ToDate
    $totalAmount = $booking['amount']; // Previous total amount
    $perDayPrice = $booking['price']; // Per day price of the car

    if (strtotime($returnDate) > strtotime($toDate)) {
        // Car is returned late
        $extraDays = (strtotime($returnDate) - strtotime($toDate)) / (60 * 60 * 24);
        $extraCharge = $extraDays * $perDayPrice;
        $newTotal = $totalAmount + $extraCharge;

        echo "<script>
        if (confirm('Car is returned $extraDays days late. Extra charge: ₹$extraCharge. Update booking?')) {
        }
        </script>";

        // Update booking with new total and status
        $update = "UPDATE booking SET status=3, ReturnDate='$returnDate', amount=$newTotal WHERE bookingno=$returnid";
        mysqli_query($conn, $update);
    } else {
        // Car returned on time
        $update = "UPDATE booking SET status=3, ReturnDate='$returnDate' WHERE bookingno=$returnid";
        mysqli_query($conn, $update);
    }

    // Update car status
    $updatecar = "UPDATE car_list SET status=0 WHERE vid=$vid";
    mysqli_query($conn, $updatecar);

    // Update driver status only if $did exists
    if (!empty($did)) {
        $updatedriver = "UPDATE driver SET status=0 WHERE did=$did";
        mysqli_query($conn, $updatedriver);
    }

    // Send confirmation email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'carolarental3@gmail.com';
        $mail->Password = 'xiysjnbhlejdkyok';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('carolarental3@gmail.com', 'CarOla');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Car Returned';
        $mail->Body = "
        Welcome To Carola. <br>
        Your Car Is Returned Successfully!<br>
        " . ($extraDays > 0 ? "You have been charged for $extraDays extra day(s). Total Bill: ₹$newTotal" : "Try Again !!") . "
        <h2>Thank You</h2>";

        $mail->send();
        echo "<script>alert('Return Car email sent!');</script>";
        echo "<script>alert('Returned Booking'); window.open('return-booking.php', 'second');</script>";
    } catch (Exception $e) {
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="carolalogo-8.png">
    <title>Booking Details</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        @font-face {
    font-family: 'pop-regular';
    src: url('../font/Poppins-Regular.ttf');
}
        * {
            font-family: 'pop-regular';
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            width: 80%;
            margin: 30px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .title {
            text-align: center;
            color: red;
            font-size: 28px;
            margin-bottom: 20px;
        }

        .section {
            margin-bottom: 20px;
        }

        h3 {
            color: blue;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table td {
            padding: 8px 12px;
            border: 1px solid #ddd;
        }

        .details-table td strong {
            color: #555;
        }

        .buttons {
            text-align: center;
            margin-top: 20px;
        }

        .confirm-button,
        .cancel-button {
            padding: 10px 20px;
            font-size: 16px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .confirm-button {
            background-color: #4CAF50;
            color: white;
        }

        .cancel-button {
            background-color: #f44336;
            color: white;
        }

        .confirm-button:hover {
            background-color: #45a049;
        }

        .cancel-button:hover {
            background-color: #e53935;
        }

        .print-button {
            text-align: center;
            margin-top: 30px;
        }

        .print-button button {
            padding: 10px 20px;
            font-size: 16px;
            border: none;
            background-color: #008CBA;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }

        .print-button button:hover {
            background-color: #007B8A;
        }
         /* Modal Background */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Modal Content */
        .modal-content {
            background: white;
            padding: 20px;
            border-radius: 10px;
            width: 90%;
            max-width: 400px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            position: relative;
            text-align: center;
            transform: translateY(-50px);
            opacity: 0;
            animation: slideIn 0.5s forwards;
            margin-top: 180px;
            margin-left: 207px;
        }

        /* Close Button */
        .close {
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 22px;
            cursor: pointer;
        }

        /* Submit Button */
        .submit-btn {
            background: #ff4d4d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: 0.3s ease-in-out;
        }

        .submit-btn:hover {
            background: #cc0000;
        }
        #cancelModal
        {
            display: none;
        }
        /* Animation */
        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        /* Responsive Design */
        @media (max-width: 500px) {
            .modal-content {
                width: 95%;
                padding: 15px;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <?php

        $bno = $_GET['bno'];
        $uid = $_GET['userEmail'];
        //         $sql = "SELECT reguser.*, 
        //         car_list.cname, 
        //         booking.FromDate, 
        //         booking.ToDate, 
        //         booking.message, 
        //         booking.vid, 
        //         booking.status, 
        //         booking.PostingDate, 
        //         booking.id, 
        //         booking.bookingno, 
        //         DATEDIFF(booking.ToDate, booking.FromDate) as totalnodays, 
        //         car_list.price, 
        //         (DATEDIFF(booking.ToDate, booking.FromDate) * car_list.price) AS grand_total
        //  FROM booking 
        //  JOIN car_list ON car_list.vid = booking.vid 
        //  JOIN reguser ON reguser.email = booking.userEmail 
        //  WHERE booking.bookingno = $bno AND booking.userEmail='$uid'
        //  ORDER BY booking.PostingDate" ;
        $bm = "SELECT did FROM booking WHERE bookingno=$bno AND (userEmail='$uid' OR did='$did')";
        $exbm = mysqli_query($conn, $bm);

        if (mysqli_num_rows($exbm) > 0) {
            $sql = "SELECT reguser.*, 
               car_list.cname, 
               booking.FromDate, 
               booking.ToDate, 
               booking.message,
               booking.rent_type, 
               booking.vid, 
               booking.status, 
               booking.PostingDate, 
               booking.id, 
               booking.bookingno, 
               booking.userEmail,
               DATEDIFF(booking.ToDate, booking.FromDate) as totalnodays, 
               TIMESTAMPDIFF(HOUR, booking.FromDate, booking.ToDate) AS total_hours,
               car_list.price, 
               car_list.chprice, 
               
               (DATEDIFF(booking.ToDate, booking.FromDate) * car_list.price) AS grand_total,
                (TIMESTAMPDIFF(HOUR, booking.FromDate, booking.ToDate) * car_list.chprice) AS grand_totalh,

                (DATEDIFF(booking.ToDate, booking.FromDate) * driver.dprice) AS grand_total_day_d,
                (TIMESTAMPDIFF(HOUR, booking.FromDate, booking.ToDate) * driver.hprice) AS grand_total_hour_d,

               (DATEDIFF(booking.ToDate, booking.FromDate) * driver.dprice)
               +(DATEDIFF(booking.ToDate, booking.FromDate) * car_list.price) AS grand_totald,

               (TIMESTAMPDIFF(HOUR, booking.FromDate, booking.ToDate) * driver.hprice)
               +(TIMESTAMPDIFF(HOUR, booking.FromDate, booking.ToDate) * car_list.chprice) AS grand_total_h,
               driver.dfname, 
               driver.did, 
               driver.dprice, 
               driver.hprice, 
               driver.status as driver_status
        FROM booking
        JOIN car_list ON car_list.vid = booking.vid
        JOIN reguser ON reguser.email = booking.userEmail
        LEFT JOIN driver ON driver.did = booking.did
        WHERE booking.bookingno = $bno
        ORDER BY booking.PostingDate";
            $result = mysqli_query($conn, $sql);
        } else {
            echo "<script>alert('No Record Found');</script>";
            exit;
        }

        $na = mysqli_num_rows($result);
        while ($row = mysqli_fetch_assoc($result)) {
        ?>
            <h2 class="title">#<?php echo $row['bookingno']; ?> Booking Details</h2>

            <div class="section">
                <h3>User Details</h3>
                <table class="details-table">
                    <tr>
                        <td><strong>Booking No.</strong></td>
                        <td>#<?php echo $row['bookingno']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Name</strong></td>
                        <td><?php echo $row['name']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Email Id</strong></td>
                        <td><?php echo $row['email']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Contact No</strong></td>
                        <td><?php echo $row['mnumber']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Address</strong></td>
                        <td><?php echo $row['address']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Country</strong></td>
                        <td><?php echo "India"; ?></td>
                    </tr>
                </table>
            </div>

            <div class="section">
                <h3>Booking Details</h3>
                <table class="details-table">
                    <tr>
                        <td><strong>Vehicle Name</strong></td>
                        <td><?php echo $row['cname']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Booking Date</strong></td>
                        <td><?php echo $row['PostingDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>Rent Type</strong></td>
                        <td><?php echo $row['rent_type']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>From Date</strong></td>
                        <td><?php echo $row['FromDate']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>To Date</strong></td>
                        <td><?php echo $row['ToDate']; ?></td>
                    </tr>
                    <?php if ($row['rent_type'] == 'Day') { ?>
                        <tr>
                            <td><strong>Total Days</strong></td>
                            <td><?php echo $row['totalnodays']; ?></td>
                        </tr>

                        <tr>
                            <td><strong>Rent Per Day</strong></td>
                            <td><?php echo $row['price']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Car Grand Total</strong></td>
                            <td><?php echo $row['grand_total']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Booking Status</strong></td>
                            <?php if ($row['status'] == 0) {
                                echo "<td>Not Confirmed Yet</td>";
                            } elseif ($row['status'] == 1) {
                                echo "<td>Booked</td>";
                            } elseif ($row['status'] == 2) {
                                echo "<td>Cancelled</td>";
                            } elseif ($row['status'] == 3) {
                                echo "<td>Returned</td>";
                            }

                            ?>
                        </tr>
                        <tr>
                            <td><strong>Today Return Date</strong></td>
                            <td><?php echo date('Y-m-d') ?></td>
                        </tr>
                        <?php if ($row['did'] == "") { ?>
                            <tr>
                                <td><strong> Grand Total</strong></td>
                                <td><?php echo $row['grand_total']; ?></td>
                            </tr>
                        <?php } ?>
                    <?php } else { ?>
                        <tr>
                            <td><strong>Total Hours</strong></td>
                            <td><?php echo $row['total_hours']; ?></td>
                        </tr>

                        <tr>
                            <td><strong>Rent Per Hour</strong></td>
                            <td><?php echo $row['chprice']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Car Grand Total</strong></td>
                            <td><?php echo $row['grand_totalh']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Booking Status</strong></td>
                            <?php if ($row['status'] == 0) {
                                echo "<td>Not Confirmed Yet</td>";
                            } elseif ($row['status'] == 1) {
                                echo "<td>Booked</td>";
                            } elseif ($row['status'] == 2) {
                                echo "<td>Cancelled</td>";
                            } elseif ($row['status'] == 3) {
                                echo "<td>Returned</td>";
                            }

                            ?>
                        </tr>
                        <tr>
                            <td><strong>Last Return Date</strong></td>
                            <td></td>
                        </tr>
                        <?php if ($row['did'] == "") { ?>
                            <tr>
                                <td><strong> Grand Total</strong></td>
                                <td><?php echo $row['grand_totalh']; ?></td>
                            </tr>
                    <?php  }
                    } ?>
                </table>
            </div>

            <?php if ($row['did']) {   ?>
                <div class="section">
                    <h3>Driver Details</h3>
                    <table class="details-table">
                        <tr>
                            <td><strong>Driver Name</strong></td>
                            <td><?php echo $row['dfname']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Booking Date</strong></td>
                            <td><?php echo $row['PostingDate']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Rent Type</strong></td>
                            <td><?php echo $row['rent_type']; ?></td>
                        </tr>
                        <?php if ($row['rent_type'] == 'Day') { ?>
                            <tr>
                                <td><strong>Total Days</strong></td>
                                <td><?php echo $row['totalnodays']; ?></td>
                            </tr>

                            <tr>
                                <td><strong>Rent Per Day</strong></td>
                                <td><?php echo $row['dprice']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Driver Grand Total</strong></td>
                                <td><?php echo $row['grand_total_day_d']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Grand Total</strong></td>
                                <td><?php echo $row['grand_totald']; ?></td>
                            </tr>
                        <?php  } else { ?>
                            <tr>
                                <td><strong>Total Hours</strong></td>
                                <td><?php echo $row['total_hours']; ?></td>
                            </tr>

                            <tr>
                                <td><strong>Rent Per Hour</strong></td>
                                <td><?php echo $row['hprice']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Driver Grand Total</strong></td>
                                <td><?php echo $row['grand_total_hour_d']; ?></td>
                            </tr>
                            <tr>
                                <td><strong>Grand Total</strong></td>
                                <td><?php echo $row['grand_total_h']; ?></td>
                            </tr>
                        <?php  } ?>




                    </table>
                </div>

            <?php }
            if ($row['status'] == 0) { ?>
                <div class="buttons">
                    <!-- for approve -->
                    <a href="Approve.php?aid=<?php echo $row['bookingno']; ?> && vid=<?php echo $row['vid']; ?>&& userEmail=<?php echo $row['userEmail']; ?><?php echo !empty($row['did']) ? '&& did=' . $row['did'] : ''; ?>">
                        <button class="confirm-button" name="approve" onclick="return confirm('Do you really want to Approve this Booking')">
                            Confirm Booking
                        </button>
                    </a>

                    <!-- for Cancel -->
                     <!-- Cancel Button -->
                    <button class="cancel-button" onclick="openCancelModal('<?php echo $row['bookingno']; ?>', '<?php echo $row['vid']; ?>', '<?php echo $row['userEmail']; ?>', '<?php echo !empty($row['did']) ? $row['did'] : ''; ?>')">
                        Cancel Booking
                    </button>
                    <!-- Modal Popup reason -->
                    <!-- Cancel Booking Modal -->
                    <div id="cancelModal" class="modal">
                        <div class="modal-content animate">
                            <span class="close" onclick="closeCancelModal()">&times;</span>
                            <h3>Cancel Booking</h3>
                            <p>Enter reason for cancellation:</p>
                            <textarea id="cancelReason" rows="3" placeholder="Enter reason..."></textarea>
                            <br>
                            <button class="submit-btn" onclick="submitCancellation()">Submit</button>
                        </div>
                    </div>

                    <!-- JavaScript for Modal -->
                    <script>
                        function openCancelModal(bookingno, vid, userEmail, did) {
                            document.getElementById("cancelModal").style.display = "block";
                            document.getElementById("cancelModal").dataset.bookingno = bookingno;
                            document.getElementById("cancelModal").dataset.vid = vid;
                            document.getElementById("cancelModal").dataset.userEmail = userEmail;
                            document.getElementById("cancelModal").dataset.did = did;
                        }

                        function closeCancelModal() {
                            document.getElementById("cancelModal").style.display = "none";
                        }

                        function submitCancellation() {
                            var reason = document.getElementById("cancelReason").value;
                            if (reason.trim() === "") {
                                alert("Please enter a cancellation reason.");
                                return;
                            }

                            var modal = document.getElementById("cancelModal");
                            var bookingno = modal.dataset.bookingno;
                            var vid = modal.dataset.vid;
                            var userEmail = modal.dataset.userEmail;
                            var did = modal.dataset.did;

                            // Send data using AJAX to PHP file
                            var xhr = new XMLHttpRequest();
                            xhr.open("POST", "Approve.php", true);
                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
                            xhr.onreadystatechange = function() {
                                if (xhr.readyState === 4 && xhr.status === 200) {
                                    alert(xhr.responseText);
                                    closeCancelModal();
                                    location.reload();
                                }
                            };
                            var data = "eaid=" + bookingno + "&vid=" + vid + "&userEmail=" + userEmail + "&reason=" + encodeURIComponent(reason);
                            if (did) {
                                data += "&did=" + did;
                            }
                            xhr.send(data);
                        }
                    </script>

                </div>
            <?php  }
            if ($row['status'] == 1) { ?>
                <div class="buttons">
                    <!-- for return car -->
                    <a href="Approve.php?returnid=<?php echo $row['bookingno']; ?> && vid=<?php echo $row['vid']; ?> && userEmail=<?php echo $row['userEmail']; ?> <?php echo !empty($row['did']) ? '&& did=' . $row['did'] : ''; ?>">
                        <button class="confirm-button" name="approve" onclick="return confirm('Do you really want to Return Booked Car')">
                            Return Car
                        </button>
                        
                    </a>

                </div>
        <?php  }
        } ?>


        <div class="print-button">
            <button onclick="window.print()">Print</button>
            <button onclick="window.print()"><?php echo $na;  ?></button>
            <button onclick="refresh()"><?php echo "refresh";  ?></button>

        </div>
    </div>
    <script>
        function refresh()
        {
            location.reload();
        }
    </script>
</body>

</html>