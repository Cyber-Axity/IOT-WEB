<?php
session_start();
$page_title = "Send Email";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../PHP_Mailer/phpmailer/src/Exception.php';
require '../PHP_Mailer/phpmailer/src/PHPMailer.php';
require '../PHP_Mailer/phpmailer/src/SMTP.php';
include ('../include/connect.php');

if(isset($_POST["r-email"])) {

    $emailTo = $_POST["r-email"];

    $code = uniqid(true);
    $query = mysqli_query($conn, "INSERT INTO reset(code, email) VALUES('$code', '$emailTo')");
    if(!$query){
        exit("Error");
    }

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'specialization10@gmail.com';
        $mail->Password = 'hfneaurbrxiiupqo';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;

        $mail->setFrom('specialization10@gmail.com', 'Asia Technological School of Science and Arts');
        $mail->addAddress($emailTo);

        $url = "http://" . $_SERVER["HTTP_HOST"] . dirname($_SERVER["PHP_SELF"]) . "/linkpass.php?code=$code";
        $mail->isHTML(true);
        $mail->Subject = 'Your password reset link';
        $mail->Body = "<h1>You requested a password reset</h1><br>
                        Click <a href ='$url'>this link</a> to do so.";

        $mail->send();

        echo '<script> alert("Reset Password has been sent to your email")</script>';

    } catch (Exception $e) {
        $_SESSION['error'] = "Email sending failed. Please try again.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assests/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assests/font/bootstrap-icons/font/bootstrap-icons.css">
    <title>Document</title>
</head>
<body style="background-image: url(../assests/img/bg2.jpg);">
<div class="container d-flex justify-content-center align-items-center min-vh-100">
    <!-- Login Container -->
    <div class="row border-round p-3 bg-white box-area log_form mx-1" style="box-shadow: 0 0 80px 10px rgba(145, 255, 0, 0.7); border-radius: 10px; border: 3px solid gray">
        <!-- Right Box -->
        <div class="col-12 right-box">
            <div class="row align-items-center">

                <div class="header-text mb-3">
                    <h3 class="text-center pt-4 fs-1" style="font-weight: 500;">Forgot Password <!--<span class="bi bi-shield-fill pe-0 align-items-left" style="font-size: 40px;"></span>--></h3>
                    <p class="fs-5 text-center">Enter your email address</p>
                </div>

                    <?php if(isset($_SESSION['otp_sent'])) { ?>
                        <p class="success alert alert-success text-center px-0 py-0"><?php echo htmlspecialchars($_SESSION['otp_sent']); ?></p>
                    <?php unset($_SESSION['otp_sent']); // Clear error after displaying ?>
                    <?php } ?>

                    <?php if (isset($_SESSION['otp_error'])): ?>
                        <div class="error alert alert-danger text-center" role="alert">
                            <?= $_SESSION['otp_error']; unset($_SESSION['otp_error']); ?>
                        </div>
                    <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <input type="email" name="r-email" class="form-control w-100 border-bottom border border-secondary" placeholder="Enter Email" required>
                    </div>
                        <button type="submit" name="send_email" class="btn btn-success w-100">Send</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../assests/js/script.js"></script>
</body>
</html>

