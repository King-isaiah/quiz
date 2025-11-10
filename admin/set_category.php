<?php
session_start();

if (isset($_POST['examSubject'])) {
    $_SESSION['exam_subject'] = $_POST['examSubject']; // Set the session variable
    echo json_encode(["status" => "success", "message" => "Session variable set: " . $_SESSION['exam_subject']]);
} else {
    error_log("No category selected. POST data: " . print_r($_POST, true)); // Log POST data for debugging
    echo json_encode(["status" => "error", "message" => "No category selected."]);
}
?>