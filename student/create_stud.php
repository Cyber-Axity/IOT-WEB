<?php
include("../config.php");

if (isset($_POST['save'])) {
    $student_id  = $_POST['student_id'];
    $first_name  = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name   = $_POST['last_name'];
    $course      = $_POST['course'];
    $year_level  = $_POST['year_level'];
    $card_no     = $_POST['card_no'];

    $sql = "INSERT INTO student_tbl (student_id, first_name, middle_name, last_name, course, year_level, card_no) 
            VALUES ('$student_id', '$first_name', '$middle_name', '$last_name', '$course', '$year_level', '$card_no')";

    if (mysqli_query($conn, $sql)) {
        header("Location: ../dashboard.php?page=students");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
} else {
    echo "Form not submitted correctly.";
}
?>
