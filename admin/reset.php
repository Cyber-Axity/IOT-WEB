<?php
session_start();
require_once '../config.php';

// Handle form submit
if (isset($_POST['reset'])) {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm  = $_POST['confirm'];

    if ($password !== $confirm) {
        $_SESSION['error'] = "Passwords do not match.";
        header("Location: reset.php");
        exit();
    }

    // Check if email exists
    $checkEmail = "SELECT * FROM acc WHERE email='$email' LIMIT 1";
    $result = $conn->query($checkEmail);

    if ($result->num_rows > 0) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $conn->query("UPDATE acc SET password='$hashedPassword' WHERE email='$email'");
        $_SESSION['success_message'] = "Your password has been reset successfully. You can now log in.";
        header("Location: login.php");
        exit();
    } else {
        $_SESSION['error'] = "No account found with that email.";
        header("Location: reset.php");
        exit();
    }
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Reset Password</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
    body {
        background: radial-gradient(circle at center, #ffffff 0%, #28a745 25%, #25963f 70%, #156d28 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .reset-card {
        max-width: 450px;
        width: 100%;
        background: #ffffff;
        border-radius: 12px;
        padding: 30px;
        box-shadow: 0 8px 25px rgba(63, 63, 63, 0.5);
    }

    .input-group-text {
        cursor: pointer;
        background: #ffffff;
        border-left: none;
    }

    .form-control {
        border-right: none;
    }

    .toggle-password i {
        color: #28a745;
    }
    .mascot {
        position: fixed;
        bottom: 8%;
        right: 22%;
        max-width:20%;
        width: 140px;
        height: auto;
        z-index: 1000;
    }

    @media (max-width: 576px) {
        .reset-card {
            padding: 20px;
        }
    }

    /* Large screens (desktops) */
    @media (min-width: 1441px) {
        .mascot {
            width: 180px;
            bottom: 5%;
            right: 5%;
        }
    }

    /* Laptops / medium screens (1025px–1440px) */
    @media (max-width: 1440px) and (min-width: 1025px) {
        .mascot {
            width: 150px;
            bottom: 5%;
            right: 5%;
        }
    }

    /* Tablets / small laptops (769px–1024px) */
    @media (max-width: 1024px) and (min-width: 769px) {
        .mascot {
            width: 120px;
            bottom: 4%;
            right: 4%;
        }
    }

    /* Mobile landscape / large phones (481px–768px) */
    @media (max-width: 768px) and (min-width: 481px) {
        .mascot {
            width: 100px;
            bottom: 3%;
            right: 3%;
        }
    }

    /* Small phones (≤480px) */
    @media (max-width: 480px) {
        .mascot {
            width: 80px;
            bottom: 2%;
            right: 50%;
            transform: translateX(50%);
        }
    }
</style>
</head>
<body>

<div class="reset-card">
    <h3 class="text-center text-success fw-bold">Reset Password</h3>

    <?php if (!empty($error)): ?>
        <div class="alert alert-danger text-center"><?= $error; ?></div>
    <?php endif; ?>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success text-center"><?= $success; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="Enter your email" required>
        </div>

        <div class="mb-3">
            <label class="form-label">New Password</label>
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="New password" required minlength="8">
                <span class="input-group-text toggle-password" onclick="togglePassword()">
                    <i class="fa-solid fa-eye" id="eye-icon"></i>
                </span>
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Confirm Password</label>
            <input type="password" name="confirm" id="confirm" class="form-control" placeholder="Confirm password" readonly required minlength="8">
        </div>

        <button type="submit" name="reset" class="btn btn-success w-100 mb-3">Reset Password</button>
    </form>
</div>

<img src="../assests/img/Binitastico.png" class="image-fluid mascot">

<script>
// Toggle visibility for both fields
function togglePassword() {
    const password = document.getElementById('password');
    const confirm  = document.getElementById('confirm');
    const eyeIcon  = document.getElementById('eye-icon');

    const type = password.type === 'password' ? 'text' : 'password';
    password.type = type;
    confirm.type = type;

    eyeIcon.classList.toggle('fa-eye');
    eyeIcon.classList.toggle('fa-eye-slash');
}

// Mirror new password into confirm password
const passwordInput = document.getElementById('password');
const confirmInput  = document.getElementById('confirm');

passwordInput.addEventListener('input', () => {
    confirmInput.value = passwordInput.value;
});
</script>

</body>
</html>
