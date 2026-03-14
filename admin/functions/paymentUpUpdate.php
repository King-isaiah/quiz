<?php
session_start();
include '../../superbase/config.php';

$mess = [];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Collect and sanitize the input data
        $username = isset($_POST['username']) ? trim($_POST['username']) : null;
        $category = isset($_POST['category']) ? trim($_POST['category']) : null;
        $status = isset($_POST['status']) ? trim($_POST['status']) : null;
        $unique_id = isset($_POST['unique_id']) ? strtoupper(trim($_POST['unique_id'])) : null;
        $email = isset($_POST['student_email']) ? strtoupper(trim($_POST['student_email'])) : null;
        $current_time = date('Y-m-d H:i:s');

        if ($username === null || $category === null || $status === null) {
            echo json_encode(['error' => 'All fields are required.']);
            exit();
        }

        // Check if record exists in Supabase using universalFetch
        $existingRecords = universalFetch('customer_details', ['unique_id' => $unique_id]);
        
        if (is_array($existingRecords) && !isset($existingRecords['error']) && count($existingRecords) > 0) {
            
            $updateData = [
                'status' => $status,
                'updated_at' => $current_time
            ];
            
            $updateResult = updateUserStatus('customer_details', $unique_id, $updateData);
            
            if (isset($updateResult['error'])) {
                $mess['error'] = 'Update Error: ' . $updateResult['error'];
            } else {
                $mess['succ'] = 'Payment Updated';
            }
        } else {
            // Insert new record using createData
            $insertData = [
                'fullname' => $username,
                'status' => $status,
                'exam' => $category,
                'unique_id' => $unique_id,
                'email' => $email,
                'updated_at' => $current_time
            ];
            
            $insertResult = createData('customer_details', $insertData);
            
            if (isset($insertResult['error'])) {
                $mess['error'] = 'Insert Error: ' . extractSupabaseErrorMessage($insertResult['error']);
            } else {
                $mess['succ'] = 'Payment Inserted';
            }
        }

        // Send the response back as JSON
        header('Content-Type: application/json');
        echo json_encode($mess);
    } else {
        echo json_encode(['error' => 'Invalid request method']);
    }
} catch (Exception $e) {
    // Handle general errors
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
}
?>