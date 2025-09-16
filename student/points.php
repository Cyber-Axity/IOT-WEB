<?php
include "../config.php"; // DB connection

// Ensure transactions table supports balance_after so history shows correct past balances
$conn->query("CREATE TABLE IF NOT EXISTS point_transactions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT NOT NULL,
  points_added DECIMAL(10,2) NOT NULL DEFAULT 0,
  source VARCHAR(50) DEFAULT 'RFID',
  balance_after DECIMAL(10,2) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_created_at (created_at)
) ENGINE=InnoDB");
// Best-effort add of balance_after if table existed
$conn->query("ALTER TABLE point_transactions ADD COLUMN IF NOT EXISTS balance_after DECIMAL(10,2) NULL");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Read UID and points sent by ESP32
    $uid = isset($_POST['UID']) ? $_POST['UID'] : '';
    $points = isset($_POST['points']) ? floatval($_POST['points']) : 0;

    if (empty($uid)) {
        echo json_encode([
            "status" => "error",
            "message" => "UID not provided"
        ]);
        exit;
    }

    // Check if student exists
    $sql = "SELECT * FROM student_tbl WHERE card_no = '$uid' LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $newPoints = $student['points'] + $points;

        $update = "UPDATE student_tbl SET points = '$newPoints', last_activity = NOW() WHERE card_no = '$uid'";
        $conn->query($update);

        // Log transaction with balance after the earn
        if (isset($student['id']) && $student['id'] && $points > 0) {
            $studId = (int)$student['id'];
            $conn->query("INSERT INTO point_transactions (student_id, points_added, source, balance_after) VALUES ($studId, " . floatval($points) . ", 'RFID', " . floatval($newPoints) . ")");
        }

        echo json_encode([
            "status" => "success",
            "message" => "Points updated",
            "student_id" => $student['student_id'],
            "name" => $student['first_name'] . " " . $student['last_name'],
            "points" => $newPoints
        ]);
    } else {
        // Auto-register student with placeholder name
        $firstName = "New";  
        $lastName = "Student";

        $insert = "INSERT INTO student_tbl (card_no, first_name, last_name, points, last_activity) 
                   VALUES ('$uid', '$firstName', '$lastName', '$points', NOW())";
        if ($conn->query($insert) === TRUE) {
            // Log transaction for new student with starting balance
            $newStudentId = $conn->insert_id;
            if ($points > 0) {
                $conn->query("INSERT INTO point_transactions (student_id, points_added, source, balance_after) VALUES ($newStudentId, " . floatval($points) . ", 'RFID', " . floatval($points) . ")");
            }
            echo json_encode([
                "status" => "success",
                "message" => "New student registered and points added",
                "student_id" => $conn->insert_id,
                "name" => "$firstName $lastName",
                "points" => $points
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "DB error: " . $conn->error
            ]);
        }
    }
}
?>
