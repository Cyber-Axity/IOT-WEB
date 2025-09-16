<?php
session_start();

// if (isset($_SESSION['email'])) {
//     header("Location: dashboard.php");
//     exit();
// }

// collect error if exists
$error = $_SESSION['error'] ?? null;
$success = $_SESSION['success_message'] ?? null;

// clear it so it won’t persist on refresh
unset($_SESSION['error']);
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
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <title>Login</title>
</head>
<body>

    <!----------------------- Main Container -------------------------->

     <div class="container d-flex justify-content-center align-items-center min-vh-100">

    <!----------------------- Login Container -------------------------->

       <div class="row border rounded-5 p-3 bg-light box-area">

    <!--------------------------- Left Box ----------------------------->

       <div class="col-md-6 rounded-3 d-flex justify-content-center bg-success align-items-center flex-column left-box" style="background: radial-gradient(circle at center, #28a745, #0c6139);">
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

    <!-------------------- ------ Right Box ---------------------------->
        
       <div class="col-md-6 right-box">
          <div class="row align-items-center">

                <!-- Display Error Message -->
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger text-center" id="error-alert">
                    <?= $error; ?>
                </div>
                <?php endif; ?>
                <!-- Display Success Message -->
                <?php if (!empty($success)): ?>
                <div class="alert alert-success text-center" id="success-alert">
                    <?= $success; ?>
                </div>
                <?php endif; ?>

            <div class="header-text mb-4">
                    <h2 class="title text-success">LOGIN</h2>
            </div>

                <form action="index.php" method="POST">
                <div class="input-group mb-3">
                    <i class="bx bx-envelope text-secondary icon"></i>
                    <input type="email" class="form-control form-control-lg bg-light fs-6 rounded-3" placeholder="Email address" name="email" required>
                </div>
                <div class="input-group mb-1">
                    <i class="bx bx-lock-alt text-secondary icon"></i>
                    <input type="password" class="form-control form-control-lg bg-light fs-6 rounded-3" id="password" placeholder="Password" name="password" required>
                    <i class="bx bx-show text-secondary eye"></i>
                </div>
                <div class="input-group mb-0 d-flex justify-content-between">
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="formCheck">
                        <label for="formCheck" class="form-check-label text-secondary"><small>Remember Me</small></label>
                    </div>
                    <div class="forgot">
                        <small><a class="text-success" href="forgot.php">Forgot Password?</a></small>
                    </div>
                </div>
                <div class="mb-0">
                    <div class="recaptcha-wrapper">
                        <div class="g-recaptcha" data-sitekey="6LeAPskrAAAAAJd82e_gGLabQ6KAFOt1Jr2aQkfm" data-callback="enableSubmitButton"></div>
                    </div>
                </div>
                <div class="input-group mb-3">
                    <button class="btn btn-lg btn-success w-100 fs-6" id="submitBtn" name="login" disabled="disabled">Login</button>
                </div>
                <div class="input-group mb-3">
                    <button class="btn btn-lg btn-light w-100 fs-6"><img src="../assests/img/google.png" style="width:20px" class="me-2"><small>Sign In with Google</small></button>
                </div>
                <div class="row">
                    <small class="acc">Don't have account yet? <a class="text-success" href="register.php">Sign Up</a></small>
                </div>
                </form>
          </div>
       </div> 

      </div>
    </div>
    <script src="UI.js"></script>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const errorAlert = document.getElementById("error-alert");
        const successAlert = document.getElementById("success-alert");

        [errorAlert, successAlert].forEach(alert => {
            if (alert) {
                setTimeout(() => {
                    alert.style.transition = "opacity 0.5s ease";
                    alert.style.opacity = "0";
                    setTimeout(() => alert.remove(), 500);
                }, 2000);
            }
        });
    });

    function enableSubmitButton() {
        document.getElementById("submitBtn").disabled = false;
    }
    </script>

</body>
</html>