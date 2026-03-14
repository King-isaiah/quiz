<?php

include('../../superbase/config.php');

$response = ['success' => false, 'message' => ''];

if (isset($_GET['username'])) {
    $username = trim($_GET['username']);
    
    try {
        // Fetch user from Supabase using universalFetch with username filter
        $userData = universalFetch('registration', ['username' => $username], ['unique_id', 'email']);
        
        if (is_array($userData) && !isset($userData['error']) && count($userData) > 0) {
            // Get the first matching user
            $user = $userData[0];
            $response = [
                'success' => true,
                'unique_id' => $user['unique_id'] ?? '',
                'email' => $user['email'] ?? ''
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

header('Content-Type: application/json');
echo json_encode($response);
?>