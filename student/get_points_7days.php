<?php
include "../config.php";
header('Content-Type: application/json');

// Return totals for each of the last 7 days including today
$sql = "
  SELECT DATE(created_at) as day, SUM(points_added) as total
  FROM point_transactions
  WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
  GROUP BY DATE(created_at)
  ORDER BY day ASC
";

$result = $conn->query($sql);
$map = [];
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $map[$row['day']] = (float)$row['total'];
  }
}

// Build last 7 days labels and totals
$days = [];
$totals = [];
for ($i = 6; $i >= 0; $i--) {
  $d = date('Y-m-d', strtotime("-{$i} day"));
  $days[] = $d;
  $totals[] = isset($map[$d]) ? $map[$d] : 0.0;
}

echo json_encode([
  'status' => 'success',
  'days' => $days,
  'totals' => $totals,
  'today' => date('Y-m-d')
]);

$conn->close();
?>



