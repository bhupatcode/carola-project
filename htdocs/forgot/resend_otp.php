<?php
session_start();
require 'vendor/autoload.php'; // Load PHPMailer

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

@include "include/config.php";

if (isset($_SESSION["email"])) {
    $email = $_SESSION["email"];
    $otp = rand(100000, 999999); // Generate new OTP

    $_SESSION["otp"] = $otp;
    $_SESSION["otp_expire"] = time() + 300; // Reset expiration (5 minutes)

    // PHPMailer Configuration
    $mail = new PHPMailer(true);
    try {
        // SMTP Configuration
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';  // SMTP server (Use your mail provider's SMTP)
        $mail->SMTPAuth   = true;
        $mail->Username   = 'carolarental3@gmail.com'; // Your email
        $mail->Password   = 'xiysjnbhlejdkyok'; // Your email app password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Email Content
        $mail->setFrom('your-email@gmail.com', 'Carola Support'); // Sender
        $mail->addAddress($email); // Receiver
        $mail->Subject = "Resend OTP for Password Reset";
        $mail->Body    = "Your new OTP is: $otp. It will expire in 5 minutes.";

        $mail->send();
        echo "<script>alert('New OTP sent to your email.'); window.location='verify_otp.php';</script>";
        $sql="update reguser set reset_otp=$otp where  email='$email'";
        $exsql=mysqli_query($conn,$sql);
    } catch (Exception $e) {
        echo "<script>alert('Failed to send OTP: " . $mail->ErrorInfo . "'); window.location='verify_otp.php';</script>";
    }
} else {
    echo "<script>alert('Session expired! Please request a new OTP.'); window.location='send_otp.php';</script>";
}
?>
