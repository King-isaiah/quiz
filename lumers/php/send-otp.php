<?php
// lumers/php/send-otp.php

// =============================================
// ERROR REPORTING & DEBUGGING
// =============================================
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't show errors to users
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/php-errors.log'); // Create logs folder first

// Create logs directory if it doesn't exist
$logDir = __DIR__ . '/../logs';
if (!file_exists($logDir)) {
    mkdir($logDir, 0755, true);
}

// =============================================
// HEADERS
// =============================================
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=UTF-8');

// =============================================
// HANDLE PREFLIGHT REQUESTS (CORS)
// =============================================
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// =============================================
// LOGGING FUNCTION
// =============================================
function logMessage($message, $level = 'INFO') {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[$timestamp] [$level] $message\n";
    
    // Log to file
    $logFile = __DIR__ . '/../logs/send-otp.log';
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Also log to PHP error log
    error_log($message);
}

// =============================================
// START PROCESSING
// =============================================
logMessage("=== SEND OTP REQUEST STARTED ===");
logMessage("Request Method: " . $_SERVER['REQUEST_METHOD']);
logMessage("Request URI: " . $_SERVER['REQUEST_URI']);
logMessage("Remote IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));

// =============================================
// CHECK REQUEST METHOD
// =============================================
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    logMessage("ERROR: Invalid request method. Expected POST, got " . $_SERVER['REQUEST_METHOD']);
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'message' => 'Method not allowed. Please use POST.'
    ]);
    exit();
}

// =============================================
// INCLUDE MAIL CONFIG
// =============================================
$mailConfigPath = __DIR__ . '/../includes/mail-config.php';
if (!file_exists($mailConfigPath)) {
    logMessage("ERROR: Mail config file not found at: $mailConfigPath");
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server configuration error.'
    ]);
    exit();
}

require_once $mailConfigPath;
logMessage("Mail config loaded successfully");

// =============================================
// GET AND VALIDATE INPUT
// =============================================
$input = file_get_contents('php://input');
logMessage("Raw input received: " . substr($input, 0, 500));

if (empty($input)) {
    logMessage("ERROR: Empty request body");
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Empty request. Please provide email address.'
    ]);
    exit();
}

$data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    logMessage("ERROR: Invalid JSON input. Error: " . json_last_error_msg());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Invalid JSON data. Please check your request.'
    ]);
    exit();
}

if (!isset($data['email']) || empty($data['email'])) {
    logMessage("ERROR: Email field missing in request");
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Email address is required.'
    ]);
    exit();
}

$email = trim($data['email']);
logMessage("Processing email: " . $email);

// =============================================
// VALIDATE EMAIL FORMAT
// =============================================
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    logMessage("ERROR: Invalid email format: " . $email);
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Please enter a valid email address.'
    ]);
    exit();
}

// =============================================
// PROCESS OTP REQUEST
// =============================================
try {
    logMessage("Creating MailSender instance...");
    $mailSender = new MailSender();
    
    logMessage("Processing OTP request for: " . $email);
    $result = $mailSender->processOTPRequest($email);
    
    logMessage("OTP request completed. Result: " . json_encode($result));
    
    // Return the result
    echo json_encode($result);
    
} catch (Exception $e) {
    // Log detailed error information
    logMessage("EXCEPTION: " . $e->getMessage());
    logMessage("Exception trace: " . $e->getTraceAsString());
    
    // Get debug info if available
    $debugInfo = [];
    if (isset($mailSender) && method_exists($mailSender, 'getDebugInfo')) {
        $debugInfo = $mailSender->getDebugInfo();
        logMessage("Debug info: " . json_encode($debugInfo));
    }
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'An internal server error occurred. Please try again later.',
        'debug' => $_SERVER['SERVER_NAME'] === 'localhost' ? $e->getMessage() : null // Only show in localhost
    ]);
}

// =============================================
// LOG COMPLETION
// =============================================
logMessage("=== SEND OTP REQUEST COMPLETED ===\n");
?>