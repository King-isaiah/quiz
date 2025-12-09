<?php
// lumers/includes/mail-config.php

// Include PHPMailer
require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

// =============================================
// ENVIRONMENT CONFIG CLASS
// =============================================
class EnvironmentConfig {
    private static $config = null;
    
    public static function load() {
        if (self::$config !== null) {
            return self::$config;
        }
        
        $envPath = __DIR__ . '/../.env';
        
        if (!file_exists($envPath)) {
            error_log(".env file not found at: " . $envPath);
            // Fallback to environment variables
            self::$config = [
                'GMAIL_USERNAME' => getenv('GMAIL_USERNAME') ?: '',
                'GMAIL_PASSWORD' => getenv('GMAIL_PASSWORD') ?: '',
                'SUPABASE_URL' => getenv('SUPABASE_URL') ?: '',
                'SUPABASE_KEY' => getenv('SUPABASE_KEY') ?: ''
            ];
            return self::$config;
        }
        
        $config = [];
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $config[trim($key)] = trim($value);
            }
        }
        
        self::$config = $config;
        
        // Log loaded config (without password)
        $logConfig = $config;
        if (isset($logConfig['GMAIL_PASSWORD'])) {
            $logConfig['GMAIL_PASSWORD'] = '***' . substr($logConfig['GMAIL_PASSWORD'], -4);
        }
        error_log("Loaded config: " . json_encode($logConfig));
        
        return self::$config;
    }
    
    public static function get($key, $default = '') {
        $config = self::load();
        return $config[$key] ?? $default;
    }
}

// =============================================
// SUPABASE HELPER CLASS
// =============================================
class SupabaseHelper {
    
    public static function createOTPRecord($email, $otp, $resetToken, $ip = null, $userAgent = null) {
        $config = EnvironmentConfig::load();
        $supabaseUrl = $config['SUPABASE_URL'] ?? '';
        $supabaseKey = $config['SUPABASE_KEY'] ?? '';
        
        if (empty($supabaseUrl) || empty($supabaseKey)) {
            error_log("Supabase credentials missing. URL: " . ($supabaseUrl ? 'set' : 'empty') . ", Key: " . ($supabaseKey ? 'set' : 'empty'));
            return false;
        }
        
        // Prepare data for Supabase
        $data = [
            'email' => $email,
            'otp_code' => $otp,
            'reset_token' => $resetToken,
            'expires_at' => date('Y-m-d H:i:s', time() + 600), // 10 minutes
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        error_log("Creating OTP record for: $email");
        
        // Send to Supabase
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $supabaseUrl . '/rest/v1/otp_verifications');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $supabaseKey,
            'Authorization: Bearer ' . $supabaseKey,
            'Content-Type: application/json',
            'Prefer: return=minimal'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("cURL error creating OTP record: " . $error);
            return false;
        }
        
        error_log("Supabase response code: $httpCode, Response: " . substr($response, 0, 200));
        
        return $httpCode >= 200 && $httpCode < 300;
    }
    
    public static function checkRateLimit($email) {
        $config = EnvironmentConfig::load();
        $supabaseUrl = $config['SUPABASE_URL'] ?? '';
        $supabaseKey = $config['SUPABASE_KEY'] ?? '';
        
        if (empty($supabaseUrl) || empty($supabaseKey)) {
            error_log("Supabase credentials missing for rate limit check");
            return true; // Allow if can't check
        }
        
        // Check for OTP requests in last 5 minutes
        $fiveMinutesAgo = date('Y-m-d H:i:s', time() - 300);
        
        $url = $supabaseUrl . '/rest/v1/otp_verifications?' . 
               http_build_query([
                   'email' => 'eq.' . urlencode($email),
                   'created_at' => 'gte.' . $fiveMinutesAgo,
                   'select' => 'count'
               ]);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'apikey: ' . $supabaseKey,
            'Authorization: Bearer ' . $supabaseKey,
            'Content-Type: application/json',
            'Prefer: return=minimal'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            error_log("cURL error checking rate limit: " . $error);
            return true;
        }
        
        if ($httpCode === 200) {
            $result = json_decode($response, true);
            $count = $result[0]['count'] ?? 0;
            error_log("Rate limit check for $email: $count requests in last 5 minutes");
            return $count < 3; // Allow max 3 attempts in 5 minutes
        }
        
        error_log("Rate limit check failed with HTTP code: $httpCode");
        return true; // If can't check, allow by default
    }
    
    public static function checkEmailExists($email) {
        // For now, return true - you can implement this later
        // to check if email exists in your users table
        return true;
    }
}

