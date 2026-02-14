<?php
error_reporting(0);
@include "include/config.php";

function softDelete($conn, $table, $column, $id, $redirect) {
    $sql = "UPDATE $table SET deleted_at = NOW() WHERE $column = $id";
    $run = mysqli_query($conn, $sql);

    if ($run) {
        echo "<script>alert('Record deleted successfully');
        window.open('$redirect', '_self');</script>";
    } else {
        echo "<script>alert('Deletion failed');</script>";
    }
}

if (isset($_GET['vid'])) {
    softDelete($conn, "car_list", "vid", $_GET['vid'], "managecar.php");
}

if (isset($_GET['uid'])) {
    softDelete($conn, "reguser", "uid", $_GET['uid'], "reguser.php");
}

if (isset($_GET['bid'])) {
    softDelete($conn, "brands", "bid", $_GET['bid'], "managebrand.php");
}

if (isset($_GET['did'])) {
    softDelete($conn, "driver", "did", $_GET['did'], "managedriver.php");
}

if (isset($_GET['fid'])) {
    softDelete($conn, "feedback", "fid", $_GET['fid'], "manage_feedback.php");
}

if (isset($_GET['contactid'])) {
    softDelete($conn, "contactusquery", "contactid", $_GET['contactid'], "manage_contactus_query.php");
}

if (isset($_GET['delete'])) {
    softDelete($conn, "team_members", "id", $_GET['delete'], "manage_team.php");
}

if (isset($_GET['delete_id'])) {
    softDelete($conn, "coupons", "cpid", $_GET['delete_id'], "manage_coupon.php");
}
?>
