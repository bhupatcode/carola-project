<?php 
session_start();
@include "include/config.php";

$payment_id=$_GET['payment_id'];
$bookingdata=$_SESSION['booking_data'];
$did=$_SESSION['driver_id'];


$vid=$bookingdata['vid'];
$bookingno=$bookingdata['bookingno'];
$useremail=$bookingdata['useremail'];
$fdate=$bookingdata['fdate'];
$tdate=$bookingdata['tdate'];
$pick_up_loc=$bookingdata['pick_up_loc'];
$drop_of_loc=$bookingdata['drop_of_loc'];
$status=$bookingdata['status'];
$order_id=$bookingdata['order_id'];
$amount=$bookingdata['amount'];
// $amount=$_GET['amount'];
$rent_type=$bookingdata['rent_type'];
$dname=$bookingdata['dname'];
$did=$bookingdata['did'];
$payment=1;
$payment1='complated';
$payment_date = date("Y-m-d H:i:s");
// echo $vid;
// echo $bookingno;
// echo $useremail;
// echo $fdate;
// echo $tdate;
if($did=="")
{
    $sql = "INSERT INTO booking (bookingno, userEmail, vid,rent_type, FromDate, ToDate, status,pickup,dropoff,amount,payment) 
    VALUES ('$bookingno', '$useremail', '$vid','$rent_type', '$fdate', '$tdate', '$status','$pick_up_loc','$drop_of_loc',$amount ,$payment)";
$exsql=mysqli_query($conn,$sql);

$updatecar="UPDATE  car_list SET  status=1 WHERE  vid=$vid";
mysqli_query($conn,$updatecar);

$payment_query="insert into payment (payment_id,booking_id,payment_type,amount,payment_status,payment_date,order_id) values ('$payment_id','$bookingno','$rent_type','$amount','$payment1','$payment_date','$order_id')";
$expayment_query-mysqli_query($conn,$payment_query);
}
else
{
$sql = "INSERT INTO booking (bookingno, userEmail, vid,rent_type, FromDate, ToDate, status,pickup,dropoff,did,dname,amount,payment) 
    VALUES ('$bookingno', '$useremail', '$vid', '$rent_type','$fdate', '$tdate', '$status','$pick_up_loc','$drop_of_loc','$did','$dname',$amount ,$payment)";
$exsql=mysqli_query($conn,$sql);

    $update_status_query = "UPDATE driver SET status = 1  WHERE did = $did";
        mysqli_query($conn, $update_status_query);

        $updatecar="UPDATE  car_list SET  status=1 WHERE  vid=$vid";
mysqli_query($conn,$updatecar);


$payment_query="insert into payment (payment_id,booking_id,payment_type,amount,payment_status,payment_date,order_id) values ('$payment_id','$bookingno','$rent_type','$amount','$payment1','$payment_date','$order_id')";
$expayment_query-mysqli_query($conn,$payment_query);


}

echo "<script>alert('Booking  Successfully !');
window,location.href='profile.php';</script>";

?>
