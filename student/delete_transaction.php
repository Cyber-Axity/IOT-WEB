<?php
include "../config.php";

// Simple delete endpoint for point_transactions
// Usage: student/delete_transaction.php?id=123

if (!isset($_GET['id'])) {
  header('Location: ../dashboard.php?page=history');
  exit();
}

$id = (int)$_GET['id'];
if ($id <= 0) {
  header('Location: ../dashboard.php?page=history');
  exit();
}

$stmt = $conn->prepare("DELETE FROM point_transactions WHERE id = ?");
if ($stmt) {
  $stmt->bind_param('i', $id);
  $stmt->execute();
  $stmt->close();
}

$conn->close();
header('Location: ../dashboard.php?page=history');
exit();
?>


