<?php
header('Content-Type: application/json');

include('../../connection.php');

$response = ['success' => false, 'message' => ''];

if (isset($_GET['username'])) {
    $username = trim($_GET['username']);
    
    try {
        // Prepare and execute query
        $stmt = $link->prepare("SELECT 	unique_id, email FROM registration WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $userData = $result->fetch_assoc();
            $response = [
                'success' => true,
                'unique_id' => $userData['unique_id'],
                'email' => $userData['email']
            ];
        } else {
            $response['message'] = 'User not found';
        }
    } catch (Exception $e) {
        $response['message'] = 'Database error: ' . $e->getMessage();
    }
} else {
    $response['message'] = 'Username not provided';
}

echo json_encode($response);
?>