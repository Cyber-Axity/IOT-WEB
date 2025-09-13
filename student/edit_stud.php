<?php
// student/edit_stud.php
include("../config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit'])) {
    $id          = $_POST['id'];         // Hidden ID field
    $student_id  = $_POST['student_id'];
    $first_name  = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name   = $_POST['last_name'];
    $course      = $_POST['course'];
    $year_level  = $_POST['year_level'];
    $card_no     = $_POST['card_no'];

    // Escape input to prevent basic SQL injection
    $student_id  = mysqli_real_escape_string($conn, $student_id);
    $first_name  = mysqli_real_escape_string($conn, $first_name);
    $middle_name = mysqli_real_escape_string($conn, $middle_name);
    $last_name   = mysqli_real_escape_string($conn, $last_name);
    $course      = mysqli_real_escape_string($conn, $course);
    $year_level  = mysqli_real_escape_string($conn, $year_level);
    $card_no     = mysqli_real_escape_string($conn, $card_no);

    // Build the query
    $sql = "UPDATE student_tbl 
            SET student_id='$student_id', first_name='$first_name', middle_name='$middle_name', 
                last_name='$last_name', course='$course', year_level='$year_level', card_no='$card_no'
            WHERE id='$id'";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../dashboard.php?page=students&success=Student updated successfully");
        exit();
    } else {
        header("Location: ../dashboard.php?page=students&error=Error updating student");
        exit();
    }
} else {
    header("Location: ../dashboard.php?page=students");
    exit();
}
?>
