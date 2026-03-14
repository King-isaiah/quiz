<?php
// lumers/php/verify-otp.php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/../includes/supabase-config.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);
$email = $data['email'] ?? '';
$otp = $data['otp'] ?? '';

if (empty($email) || empty($otp)) {
    echo json_encode([
        'success' => false,
        'message' => 'Email and OTP are required'
    ]);
    exit;
}

// Verify OTP using Supabase
$result = OTPManager::verifyOTP($email, $otp);

echo json_encode($result);
?>