// =============================================
// MAIL SENDER CLASS
// =============================================
class MailSender {
    private $mail;
    private $config;
    private $debugLog = [];
    
    public function __construct() {
        // Load configuration
        $this->config = EnvironmentConfig::load();
        
        // Validate required configuration
        $this->validateConfig();
        
        // Initialize PHPMailer
        $this->mail = new PHPMailer(true);
        $this->setupSMTP();
    }
    
    private function validateConfig() {
        $required = ['GMAIL_USERNAME', 'GMAIL_PASSWORD'];
        foreach ($required as $key) {
            if (empty($this->config[$key])) {
                $errorMsg = "Missing required configuration: $key in .env file";
                error_log($errorMsg);
                throw new Exception($errorMsg);
            }
        }
        
        error_log("Configuration validated successfully");
    }
    
    private function setupSMTP() {
        try {
            // Enable verbose debug output
            $this->mail->SMTPDebug = SMTP::DEBUG_SERVER; // Full debug output
            $this->mail->Debugoutput = function($str, $level) {
                $this->debugLog[] = $str;
                error_log("PHPMailer [$level]: $str");
            };
            
            // Server settings for Gmail
            $this->mail->isSMTP();
            $this->mail->Host       = 'smtp.gmail.com';
            $this->mail->SMTPAuth   = true;
            $this->mail->Username   = $this->config['GMAIL_USERNAME'];
            $this->mail->Password   = $this->config['GMAIL_PASSWORD'];
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port       = 587;
            
            // Important settings
            $this->mail->Timeout = 30;
            $this->mail->SMTPKeepAlive = false;
            
            // Sender info
            $this->mail->setFrom($this->config['GMAIL_USERNAME'], 'Quiz System');
            $this->mail->addReplyTo($this->config['GMAIL_USERNAME'], 'Quiz System Support');
            
            // Email format
            $this->mail->isHTML(true);
            $this->mail->CharSet = 'UTF-8';
            
            // Optional: Add DKIM, DMARC here if needed
            
            error_log("SMTP setup completed for: " . $this->config['GMAIL_USERNAME']);
            
        } catch (Exception $e) {
            $errorMsg = "Mail setup error: " . $e->getMessage();
            error_log($errorMsg);
            throw new Exception($errorMsg);
        }
    }
    
    public function sendOTP($toEmail, $otpCode) {
        try {
            // Clear any previous settings
            $this->mail->clearAllRecipients();
            $this->mail->clearAttachments();
            $this->mail->clearCustomHeaders();
            
            // Validate recipient email
            if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
                error_log("Invalid recipient email: $toEmail");
                return false;
            }
            
            // Add recipient
            $this->mail->addAddress($toEmail);
            
            // Email content
            $this->mail->Subject = 'Quiz System - Password Reset OTP';
            $this->mail->Body    = $this->getOTPTemplate($otpCode);
            $this->mail->AltBody = "Your OTP code is: $otpCode\nThis code will expire in 10 minutes.";
            
            error_log("Attempting to send OTP to: $toEmail");
            error_log("Using SMTP username: " . $this->mail->Username);
            
            // Send email
            $sent = $this->mail->send();
            
            if (!$sent) {
                $errorInfo = $this->mail->ErrorInfo;
                error_log("Failed to send OTP to $toEmail. Error: $errorInfo");
                error_log("Debug log: " . implode("\n", $this->debugLog));
                return false;
            }
            
            error_log("✅ OTP sent successfully to: $toEmail");
            return true;
            
        } catch (Exception $e) {
            error_log("Exception sending OTP to $toEmail: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());
            return false;
        }
    }
    
