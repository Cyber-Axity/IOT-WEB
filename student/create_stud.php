<?php
session_start();
include("../config.php");

if (isset($_POST['save'])) {
    $student_id  = $_POST['student_id'];
    $first_name  = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name   = $_POST['last_name'];
    $course      = $_POST['course'];
    $year_level  = $_POST['year_level'];
    $card_no     = $_POST['card_no'];

    // ✅ Check if student_id already exists
    $checkStudentId = mysqli_query($conn, "SELECT * FROM student_tbl WHERE student_id = '$student_id'");
    
    if (mysqli_num_rows($checkStudentId) > 0) {
        $_SESSION['modal_error'] = "Student ID already exists. Please use a different one.";
        $_SESSION['open_modal'] = true; // flag to reopen modal
        $_SESSION['form_data'] = [
            'student_id' => $student_id,
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'course' => $course,
            'year_level' => $year_level,
            'card_no' => $card_no
        ];
        header("Location: ../dashboard.php?page=students");
        exit();
    }

    // ✅ Check if card_no already exists
    $checkCardNo = mysqli_query($conn, "SELECT * FROM student_tbl WHERE card_no = '$card_no'");
    
    if (mysqli_num_rows($checkCardNo) > 0) {
        $_SESSION['modal_error'] = "Card Number already exists. Please use a different one.";
        $_SESSION['open_modal'] = true; // flag to reopen modal
        $_SESSION['form_data'] = [
            'student_id' => $student_id,
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'course' => $course,
            'year_level' => $year_level,
            'card_no' => $card_no
        ];
        header("Location: ../dashboard.php?page=students");
        exit();
    }

    // ✅ Insert if no duplicate
    $sql = "INSERT INTO student_tbl 
                (student_id, first_name, middle_name, last_name, course, year_level, card_no) 
            VALUES 
                ('$student_id', '$first_name', '$middle_name', '$last_name', '$course', '$year_level', '$card_no')";

    if (mysqli_query($conn, $sql)) {
        $_SESSION['success_message'] = "Student added successfully!";
        unset($_SESSION['form_data']); // Clear form data on success
        header("Location: ../dashboard.php?page=students");
        exit();
    } else {
        $_SESSION['modal_error'] = "Error: " . mysqli_error($conn);
        $_SESSION['open_modal'] = true;
        $_SESSION['form_data'] = [
            'student_id' => $student_id,
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'course' => $course,
            'year_level' => $year_level,
            'card_no' => $card_no
        ];
        header("Location: ../dashboard.php?page=students");
        exit();
    }
} else {
    $_SESSION['modal_error'] = "Form not submitted correctly.";
    $_SESSION['open_modal'] = true;
    header("Location: ../dashboard.php?page=students");
    exit();
}
?>
