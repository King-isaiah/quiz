<?php
session_start();
include_once "../../superbase/config.php";

// Set header to always return JSON
header('Content-Type: application/json');

$username = $_POST["username"] ?? '';
$password = $_POST["password"] ?? '';

// Check if fields are empty
if (empty($username) || empty($password)) {
    echo json_encode(array(
        "message" => "All input fields are required!",
        "success" => false
    ));
    exit();
}

try {
    // Fetch the specific user from Supabase
    $response = fetchData('admin_login?username=eq.' . urlencode($username));
    
    // Debug: Log the response to see what Supabase returns
    error_log("Supabase login response: " . print_r($response, true));

    // Check if response has error
    if (isset($response['error'])) {
        echo json_encode(array(
            "message" => "Database connection failed. Please try again.",
            "success" => false
        ));
        exit();
    }

    // Check if user exists and we got a valid array response
    if (is_array($response) && count($response) > 0) {
        $row = $response[0];
        
        // Debug: Log what we found
        error_log("User found - Username: " . $row['username'] . ", Password in DB: " . $row['password']);
        
        // Check if password field exists in the response
        if (!isset($row['password'])) {
            error_log("Password field missing in Supabase response");
            echo json_encode(array(
                "message" => "System configuration error",
                "success" => false
            ));
            exit();
        }

        // Verify the password - try both methods
        if (password_verify($password, $row['password'])) {
            // Passwords are hashed and match
            $_SESSION["admin"] = $username;
            error_log("Login successful - hashed password match");
            echo json_encode(array(
                "message" => "Successful Login",
                "success" => true
            ));
        } else if ($password === $row['password']) {
            // Passwords are plain text and match
            $_SESSION["admin"] = $username;
            error_log("Login successful - plain text password match");
            echo json_encode(array(
                "message" => "Successful Login",
                "success" => true
            ));
        } else {
            // Passwords don't match
            error_log("Password mismatch - Input: '$password', Stored: '" . $row['password'] . "'");
            echo json_encode(array(
                "message" => "Invalid Login Credentials",
                "success" => false
            ));
        }
    } else {
        // No user found or empty response
        error_log("No user found for username: '$username'. Response count: " . (is_array($response) ? count($response) : 'not array'));
        echo json_encode(array(
            "message" => "User doesn't Exist",
            "success" => false
        ));
    }
} catch (Exception $e) {
    error_log("Login error: " . $e->getMessage());
    echo json_encode(array(
        "message" => "System error occurred",
        "success" => false
    ));
}
?>