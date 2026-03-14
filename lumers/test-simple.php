<?php
// test-simple.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require_once 'includes/mail-simple.php';

// Get email from query string or POST
$email = $_GET['email'] ?? $_POST['email'] ?? 'test@example.com';

if (empty($email)) {
    echo json_encode(['success' => false, 'message' => 'No email provided']);
    exit;
}

$result = SimpleMailSystem::sendOTP($email);

echo json_encode($result);
?>