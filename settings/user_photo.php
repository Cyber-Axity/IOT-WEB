<?php
session_start();
require_once '../config.php';

if(!isset($_SESSION['email'])){
    echo json_encode(['success'=>false]);
    exit;
}

$email = $_SESSION['email'];

// Fetch user profile
$sql = "SELECT photo, user_name FROM profile WHERE email='$email' LIMIT 1";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

if($user){
    $photo_url = $user['photo'] ? '../uploads/' . $user['photo'] : null;
    $initial   = strtoupper(substr($_SESSION['user_name'],0,1));
    echo json_encode([
        'success' => true,
        'photo'   => $photo_url,
        'initial' => $initial,
        'username'=> $_SESSION['user_name']
    ]);
} else {
    echo json_encode(['success'=>false]);
}
