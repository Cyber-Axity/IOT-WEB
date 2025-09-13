<?php
session_start();
require_once '../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHP_Mailer/phpmailer/src/Exception.php';
require '../PHP_Mailer/phpmailer/src/PHPMailer.php';
require '../PHP_Mailer/phpmailer/src/SMTP.php';

// =========================
// REGISTER PROCESS
// =========================
if (isset($_POST['register'])) {
    $user_name = $_POST['user_name'];
    $email     = $_POST['email'];
    $password  = $_POST['password'];
    $cpassword = $_POST['cpassword'];

    // Password validation
    if ($password !== $cpassword) {
        $_SESSION['password_error'] = "Passwords do not match.";
        include("register.php");
        exit();
    }

    if (strlen($password) < 7) {
        $_SESSION['password_error'] = "Password must be at least 7 characters long.";
        include("register.php");
        exit();
    }

    // Check email uniqueness
    $checkEmail = "SELECT email FROM acc WHERE email = '$email' ";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        $_SESSION['email_error'] = "Email already taken. Please use another email.";
        header("location: register.php");
        exit();
    }

    // Generate OTP and hash password
    $otp = rand(100000, 999999);
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $_SESSION['temp_registration'] = [
        'user_name' => $user_name,
        'email'     => $email,
        'password'  => $hashedPassword,
        'otp'       => $otp
    ];

    // Send email with PHPMailer
    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'asiatechssc@gmail.com';
        $mail->Password   = 'vlsmndgbzlvbjlpy';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        $mail->setFrom('asiatechssc@gmail.com', 'Asia Technological School of Science and Arts');
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = 'Your OTP Verification Code';
        $mail->Body    = "Hello $user_name,<br><br>Your OTP code is <strong>$otp</strong><br><br>Thank you.";

        $mail->send();

        $_SESSION['otp_sent'] = "We sent a verification code to your email.";
        header("Location: otp.php");
        exit();
    } catch (Exception $e) {
        $_SESSION['error'] = "Email sending failed. Please try again.";
        header("Location: register.php");
        exit();
    }
}

// =========================
// LOGIN PROCESS
// =========================
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $checkEmail = "SELECT * FROM acc WHERE email = '$email' ";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Login success
            $_SESSION['user_name'] = $row['user_name'];
            $_SESSION['email']     = $row['email'];

            // Ensure profile exists and fetch photo
            $email_safe = mysqli_real_escape_string($conn, $email);
            $sqlProfile = "SELECT photo FROM profile WHERE email='$email_safe' LIMIT 1";
            $resProfile = $conn->query($sqlProfile);

            if ($resProfile->num_rows > 0) {
                $profile = $resProfile->fetch_assoc();
                $_SESSION['user_photo'] = $profile['photo'];
            } else {
                // If profile does not exist, create one
                $user_name_safe = mysqli_real_escape_string($conn, $row['user_name']);
                $insertProfile = "INSERT INTO profile (email, user_name, student_id, photo) VALUES ('$email_safe', '$user_name_safe', '', NULL)";
                $conn->query($insertProfile);
                $_SESSION['user_photo'] = null; // default to null
            }

            header("location: ../dashboard.php");
            exit();
        } else {
            $_SESSION['error'] = "Invalid password.";
            header("location: login.php");
            exit();
        }

    } else {
        $_SESSION['error'] = "Account not found.";
        header("location: login.php");
        exit();
    }
}
