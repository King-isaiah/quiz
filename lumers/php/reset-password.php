<?php
// lumers/php/reset-password.php (Enhanced Version)

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../includes/supabase-config.php';

// Get and validate input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
    exit;
}

$email = trim($data['email'] ?? '');
$password = $data['password'] ?? '';
$confirmPassword = $data['confirm_password'] ?? '';
$token = trim($data['token'] ?? '');

// Validation
$errors = [];

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Valid email is required';
}

if (strlen($password) < 8) {
    $errors[] = 'Password must be at least 8 characters';
}

if ($password !== $confirmPassword) {
    $errors[] = 'Passwords do not match';
}

if (empty($token)) {
    $errors[] = 'Reset token is required';
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode('. ', $errors)]);
    exit;
}

// Validate reset token
if (!OTPManager::validateResetToken($email, $token)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid or expired reset token. Please restart the password reset process.'
    ]);
    exit;
}

try {
    // Hash password (same as your registration)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Prepare update data for registration table
    $updateData = ['password' => $hashedPassword];
    
    // Add updated_at if column exists in your table
    // Check your registration table structure first
    // $updateData['updated_at'] = date('Y-m-d H:i:s');
    
    // Update password in registration table
    $result = SupabaseConnection::query('PATCH', 'registration', $updateData, [
        'email' => $email
    ]);
    
    if ($result['success']) {
        // Success - log the reset and return success
        OTPManager::markPasswordReset($email, $token);
        
        // Optional: Invalidate the reset token so it can't be used again
        // self::invalidateResetToken($email, $token);
        
        echo json_encode([
            'success' => true,
            'message' => '✅ Password reset successful! You can now login with your new password.',
            'redirect' => 'login.php' // Optional: Suggest redirect
        ]);
    } else {
        // Handle different error cases
        $errorMsg = 'Failed to update password. ';
        
        if ($result['code'] == 404) {
            $errorMsg .= 'User not found.';
        } elseif ($result['code'] == 400) {
            $errorMsg .= 'Invalid request.';
        } else {
            $errorMsg .= 'Please try again.';
        }
        
        echo json_encode([
            'success' => false,
            'message' => $errorMsg,
            'debug' => $result['code'] ?? 'unknown' // Remove in production
        ]);
    }
    
} catch (Exception $e) {
    error_log("Password reset error [{$email}]: " . $e->getMessage());
    
    echo json_encode([
        'success' => false,
        'message' => 'A system error occurred. Our team has been notified.'
    ]);
}

// Optional: Helper function to invalidate used tokens
function invalidateResetToken($email, $token) {
    // Update the OTP record to nullify the reset token
    $updateData = ['reset_token' => null];
    
    SupabaseConnection::query('PATCH', 'otp_verifications', $updateData, [
        'email' => $email,
        'reset_token' => $token
    ]);
}
?>