<?php 
session_start();
require_once '../../connection.php'; // Local connection
require_once '../../superbase/config.php'; // Supabase connection

header('Content-Type: application/json');
header('X-Content-Type-Options: nosniff');

// Decode JSON input
$requestData = json_decode(file_get_contents('php://input'), true);
$exam = $requestData['exam'] ?? null; 

if (!$exam) {
    http_response_code(400); // Bad Request
    echo json_encode(['error' => 'Exam must be provided.']);
    exit;
}

try {
    // Check database preference from cookie
    $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';
    
    if ($useLocal) {
        // Use Local MySQL (your existing code)
        $stmt = mysqli_prepare($link, "SELECT * FROM customer_details 
            LEFT JOIN exam_category AS examCat ON customer_details.exam = examCat.category 
            WHERE exam = ?");

        if ($stmt) {      
            mysqli_stmt_bind_param($stmt, 's', $exam);      
            mysqli_stmt_execute($stmt);
        
            $res = mysqli_stmt_get_result($stmt);
            $data = [];

            while ($row = mysqli_fetch_assoc($res)) {
                $data[] = [
                    'username' => $row['fullname'] ?? $row['username'] ?? 'N/A',
                    'unique_id' => $row['unique_id'] ?? 'N/A',
                    'exam' => $row['exam'] ?? 'N/A',
                    'year' => $row['year'] ?? 'N/A',
                    'status' => $row['status'] ?? 'N/A',
                    'day_of_payment' => $row['date_purchased'] ?? 'N/A',                    
                    'email' => $row['email'] ?? 'N/A',                  
                ];
            }

            mysqli_free_result($res);
            mysqli_stmt_close($stmt);
            
            echo json_encode($data);
            
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to prepare the SQL statement.']);
            exit;
        }
        
    } else {
        // Use Supabase
        // First get customer details with the exam filter
        $customerDetails = universalFetch('customer_details', ['exam' => $exam]);
        
        if (isset($customerDetails['error'])) {
            http_response_code(500);
            echo json_encode(['error' => 'Supabase error: ' . $customerDetails['error']]);
            exit;
        }
        
        $data = [];
        
        if (!empty($customerDetails)) {
            // Get exam categories for additional data if needed
            $examCategories = universalFetch('exam_category');
            $categoryMap = [];
            
            if (is_array($examCategories) && !isset($examCategories['error'])) {
                foreach ($examCategories as $category) {
                    $categoryMap[$category['category']] = $category;
                }
            }
            
            foreach ($customerDetails as $row) {
                $categoryInfo = $categoryMap[$row['exam']] ?? [];
                
                $data[] = [
                    'username' => $row['fullname'] ?? $row['username'] ?? 'N/A',
                    'unique_id' => $row['unique_id'] ?? 'N/A',
                    'exam' => $row['exam'] ?? 'N/A',
                    'year' => $categoryInfo['year'] ?? $row['year'] ?? 'N/A',
                    'status' => $row['status'] ?? 'N/A',
                    'day_of_payment' => $row['date_purchased'] ?? $row['day_of_payment'] ?? 'N/A',                    
                    'email' => $row['email'] ?? 'N/A',                  
                ];
            }
        }
        
        echo json_encode($data);
    }
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred: ' . $e->getMessage()]);
}
?>