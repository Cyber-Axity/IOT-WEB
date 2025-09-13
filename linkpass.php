<?php
session_start();
include ('../include/connect.php');
$page_title = "Reset Your Password";

if(!isset($_GET["code"])) {
    exit("Can't find your page");
}

$code = $_GET["code"];
$getEmailQuery = mysqli_query($conn, "SELECT email FROM reset WHERE code = '$code'");
if(mysqli_num_rows($getEmailQuery) == 0) {
    exit("Can't find your page");
}

if(isset($_POST["password"])) {
    $password = $_POST["password"];
    $cpass = $_POST["co-pass"];

    if ($password !== $cpass) {
        $_SESSION['password_error'] = "Passwords do not match!";
        header("Location: linkpass.php");
        exit();
    }

    $password = md5($password);
    $row = mysqli_fetch_array($getEmailQuery);
    $email = $row["email"];

    // Check if email exists in admin_form
    $adminCheck = mysqli_query($conn, "SELECT * FROM admin_form WHERE ad_email = '$email'");
    
    // Check if email exists in user_form
    $userCheck = mysqli_query($conn, "SELECT * FROM user_form WHERE email = '$email'");

    if (mysqli_num_rows($adminCheck) > 0) {
        $update = mysqli_query($conn, "UPDATE admin_form SET ad_password = '$password' WHERE ad_email = '$email'");
    } elseif (mysqli_num_rows($userCheck) > 0) {
        $update = mysqli_query($conn, "UPDATE user_form SET password = '$password' WHERE email = '$email'");
    } else {
        exit();
    }

    if ($update) {
        mysqli_query($conn, "DELETE FROM reset WHERE code='$code'");
        echo '<script> alert("Password Updated")</script>';
    } else {
        exit("Something went wrong :(");
    }
}
?>

<head>
    <link rel="stylesheet" href="../assests/css/bootstrap.min.css">
    <link rel="stylesheet" href="../assests/font/bootstrap-icons/font/bootstrap-icons.css">
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
                        <p class="success alert alert-success text-center px-0 py-0">
                            <?php echo htmlspecialchars($_SESSION['otp_sent']); ?></p>
                                <?php unset($_SESSION['otp_sent']); // Clear error after displaying ?>
                                    <?php } ?>

                    <?php if (isset($_SESSION['otp_error'])): ?>
                        <div class="error alert alert-danger text-center" role="alert">
                            <?= $_SESSION['otp_error']; unset($_SESSION['otp_error']); ?>
                                </div>
                                    <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control w-100 border-bottom border border-secondary" placeholder="New Password" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="co-pass" class="form-control w-100 border-bottom border border-secondary" placeholder="Confirm New Password" required>
                    </div>
                        <button type="submit" name="submit" class="btn btn-success w-100">Change Password</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="../assests/js/script.js"></script>
</body>