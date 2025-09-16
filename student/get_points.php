<?php
include "../config.php";
header('Content-Type: application/json');

$ids = isset($_GET['ids']) ? $_GET['ids'] : '';
$pointsData = [];

if (!empty($ids)) {
    $idArray = array_map('intval', explode(',', $ids));
    $idList = implode(',', $idArray);

    if (!empty($idList)) {
        $sql = "SELECT id, points, last_activity FROM student_tbl WHERE id IN ($idList)";
        $result = $conn->query($sql);

        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $pointsData[$row['id']] = [
                    'points' => (float)$row['points'],
                    'last_activity' => $row['last_activity']
                ];
            }
            echo json_encode(["status" => "success", "data" => $pointsData]);
        } else {
            echo json_encode(["status" => "error", "message" => $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid IDs provided"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "No IDs provided"]);
}

$conn->close();
?>
