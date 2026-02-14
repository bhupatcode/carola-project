<?php
// error_reporting(0);
$host="sql107.infinityfree.com";
$username="if0_38428644";
$db="if0_38428644_car_rent";
$pass='r3Rg1hTA3a';

// for fun
// $host="ql.freedb.tech";
// $username="freedb_Bhupat";
// $db="freedb_car_rent";
// $pass='hcXB%$e?A538wtY';
$conn = mysqli_connect("$host", "$username", "$pass", "$db");
if (!$conn) {
    echo "not";
}


?>
