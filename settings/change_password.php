<?php
session_start();
require_once "../config.php"; // adjust if needed

// 🚨 Restrict if not logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$error   = null;
$success = null;

if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password     = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $email            = $_SESSION['email'];

    // Fetch current password hash from acc table
    $sql    = "SELECT password FROM acc WHERE email = '$email' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (!password_verify($current_password, $row['password'])) {
            $error = "Current password is incorrect.";
        } elseif ($new_password !== $confirm_password) {
            $error = "New passwords do not match.";
        } elseif (strlen($new_password) < 7) {
            $error = "Password must be at least 7 characters long.";
        } else {
            $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
            $update = "UPDATE acc SET password='$hashedPassword' WHERE email='$email'";

            if ($conn->query($update) === TRUE) {
                $success = "Password successfully updated!";
            } else {
                $error = "Something went wrong. Please try again.";
            }
        }
    } else {
        $error = "Account not found.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password</title>
  <link href="https://cdn.jsdelivr.net/npm/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
        min-height: 100vh;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: "Poppins", sans-serif;
        background: radial-gradient(circle at center, #ffffff 0%, #28a745 25%, #25963f 70%, #156d28 100%);
        padding: 15px;
    }

    .card-glass {
        max-width: 450px;
        width: 100%;
        border-radius: 20px;
        background: #ffffff;
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.2);
        padding: 30px 25px;
    }

    h3 {
        font-weight: 600;
    }

    .btn-success, .btn-outline-success {
        border-radius: 50px;
        transition: all 0.3s ease;
    }

    .btn-success:hover, .btn-outline-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 12px rgba(0,0,0,0.15);
    }

    .btn-outline-success:hover {
        background: #198754;
        color: white;
    }

    .password-wrapper {
        position: relative;
    }

    .toggle-eye {
        position: absolute;
        top: 75%;
        right: 10px;
        transform: translateY(-50%);
        cursor: pointer;
        font-size: 1.3rem;
        color: #6c757d;
    }

    .mascot {
        position: fixed;
        bottom: 10px;
        right: 22   %;
        width: 140px;
        height: auto;
        z-index: 1000;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-8px); }
        100% { transform: translateY(0px); }
    }

    /* ✅ Large screens (1025px–1440px) */
    @media (max-width: 1440px) {
        .mascot {
            width: 150px;
            right: 22%;
            bottom: 18px;
        }
    }

    /* ✅ Tablets / small laptops (769px–1024px) */
    @media (max-width: 1024px) {
        .mascot {
            width: 120px;
            right: 24%;
            bottom: 15px;
        }
    }

    /* ✅ Mobile landscape / large phones (481px–768px) */
    @media (max-width: 768px) {
        .mascot {
            width: 100px;
            right: 2%;
            bottom: 10px;
        }
    }

    /* ✅ Small phones (≤480px) */
    @media (max-width: 480px) {
        .mascot {
            width: 90px;
            right: 40%;
            top: 1%;
            transform: translateX(50%); /* centers horizontally */
        }
    }
  </style>
</head>
<body>

<div class="card-glass">
    <h3 class="text-success mb-4 text-center"><i class="bx bx-lock"></i> Change Password</h3>

    <!-- Error Alert -->
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger text-center" id="error-alert">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <!-- Success Alert -->
    <?php if (!empty($success)): ?>
      <div class="alert alert-success text-center" id="success-alert">
        <?= htmlspecialchars($success) ?>
      </div>
    <?php endif; ?>

    <form action="" method="POST">
        <!-- Current password with eye -->
        <div class="mb-3 password-wrapper">
          <label for="current_password" class="form-label">Current Password</label>
          <input type="password" class="form-control" name="current_password" id="current_password" required>
          <i class="bx bx-show toggle-eye" id="toggleCurrent"></i>
        </div>

        <!-- New password with shared eye -->
        <div class="mb-3 password-wrapper">
          <label for="new_password" class="form-label">New Password</label>
          <input type="password" class="form-control" name="new_password" id="new_password" required>
          <i class="bx bx-show toggle-eye" id="toggleNew"></i>
        </div>

        <!-- Confirm new password -->
        <div class="mb-3 password-wrapper">
          <label for="confirm_password" class="form-label">Confirm New Password</label>
          <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
        </div>

        <div class="d-grid gap-2">
          <button type="submit" class="btn btn-success" name="change_password">Update Password</button>
          <a href="../dashboard.php" class="btn btn-outline-success">Back to Dashboard</a>
        </div>
    </form>
</div>

<img src="../assests/img/Binitastic.png" alt="Mascot" class="mascot">

<script>
document.addEventListener("DOMContentLoaded", () => {
  // auto fade alerts
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

  // toggle for current password only
  const toggleCurrent = document.getElementById("toggleCurrent");
  const currentPass = document.getElementById("current_password");

  toggleCurrent.addEventListener("click", () => {
    const type = currentPass.getAttribute("type") === "password" ? "text" : "password";
    currentPass.setAttribute("type", type);
    toggleCurrent.classList.toggle("bx-show");
    toggleCurrent.classList.toggle("bx-hide");
  });

  // toggle for new + confirm together
  const toggleNew = document.getElementById("toggleNew");
  const newPass = document.getElementById("new_password");
  const confirmPass = document.getElementById("confirm_password");

  toggleNew.addEventListener("click", () => {
    const type = newPass.getAttribute("type") === "password" ? "text" : "password";
    newPass.setAttribute("type", type);
    confirmPass.setAttribute("type", type);
    toggleNew.classList.toggle("bx-show");
    toggleNew.classList.toggle("bx-hide");
  });
});
</script>
</body>
</html>
