<?php
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['category'])) {
        $_SESSION['category'] = $_POST['category']; // Set the session variable
        echo "Session category set to: " . $_SESSION['category']; // Optional: Respond back
    } else {
        echo "No category provided."; // Handle the error case
    }
} else {
    echo "Invalid request method."; // Not a POST request
}
?>