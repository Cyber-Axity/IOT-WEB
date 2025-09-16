<?php
include "../config.php";
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $input = json_decode(file_get_contents('php://input'), true);
    
    $student_id = isset($input['student_id']) ? intval($input['student_id']) : 0;
    $redeem_amount = isset($input['redeem_amount']) ? floatval($input['redeem_amount']) : 0;
    
    if ($student_id <= 0 || $redeem_amount <= 0) {
        echo json_encode([
            "status" => "error",
            "message" => "Invalid student ID or redeem amount"
        ]);
        exit;
    }
    
    // Get current points
    $sql = "SELECT points FROM student_tbl WHERE id = '$student_id'";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $current_points = $student['points'];
        
        if ($redeem_amount > $current_points) {
            echo json_encode([
                "status" => "error",
                "message" => "Cannot redeem more points than available"
            ]);
            exit;
        }
        
        // Update points
        $new_points = $current_points - $redeem_amount;
        $update = "UPDATE student_tbl SET points = '$new_points', updated_at = NOW() WHERE id = '$student_id'";
        
        if ($conn->query($update) === TRUE) {
            echo json_encode([
                "status" => "success",
                "message" => "Points redeemed successfully",
                "new_points" => $new_points
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Database error: " . $conn->error
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "Student not found"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error",
        "message" => "Invalid request method"
    ]);
}

$conn->close();
?>
