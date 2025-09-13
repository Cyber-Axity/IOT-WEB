<?php
session_start();
include('../config.php');

if (isset($_POST['verify'])) {
    $entered_otp = implode("", $_POST['otp']); // join 6 boxes

    if (!isset($_SESSION['temp_registration'])) {
        $_SESSION['otp_error'] = "Session expired. Please register again.";
        header("Location: register.php");
        exit();
    }

    $user_name = $_SESSION['temp_registration']['user_name'];
    $email = $_SESSION['temp_registration']['email'];
    $password = $_SESSION['temp_registration']['password'];
    $stored_otp = $_SESSION['temp_registration']['otp'];

    if ($entered_otp == $stored_otp) {
        $insertUser = "INSERT INTO acc (user_name, email, password, otp) 
                       VALUES ('$user_name', '$email', '$password', $stored_otp)";
        if ($conn->query($insertUser) === TRUE) {
            unset($_SESSION['temp_registration']);
            $_SESSION['success_message'] = "OTP verified successfully! Please log in.";
            header("Location: login.php");
            exit();
        } else {
            $_SESSION['otp_error'] = "Database error: " . $conn->error;
            header("Location: otp.php");
            exit();
        }
    } else {
        $_SESSION['otp_error'] = "Invalid OTP. Please try again.";
        header("Location: otp.php");
        exit();
    }
}

// collect error if exists
$otp_error = $_SESSION['otp_error'] ?? null;
$otp_sent = $_SESSION['otp_sent'] ?? null;
unset($_SESSION['otp_error'], $_SESSION['otp_sent']);
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
    <title>OTP</title>
    <style>
        .otp-container {
            display: flex;
            justify-content: center;
            flex-wrap: wrap; /* allows wrapping on small screens */
            gap: 16px; /* spacing between boxes */
            margin-bottom: 30px;
        }

        .otp-input {
            width: 45px;
            height: 50px;
            font-size: 22px;
            text-align: center;
            border: 2px solid #28a745;
            border-radius: 8px;
            outline: none;
            transition: 0.2s;
        }

        /* Highlight on focus */
        .otp-input:focus {
            border-color: #218838;
            box-shadow: 0 0 5px rgba(40, 167, 69, 0.5);
        }


        @media only screen and (max-width: 768px){
             .box-area{
                margin: 0 auto;
                max-width: calc(100% - 20px);

            }
            .left-box{
                height: 100px;
                overflow: hidden;
                display: flex;
                justify-content: center;
                align-items: center;
                position: relative;
                padding-top: 120px;
            }
            .right-box{
                padding: 20px;
            }
            .left-box img {
                width: 100px;           /* shrink image */
            }
            .circle1 { width: 100px; height: 100px; top: -50px; left: -40px; }
            .circle2 { width: 100px; height: 100px; bottom: -50px; right: -40px; }
            .circle3 { width: 0px; height: 0px; top: 50%; left: 70%; transform: translate(-50%, -50%); }
            .circle4 { width: 0px; height: 0px;}

            .otp-input {
                width: 35px;
                height: 37px;
                font-size: 18px;
            }
            .otp-container {
                gap: 15px;
            }

            .row {
                position: relative;
                top: -50px;
            }
        }
    </style>
</head>
<body>

    <!----------------------- Main Container -------------------------->
     <div class="container d-flex justify-content-center align-items-center min-vh-100">

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
           <p class="text-white fs-1 wel">Here We Go!</p>
           <small class="text-white text-wrap text-center" style="width: 17rem;">This is for Security Purposes</small>
       </div> 

    <!--------------------------- Right Box ---------------------------->
       <div class="col-md-6 right-box">
          <div class="row align-items-center" style="margin-top: 100px;">

            <!-- Display Error Message -->
            <?php if (!empty($otp_error)): ?>
                <div class="alert alert-danger text-center" id="error-alert">
                    <?= $otp_error; ?>
                </div>
            <?php endif; ?>

            <div class="header-text mb-4">
                <h2 class="title text-success">OTP</h2>
            </div>

                <!-- OTP Section -->
                <form method="POST" action="otp.php">
                    <div class="otp-container">
                        <input type="text" maxlength="1" class="otp-input" name="otp[]">
                        <input type="text" maxlength="1" class="otp-input" name="otp[]">
                        <input type="text" maxlength="1" class="otp-input" name="otp[]">
                        <input type="text" maxlength="1" class="otp-input" name="otp[]">
                        <input type="text" maxlength="1" class="otp-input" name="otp[]">
                        <input type="text" maxlength="1" class="otp-input" name="otp[]">
                    </div>
                    <div class="input-group mb-3">
                        <button class="btn btn-lg btn-success w-100 fs-6" name="verify">Verify</button>
                    </div>
                </form>
                
          </div>
       </div> 
      </div>
    </div>

    <script>
        const inputs = document.querySelectorAll(".otp-input");
        inputs.forEach((input, index) => {
            input.addEventListener("input", () => {
                if (input.value.length === 1 && index < inputs.length - 1) {
                    inputs[index + 1].focus();
                }
            });
            input.addEventListener("keydown", (e) => {
                if (e.key === "Backspace" && !input.value && index > 0) {
                    inputs[index - 1].focus();
                }
            });
        });

        <?php if (!empty($otp_sent)): ?>
            alert("<?= $otp_sent ?>");
        <?php endif; ?>
    </script>
</body>
</html>