    public function processOTPRequest($email) {
        try {
            error_log("🚀 Starting OTP request process for: $email");
            
            // 1. Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                error_log("Invalid email format: $email");
                return [
                    'success' => false,
                    'message' => 'Please enter a valid email address'
                ];
            }
            
            // 2. Check rate limiting
            error_log("Checking rate limit for: $email");
            if (!SupabaseHelper::checkRateLimit($email)) {
                error_log("Rate limit exceeded for: $email");
                return [
                    'success' => false,
                    'message' => 'Too many OTP requests. Please wait 5 minutes.'
                ];
            }
            
            // 3. Check if email exists in system
            error_log("Checking if email exists: $email");
            if (!SupabaseHelper::checkEmailExists($email)) {
                error_log("Email not found in system: $email");
                return [
                    'success' => false,
                    'message' => 'Email not found in our system.'
                ];
            }
            
            // 4. Generate OTP and reset token
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $resetToken = bin2hex(random_bytes(32));
            error_log("Generated OTP: $otp for: $email");
            
            // 5. Get client info
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            // 6. Save to Supabase
            error_log("Saving OTP record to Supabase...");
            $saved = SupabaseHelper::createOTPRecord($email, $otp, $resetToken, $ip, $userAgent);
            
            if (!$saved) {
                error_log("❌ Failed to save OTP record to Supabase for: $email");
                return [
                    'success' => false,
                    'message' => 'Failed to create OTP record. Please try again.'
                ];
            }
            
            error_log("✅ OTP record saved to Supabase");
            
            // 7. Send OTP via email
            error_log("Sending OTP email...");
            $emailSent = $this->sendOTP($email, $otp);
            
            if ($emailSent) {
                error_log("✅ OTP email sent successfully to: $email");
                return [
                    'success' => true,
                    'message' => 'OTP sent successfully! Check your email.',
                    'reset_token' => $resetToken
                ];
            } else {
                error_log("❌ Failed to send OTP email to: $email");
                $errorInfo = $this->mail->ErrorInfo ?? 'Unknown error';
                
                // Provide user-friendly error message
                if (strpos($errorInfo, 'password') !== false || strpos($errorInfo, 'authentication') !== false) {
                    $userMessage = 'Email service configuration error. Please contact support.';
                } elseif (strpos($errorInfo, 'connection') !== false) {
                    $userMessage = 'Unable to connect to email service. Please try again later.';
                } else {
                    $userMessage = 'Failed to send OTP email. Please check your email address or try again later.';
                }
                
                return [
                    'success' => false,
                    'message' => $userMessage,
                    'debug' => $errorInfo // Only for debugging, remove in production
                ];
            }
            
        } catch (Exception $e) {
            error_log("❌ Exception in processOTPRequest for $email: " . $e->getMessage());
            error_log("Exception trace: " . $e->getTraceAsString());
            
            return [
                'success' => false,
                'message' => 'An unexpected error occurred. Please try again.'
            ];
        }
    }
    
    private function getOTPTemplate($otp) {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8'>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; background: #f9f9f9; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 2px solid #28a745; }
        .otp-box { 
            background: #fff; 
            border: 3px solid #28a745;
            padding: 30px; 
            text-align: center; 
            margin: 25px 0; 
            border-radius: 10px;
            font-family: monospace;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .otp-code { 
            font-size: 42px; 
            font-weight: bold; 
            color: #28a745; 
            letter-spacing: 10px;
            margin: 15px 0;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .footer { 
            margin-top: 30px; 
            padding-top: 20px; 
            border-top: 1px solid #ddd; 
            font-size: 12px; 
            color: #666;
            text-align: center;
        }
        .warning { 
            background: #fff3cd; 
            border: 1px solid #ffeaa7; 
            padding: 15px; 
            border-radius: 5px; 
            margin: 20px 0;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <h2 style='color: #333; margin: 0;'>🔐 Quiz System Password Reset</h2>
        </div>
        
        <p>Hello,</p>
        
        <p>You requested to reset your password for the Quiz System. Use the OTP code below to verify your identity:</p>
        
        <div class='otp-box'>
            <div style='font-size: 18px; color: #666; margin-bottom: 15px;'>Your verification code:</div>
            <div class='otp-code'>$otp</div>
            <div style='color: #dc3545; font-size: 14px; margin-top: 15px;'>
                ⏰ <strong>Expires in 10 minutes</strong>
            </div>
        </div>
        
        <div class='warning'>
            <strong>🔒 Security Notice:</strong>
            <ul style='margin: 10px 0; padding-left: 20px;'>
                <li>Never share this code with anyone</li>
                <li>The Quiz System team will never ask for your OTP</li>
                <li>If you didn't request this, please ignore this email</li>
                <li>This code can only be used once</li>
            </ul>
        </div>
        
        <p>Enter this code on the password reset page to continue with your password reset.</p>
        
        <p style='text-align: center;'>
            <a href="#" class='btn'>Reset Password Now</a>
        </p>
        
        <p>If you're having issues, please contact our support team.</p>
        
        <div class='footer'>
            <p>This is an automated message from <strong>Quiz System</strong>.</p>
            <p>© " . date('Y') . " Quiz System. All rights reserved.</p>
            <p style='font-size: 10px; color: #999;'>If you received this email by mistake, please delete it.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
    
    // Method to get debug info for troubleshooting
    public function getDebugInfo() {
        return [
            'config_loaded' => !empty($this->config['GMAIL_USERNAME']),
            'debug_log' => $this->debugLog,
            'last_error' => $this->mail->ErrorInfo ?? 'No error'
        ];
    }
}
?>