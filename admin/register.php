<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];

// Collect all errors
if (isset($_SESSION['password_error'])) {
    $errors[] = $_SESSION['password_error'];
}
if (isset($_SESSION['email_error'])) {
    $errors[] = $_SESSION['email_error'];
}
if (isset($_SESSION['error'])) {
    $errors[] = $_SESSION['error'];
}

// Clear session errors after storing them
unset($_SESSION['password_error'], $_SESSION['email_error'], $_SESSION['error']);
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    <link rel="stylesheet" href="design.css">
    <title>Register</title>
</head>
<body>

    <!----------------------- Main Container -------------------------->

     <div class="container d-flex justify-content-center align-items-center min-vh-100">

    <!----------------------- Register Container -------------------------->

       <div class="row border rounded-5 p-3 bg-light box-area">

    <!--------------------------- Left Box ----------------------------->

       <div class="col-md-6 rounded-3 d-flex justify-content-center bg-success align-items-center flex-column left-box" style="background: radial-gradient(circle at center, #28a745, #0c6139);">
           <div class="featured-image mb-3">
            <div class="circle circle1"></div>
            <div class="circle circle2"></div>
            <div class="circle circle3"></div>
            <div class="circle circle4"></div>
            <img src="../assests/img/Binitastic.png" class="img-fluid" style="width: 250px;">
           </div>
           <p class="text-white fs-1 wel">Hello There!</p>
           <small class="text-white text-wrap text-center" style="width: 17rem;">Be part of Us</small>
       </div> 

    <!-------------------- ------ Right Box ---------------------------->
        
       <div class="col-md-6 right-box">
          <div class="row align-items-center">

            <!-- ERROR MESSAGES -->
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger text-center" id="error-alert">
                <?php foreach ($errors as $err): ?>
                    <div><?= $err; ?></div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <div class="header-text mb-4">
                <h2 class="title text-success">REGISTER</h2>
            </div>             

                <form action="index.php" method="POST">
                    <div class="input-group mb-3">
                        <i class="bx bx-user text-secondary icon"></i>
                        <input type="text" class="form-control form-control-lg bg-light fs-6 rounded-3" placeholder="Username" name="user_name" value="<?= isset($_POST['user_name']) ? htmlspecialchars($_POST['user_name']) : '' ?>" 
                            required>
                    </div>

                    <div class="input-group mb-3">
                        <i class="bx bx-envelope text-secondary icon"></i>
                        <input type="email" class="form-control form-control-lg bg-light fs-6 rounded-3" placeholder="Email address" name="email" value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>" 
                            required>
                    </div>
                    <div class="input-group mb-3">
                        <i class="bx bx-lock-alt text-secondary icon"></i>
                        <input type="password" class="form-control form-control-lg bg-light fs-6 rounded-3" id="password" placeholder="Password" name="password" required>
                        <i class="bx bx-show text-secondary eye"></i>
                    </div>
                    <div class="input-group mb-3">
                        <i class="bx bx-lock-alt text-secondary icon"></i>
                        <input type="password" class="form-control form-control-lg bg-light fs-6 rounded-3" id="c-password" placeholder="Confirm password" name="cpassword" required>
                        <i class="bx bx-show text-secondary eye"></i>
                    </div>
                    <div class="input-group mb-3">
                        <button class="btn btn-lg btn-success w-100 fs-6" name="register">Register</button>
                    </div>
                    <div class="row">
                        <small class="acc">Already have an account? <a class="text-success" href="login.php">Sign In</a></small>
                    </div>
                </form>
          </div>
       </div> 

      </div>
    </div>
    <script src="UI.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
        const alertBox = document.getElementById("error-alert");
        if (alertBox) {
            setTimeout(() => {
            alertBox.style.transition = "opacity 0.5s ease";
            alertBox.style.opacity = "0";

            setTimeout(() => {
                alertBox.remove();
            }, 500);
            }, 3000); // 3 seconds
        }
        });
    </script>
</body>
</html>