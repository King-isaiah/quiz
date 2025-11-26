<?php
session_start();
// include "../../connection.php";
include 'pdoerror.php';

try {
    // Create a new PDO connection
    $conn = new PDO("mysql:host=localhost;dbname=online_quiz", "root", "");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Set error mode to exception

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect and sanitize the input data
        $username = isset($_POST['username']) ? strtoupper(trim($_POST['username'])) : null;
        $category = isset($_POST['category']) ? strtoupper(trim($_POST['category'])) : null;
        $status = isset($_POST['status']) ? strtoupper(trim($_POST['status'])) : null;
		$unique_id = isset($_POST['unique_id']) ? strtoupper(trim($_POST['unique_id'])) : null;
		$email = isset($_POST['student_email']) ? strtoupper(trim($_POST['student_email'])) : null;
        $current_time = date('Y-m-d H:i:s');

        
        if ($username === null || $category === null || $status === null) {
            echo json_encode(['error' => 'All fields are required.']);
            exit();
        }

     
        $query = "SELECT exam FROM customer_details WHERE unique_id = ?";
        $stmt1 = $conn->prepare($query);
        
        $stmt1->execute([$unique_id]);
        
        $presentcheck = $stmt1->rowCount();
      
        if ($presentcheck > 1) {
            $sql = "UPDATE customer_details SET status = :status, updated_at = :updated_at WHERE unique_id = :unique_id";
            try {
                $stmt = $conn->prepare($sql);              
                $stmt->bindParam(':status', $status);  
                $stmt->bindParam(':updated_at', $current_time);
                $stmt->bindParam(':unique_id', $unique_id);

                if ($stmt->execute()) {
                    $mess['succ'] = 'Payment Updated';
                } else {
                    $mess['warning'] = 'Fields Unchanged or Insert Failed';
                }
            } catch (PDOException $e) {
                $mess['error'] = 'Error: ' . $e->getMessage();
            }
        }
        else{
            $sql = "INSERT INTO customer_details (status, exam, fullname, unique_id, email, updated_at) VALUES (:status, :exam, :fullname, :unique_id, :email, :updated_at)";
            try {
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':fullname', $username);
                $stmt->bindParam(':status', $status);
                $stmt->bindParam(':exam', $category);
                $stmt->bindParam(':unique_id', $unique_id);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':updated_at', $current_time);

                if ($stmt->execute()) {
                    $mess['succ'] = 'Payment Inserted';
                } else {
                    $mess['warning'] = 'Fields Unchanged or Insert Failed';
                }
            } catch (PDOException $e) {
                $mess['error'] = 'Error: ' . $e->getMessage();
            }
        }
        

       

        // Send the response back as JSON
        echo json_encode($mess);
    } else {
        echo json_encode(['error' => 'Invalid request method']);
    }
} catch (PDOException $e) {
    // Handle database connection errors
    echo json_encode(['error' => 'Database connection failed: ' . $e->getMessage()]);
}
?>