<?php
// edit_profile.php
session_start();
require_once '../config.php';

$success = ""; // success message

if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$email = $_SESSION['email'];
$email_safe = mysqli_real_escape_string($conn, $email);

// Fetch user profile
$sql = "SELECT * FROM profile WHERE email='$email_safe' LIMIT 1";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

// Create profile if it doesn't exist
if (!$user) {
    $user_name_safe = mysqli_real_escape_string($conn, $_SESSION['user_name']);
    $insert = "INSERT INTO profile (email, user_name, student_id) VALUES ('$email_safe', '$user_name_safe', '')";
    mysqli_query($conn, $insert);
    $result = mysqli_query($conn, $sql);
    $user = mysqli_fetch_assoc($result);
}

// Split fullname: Lastname, Firstname Middlename
$lastname = $firstname = $middlename = "";
if (!empty($user['fullname'])) {
    $parts = explode(",", $user['fullname']);
    if (count($parts) >= 2) {
        $lastname = trim($parts[0]);
        $names = explode(" ", trim($parts[1]));
        $firstname = $names[0];
        $middlename = isset($names[1]) ? $names[1] : "";
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
    $lastname   = mysqli_real_escape_string($conn, $_POST['lastname']);
    $firstname  = mysqli_real_escape_string($conn, $_POST['firstname']);
    $middlename = mysqli_real_escape_string($conn, $_POST['middlename']);
    $fullname   = $lastname . ", " . $firstname . " " . $middlename;
    $cellphone  = mysqli_real_escape_string($conn, $_POST['cellphone']);
    $department = mysqli_real_escape_string($conn, $_POST['department']);
    $position   = mysqli_real_escape_string($conn, $_POST['position']);

    $photo = $user['photo'];
    if (!empty($_FILES['photo']['name'])) {
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);

        $file_name = time() . "_" . basename($_FILES["photo"]["name"]);
        $target_file = $target_dir . $file_name;
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $photo = $file_name;
        }
    }

    $update = "
        UPDATE profile 
        SET student_id='$student_id', fullname='$fullname', cellphone='$cellphone',
            department='$department', position='$position', photo='$photo'
        WHERE email='$email_safe'
    ";

    if (mysqli_query($conn, $update)) {
        $_SESSION['user_photo'] = $photo;
        $success = "Profile successfully updated!";
        $result = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($result);
    } else {
        $success = "Error updating profile: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta charset="UTF-8">
<title>Edit Profile</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<style>
    body {
        background: radial-gradient(circle at center, #ffffff 0%, #28a745 30%, #25963fff 70%, #156d28ff 100% );
        min-height: 100vh;
        position: relative;
    }

    .card {
        border-radius: 20px;
        padding: 30px;
    }

    .btn-back {
        background-color: #ffffffff;
        color: black;
    }

    .btn-back:hover {
        background-color: #157347;
        color: white;
    }

    /* Mascot image default */
    .mascot {
        position: fixed;
        bottom: 40px;
        right: 20px;
        max-width:20%;
        width: 150px;
        height: auto;
        z-index: 1000;
        opacity: 0.9;
    }

    /* Medium devices (tablets, 768px and down) */
    @media (max-width: 992px) {
        .mascot {
            bottom: 20px;
            width: 120px;
            right: 5px;
        }
    }

    /* Small devices (phones) */
    @media (max-width: 576px) {
        .card {
            padding: 20px;
            margin: 10px;
        }

        .mascot {
            display: none; /* Hide mascot on mobile */
        }
    }


</style>

</head>
<body>

<!-- Back button at top-left -->
<a href="../dashboard.php" class="btn btn-back position-fixed m-3" style="top: 0; left: 0; z-index: 1050;">← Back</a>

<div class="container mt-5 mb-5">
    <div class="card shadow-lg">
        <h3 class="mb-4 text-center">Edit Profile</h3>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">

            <div class="mb-3">
                <label>Email (readonly)</label>
                <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label>Last Name</label>
                    <input type="text" name="lastname" class="form-control" value="<?= htmlspecialchars($lastname) ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>First Name</label>
                    <input type="text" name="firstname" class="form-control" value="<?= htmlspecialchars($firstname) ?>" required>
                </div>
                <div class="col-md-4 mb-3">
                    <label>Middle Name</label>
                    <input type="text" name="middlename" class="form-control" value="<?= htmlspecialchars($middlename) ?>">
                </div>
            </div>

            <div class="mb-3">
                <label>Student ID</label>
                <input type="text" name="student_id" class="form-control" value="<?= htmlspecialchars($user['student_id']) ?>" required>
            </div>

            <div class="mb-3">
                <label>Cellphone</label>
                <input type="text" name="cellphone" class="form-control" value="<?= htmlspecialchars($user['cellphone']) ?>">
            </div>

            <div class="mb-3">
                <label>Department</label>
                <select name="department" class="form-select">
                    <option value="">Select Department</option>
                    <option value="BSIT" <?= ($user['department']=="BSIT"?"selected":"") ?>>BSIT</option>
                    <option value="BSCS" <?= ($user['department']=="BSCS"?"selected":"") ?>>BSCS</option>
                    <option value="BSECE" <?= ($user['department']=="BSECE"?"selected":"") ?>>BSECE</option>
                    <option value="BSBA" <?= ($user['department']=="BSBA"?"selected":"") ?>>BSBA</option>
                    <option value="BSA" <?= ($user['department']=="BSA"?"selected":"") ?>>BSA</option>
                </select>
            </div>

            <div class="mb-3">
                <label>Position</label>
                <input type="text" name="position" class="form-control" value="<?= htmlspecialchars($user['position']) ?>">
            </div>

            <div class="mb-3">
            <label>Profile Photo</label><br>
            <?php if ($user['photo']): ?>
                <img src="../uploads/<?= htmlspecialchars($user['photo']) ?>" alt="Profile" class="rounded img-fluid mb-2" style="width:100px; height:auto;">
            <?php endif; ?>
            <input type="file" name="photo" class="form-control">
        </div>


            <div class="d-flex justify-content-center">
                <button type="submit" class="btn btn-success">Save Changes</button>
            </div>

        </form>
    </div>
</div>

<img src="../assests/img/Binitasticon.png" class="mascot img-fluid">

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
