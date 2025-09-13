<?php
include ('../config.php');

$id = $_GET['id'];

$sql = "DELETE FROM student_tbl WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    header("Location: ../dashboard.php?page=students");
    exit();
} else {
    echo "Error: " . $conn->error;
}
?>
