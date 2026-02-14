<?php
session_start();
// require 'vendor/autoload.php'; // If using Composer

@include "../include/config.php";
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $token = bin2hex(random_bytes(50)); // Generate a secure token

    // Check if email exists using prepared statement
    $stmt = $conn->prepare("SELECT aid FROM admin WHERE aemail = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Store token securely in database

        $stmt = $conn->prepare("UPDATE admin SET areset_token = ?, areset_expiry = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE aemail = ?");
        $stmt->bind_param("ss", $token, $email);
        $stmt->execute();
        // Send Email
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
            $mail->Subject = 'Password Reset Request';
            $mail->Body    = "
            Welcome To Carola. <br>
            This is is Link For Forgot Password Of your Account Of Carola.com. 
            <br>
            <br>
            If You Forgot PassWord Then Click 
             <a href='https://carolabm.infinityfreeapp.com/admin/forgot/areset_password.php?token=$token'>ResetPassword</a>  to reset your password.<br>

             <br>
            If you Cannot Send Request  Then Report To Carola.com. 
            <br>
            <br>
            <h2>Thank You</h2>
            ";

            $mail->send();
            echo "<script>alert('Admin password reset email sent!');window.location.href='../admin_login.php';</script>";
        } catch (Exception $e) {
            echo "Mailer Error: {$mail->ErrorInfo}";
        }
    } else {
        echo "<script>alert('No Admin found with this email!');window.open('aforgot_password.php','_self');</script>";
    }
}
