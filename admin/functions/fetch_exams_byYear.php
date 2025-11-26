<?php
session_start();
// require_once '../../connection.php';
require_once '../../superbase/config.php'; // Supabase connection

header('Content-Type: application/json');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 0); // Set to 0 to avoid breaking JSON response

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    // Check if JSON decoding was successful
    if (json_last_error() !== JSON_ERROR_NONE) {
        echo json_encode(['error' => 'Invalid JSON data: ' . json_last_error_msg()]);
        exit;
    }
    
    $examCategory = $data['exam'] ?? '';
    
    if (empty($examCategory)) {
        echo json_encode(['error' => 'Exam category is required']);
        exit;
    }
    
    try {
        // Check database preference from cookie (same as your toggle system)
        $useLocal = isset($_COOKIE['useLocalDB']) && $_COOKIE['useLocalDB'] === 'true';
        
        if ($useLocal) {
            // Use Local MySQL
            // Check if database connection is available
            if (!$link) {
                echo json_encode(['error' => 'Local MySQL connection failed']);
                exit;
            }
            
            $examCategory = mysqli_real_escape_string($link, $examCategory);
            $query = "SELECT * FROM exam_results WHERE exam_type = '$examCategory'";
            $result = mysqli_query($link, $query);
            
            if (!$result) {
                echo json_encode(['error' => 'MySQL error: ' . mysqli_error($link)]);
                exit;
            }
            
            $examResults = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $examResults[] = [
                    'username' => $row['username'] ?? 'N/A',
                    'exam_type' => $row['exam_type'] ?? 'N/A',
                    'total_question' => $row['total_question'] ?? 0,
                    'correct_answer' => $row['correct_answer'] ?? 0,
                    'wrong_answer' => $row['wrong_answer'] ?? 0,
                    'exam_time' => $row['exam_time'] ?? 'N/A',
                    'time_finished' => $row['time_finished'] ?? 'N/A',
                    'mins_spent' => $row['mins_spent'] ?? 0
                ];
            }
            
            mysqli_free_result($result);
            echo json_encode($examResults);
            
        } else {
            // Use Supabase
            $examResults = universalFetch('exam_results', ['exam_type' => $examCategory]);
            
            if (isset($examResults['error'])) {
                echo json_encode(['error' => 'Supabase error: ' . $examResults['error']]);
                exit;
            }
            
            if (empty($examResults)) {
                echo json_encode([]);
                exit;
            }
            
            // Format Supabase results to match expected structure
            $formattedResults = [];
            foreach ($examResults as $result) {
                $formattedResults[] = [
                    'username' => $result['username'] ?? 'N/A',
                    'exam_type' => $result['exam_type'] ?? 'N/A',
                    'total_question' => $result['total_question'] ?? 0,
                    'correct_answer' => $result['correct_answer'] ?? 0,
                    'wrong_answer' => $result['wrong_answer'] ?? 0,
                    'exam_time' => $result['exam_time'] ?? 'N/A',
                    'time_finished' => $result['time_finished'] ?? 'N/A',
                    'mins_spent' => $result['mins_spent'] ?? 0
                ];
            }
            
            echo json_encode($formattedResults);
        }
        
    } catch (Exception $e) {
        error_log("Error fetching exam results: " . $e->getMessage());
        echo json_encode(['error' => 'An error occurred while fetching results: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'Invalid request method. Expected POST, got ' . $_SERVER['REQUEST_METHOD']]);
}
?>