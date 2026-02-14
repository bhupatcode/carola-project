<?php
include "include/config.php";

$booking_id = $_GET['booking_id'];
$new_start_date = $_GET['new_start_date'];
$new_end_date = $_GET['new_end_date'];
$examount = $_GET['examount'];

// Get old amount from the database
$get_amount_query = "SELECT amount FROM booking WHERE id = $booking_id";
$result = mysqli_query($conn, $get_amount_query);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $old_amount = $row['amount'];
    $new_amount = $old_amount + $examount;

    // Update query with new amount
    $update_query = "UPDATE booking SET 
                    FromDate = '$new_start_date', 
                    ToDate = '$new_end_date',
                    amount = '$new_amount',
                    modification_status = 'approved'
                    WHERE id = $booking_id";

    if (mysqli_query($conn, $update_query)) {
        echo "<script>alert('Your booking has been modified successfully!');
        window.location.href='my_booking.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Error fetching old amount: " . mysqli_error($conn);
}
?>
