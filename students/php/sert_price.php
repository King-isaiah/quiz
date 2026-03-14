<?php
session_start(); // Start the session

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['price'])) {
        $_SESSION['price'] = $_POST['price']; // Set the session variable
        echo "Session category set to: " . $_SESSION['price']; // Optional: Respond back
    } else {
        echo "No price provided."; // Handle the error case
    }
} else {
    echo "Invalid request method."; // Not a POST request
}
?>