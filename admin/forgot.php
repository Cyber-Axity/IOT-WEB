<?php
session_start();
require_once '../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHP_Mailer/phpmailer/src/Exception.php';
require '../PHP_Mailer/phpmailer/src/PHPMailer.php';
require '../PHP_Mailer/phpmailer/src/SMTP.php';

// Handle Forgot Password Request
if (isset($_POST['forgot'])) {
    $email = trim($_POST['email']);

    // Check if email exists
    $checkEmail = "SELECT * FROM acc WHERE email = '$email' LIMIT 1";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        // Send email
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
            $mail->Subject = 'Password Reset Request';

            // Direct link to reset.php without token
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
            $resetLink = $protocol . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/reset.php";

            $mail->Body = "
                Hello,<br><br>
                We received a request to reset your password.<br>
                Click the link below to reset it:<br><br>
                <a href='$resetLink'>$resetLink</a><br><br>
                If you didn’t request this, just ignore this email.
            ";

            $mail->send();
            $_SESSION['success_message'] = "A password reset link has been sent to your email.";
        } catch (Exception $e) {
            $_SESSION['error'] = "Failed to send reset email. Please try again.";
        }
    } else {
        $_SESSION['error'] = "No account found with that email.";
    }

    header("Location: forgot.php");
    exit();
}

// collect alerts
$error   = $_SESSION['error'] ?? null;
$success = $_SESSION['success_message'] ?? null;
unset($_SESSION['error'], $_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="design.css">
    <title>Forgot Password</title>
    <style>
        .alert-container {
            min-height: 60px; /* reserve space for 1 alert */
            transition: min-height 0.3s ease;
        }
        .header-text {
            margin-top: 20px; /* lower it slightly */
        }
    </style>
</head>
<body>

<div class="container d-flex justify-content-center align-items-start min-vh-100" style="padding-top: 50px;">

    <div class="row border rounded-5 p-3 bg-light box-area">

        <!-- Left Box -->
        <div class="col-md-6 rounded-3 d-flex justify-content-center align-items-center flex-column left-box" style="background: radial-gradient(circle at center, #28a745, #0c6139);">
            <div class="featured-image mb-3">
                <div class="circle circle1"></div>
                <div class="circle circle2"></div>
                <div class="circle circle3"></div>
                <div class="circle circle4"></div>
                <img src="../assests/img/Binitastico.png" class="img-fluid" style="width: 250px;">
            </div>
            <p class="text-white fs-1 wel">Welcome!</p>
            <small class="text-white text-wrap text-center" style="width: 17rem;">Turn your Bottles into Rewards.</small>
        </div>

        <!-- Right Box -->
        <div class="col-md-6 right-box">
            <div class="row align-items-center">

                <!-- Alerts -->
                <div class="alert-container" style="min-height: 60px;"> <!-- adjust height as needed -->
                    <?php if (!empty($error)): ?>
                    <div class="alert alert-danger text-center"><?= $error; ?></div>
                    <?php endif; ?>
                    <?php if (!empty($success)): ?>
                    <div class="alert alert-success text-center"><?= $success; ?></div>
                    <?php endif; ?>
                </div>

                <div class="header-text mb-3">
                    <h2 class="title text-success">Forgot Password</h2>
                    <p class="text-secondary" style="font-size: 14px;">Enter your email and we’ll send you reset instructions</p>
                </div>

                <form method="POST">
                    <div class="input-group mb-3">
                        <i class="bx bx-envelope text-secondary icon"></i>
                        <input type="email" class="form-control form-control-lg bg-light fs-6 rounded-3" placeholder="Email address" name="email" required>
                    </div>

                    <div class="input-group mb-3">
                        <button class="btn btn-lg btn-success w-100 fs-6" name="forgot">Send Reset Link</button>
                    </div>

                    <div class="row">
                        <small class="text-secondary text-center">Remembered your password? <a href="login.php" class="text-success">Back to Login</a></small>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const alerts = document.querySelectorAll(".alert");

    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = "opacity 0.5s ease, transform 0.5s ease";
            alert.style.opacity = "0";
            alert.style.transform = "translateY(-20px)";
            setTimeout(() => alert.remove(), 500);
        }, 3000);
    });
});
</script>

</body>
</html>
