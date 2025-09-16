<?php
session_start();

// Clear form data and error messages when cancel is pressed
unset($_SESSION['form_data']);
unset($_SESSION['modal_error']);
unset($_SESSION['open_modal']);

// Redirect back to students page
header("Location: ../dashboard.php?page=students");
exit();
?>