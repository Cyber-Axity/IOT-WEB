<?php
include "../config.php";
header('Content-Type: application/json');

// Ensure transactions table exists (shared with earning flow)
$conn->query("CREATE TABLE IF NOT EXISTS point_transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  points_added DECIMAL(10,2) NOT NULL DEFAULT 0,
  source VARCHAR(50) DEFAULT 'RFID',
  balance_after DECIMAL(10,2) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB");
// Add balance_after if table already existed (best-effort)
$conn->query("ALTER TABLE point_transactions ADD COLUMN IF NOT EXISTS balance_after DECIMAL(10,2) NULL");

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
            // Log redemption as negative points in transactions with balance after
            $neg = -1 * floatval($redeem_amount);
            $conn->query("INSERT INTO point_transactions (student_id, points_added, source, balance_after) VALUES ($student_id, $neg, 'REDEEM', " . floatval($new_points) . ")");
            echo json_encode([
                "status" => "success",
                "message" => "Points redeemed successfully",
                "new_points" => (float)$new_points,
                // Simple message field a device could read if it calls this endpoint
                "rfid_message" => "Redeemed " . number_format($redeem_amount, 2) . " points",
                "redeemed" => (float)$redeem_amount,
                "timestamp" => date('Y-m-d H:i:s')
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
