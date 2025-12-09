<?php
// lumers/includes/supabase-config.php

class SupabaseConnection {
    private static $client = null;
    
    public static function getClient() {
        if (self::$client !== null) {
            return self::$client;
        }
        
        // Load environment variables
        $env = self::loadEnv();
        
        $supabaseUrl = $env['SUPABASE_URL'] ?? '';
        $supabaseKey = $env['SUPABASE_KEY'] ?? '';
        
        if (empty($supabaseUrl) || empty($supabaseKey)) {
            throw new Exception('Supabase credentials not configured in .env file');
        }
        
        // Create Supabase client using cURL (no external library needed)
        self::$client = [
            'url' => rtrim($supabaseUrl, '/'),
            'key' => $supabaseKey,
            'headers' => [
                'apikey: ' . $supabaseKey,
                'Authorization: Bearer ' . $supabaseKey,
                'Content-Type: application/json',
                'Prefer: return=minimal'
            ]
        ];
        
        return self::$client;
    }
    
    public static function query($method, $table, $data = null, $filters = null) {
        $client = self::getClient();
        
        // Build URL
        $url = $client['url'] . '/rest/v1/' . $table;
        
        // Add filters to URL if provided
        if ($filters && is_array($filters)) {
            $queryParams = [];
            foreach ($filters as $key => $value) {
                $queryParams[] = $key . '=eq.' . urlencode($value);
            }
            if (!empty($queryParams)) {
                $url .= '?' . implode('&', $queryParams);
            }
        }
        
        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $client['headers']);
        
        // Set method and data
        switch (strtoupper($method)) {
            case 'POST':
                curl_setopt($ch, CURLOPT_POST, true);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
                
            case 'PUT':
            case 'PATCH':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
                if ($data) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                }
                break;
                
            case 'DELETE':
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
                
            case 'GET':
            default:
                // GET is default
                break;
        }
        
        // Execute request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new Exception('cURL Error: ' . $error);
        }
        
        return [
            'success' => ($httpCode >= 200 && $httpCode < 300),
            'code' => $httpCode,
            'data' => $response ? json_decode($response, true) : null,
            'raw' => $response
        ];
    }
    
    private static function loadEnv() {
        static $env = null;
        
        if ($env !== null) {
            return $env;
        }
        
        $envPath = __DIR__ . '/../.env';
        
        if (!file_exists($envPath)) {
            // Fallback to direct environment variables
            $env = [
                'SUPABASE_URL' => getenv('SUPABASE_URL'),
                'SUPABASE_KEY' => getenv('SUPABASE_KEY'),
                'GMAIL_USERNAME' => getenv('GMAIL_USERNAME'),
                'GMAIL_PASSWORD' => getenv('GMAIL_PASSWORD')
            ];
            return $env;
        }
        
        $env = [];
        $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) continue;
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $env[trim($key)] = trim($value);
            }
        }
        
        return $env;
    }
}

// OTP Management Class
class OTPManager {
    
    public static function createOTP($email, $ip = null, $userAgent = null) {
        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        // Generate reset token
        $resetToken = bin2hex(random_bytes(32));
        
        // Set expiry (10 minutes from now)
        $expiresAt = date('Y-m-d H:i:s', time() + 600);
        
        $data = [
            'email' => $email,
            'otp_code' => $otp,
            'reset_token' => $resetToken,
            'expires_at' => $expiresAt,
            'attempts' => 0,
            'ip_address' => $ip,
            'user_agent' => $userAgent
        ];
        
        // Insert into Supabase
        $result = SupabaseConnection::query('POST', 'otp_verifications', $data);
        
        if ($result['success']) {
            return [
                'otp' => $otp,
                'reset_token' => $resetToken,
                'id' => $result['data'][0]['id'] ?? null
            ];
        }
        
        return false;
    }
    
    public static function verifyOTP($email, $otp) {
        // Find unexpired, unverified OTP for this email
        $currentTime = date('Y-m-d H:i:s');
        
        $result = SupabaseConnection::query('GET', 'otp_verifications', null, [
            'email' => $email,
            'verified_at' => 'null'  // Look for unverified records
        ]);
        
        if (!$result['success'] || empty($result['data'])) {
            return [
                'success' => false,
                'message' => 'No active OTP found for this email'
            ];
        }
        
        // Find the most recent unexpired OTP
        $validOTP = null;
        foreach ($result['data'] as $record) {
            if ($record['expires_at'] > $currentTime) {
                $validOTP = $record;
                break;
            }
        }
        
        if (!$validOTP) {
            return [
                'success' => false,
                'message' => 'OTP has expired'
            ];
        }
        
        // Check attempts
        if ($validOTP['attempts'] >= 5) {
            return [
                'success' => false,
                'message' => 'Too many attempts. Please request a new OTP.'
            ];
        }
        
        // Verify OTP
        if ($validOTP['otp_code'] !== $otp) {
            // Increment attempts
            self::incrementAttempts($validOTP['id']);
            
            return [
                'success' => false,
                'message' => 'Invalid OTP code'
            ];
        }
        
        // Mark as verified
        $updateData = [
            'verified_at' => date('Y-m-d H:i:s'),
            'attempts' => $validOTP['attempts'] + 1
        ];
        
        $updateResult = SupabaseConnection::query('PATCH', 'otp_verifications', $updateData, [
            'id' => $validOTP['id']
        ]);
        
        if ($updateResult['success']) {
            return [
                'success' => true,
                'reset_token' => $validOTP['reset_token'],
                'message' => 'OTP verified successfully'
            ];
        }
        
        return [
            'success' => false,
            'message' => 'Failed to verify OTP'
        ];
    }
    
    public static function validateResetToken($email, $token) {
        $currentTime = date('Y-m-d H:i:s');
        
        $result = SupabaseConnection::query('GET', 'otp_verifications', null, [
            'email' => $email,
            'reset_token' => $token,
            'verified_at' => 'not.null'  // Must be verified
        ]);
        
        if (!$result['success'] || empty($result['data'])) {
            return false;
        }
        
        $record = $result['data'][0];
        
        // Check if token is still valid (15 minutes after verification)
        $verifiedTime = strtotime($record['verified_at']);
        $currentTimestamp = time();
        
        if (($currentTimestamp - $verifiedTime) > 900) { // 15 minutes
            return false;
        }
        
        return true;
    }
    
    public static function markPasswordReset($email, $token) {
        // Log the password reset
        $logData = [
            'email' => $email,
            'action' => 'reset',
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null
        ];
        
        SupabaseConnection::query('POST', 'password_reset_logs', $logData);
        
        // You could also invalidate the token here if you want one-time use
        // $updateData = ['reset_token' => null];
        // SupabaseConnection::query('PATCH', 'otp_verifications', $updateData, [
        //     'email' => $email,
        //     'reset_token' => $token
        // ]);
        
        return true;
    }
    
    private static function incrementAttempts($otpId) {
        // Get current attempts
        $result = SupabaseConnection::query('GET', 'otp_verifications', null, [
            'id' => $otpId
        ]);
        
        if ($result['success'] && !empty($result['data'])) {
            $currentAttempts = $result['data'][0]['attempts'] ?? 0;
            
            $updateData = [
                'attempts' => $currentAttempts + 1
            ];
            
            SupabaseConnection::query('PATCH', 'otp_verifications', $updateData, [
                'id' => $otpId
            ]);
        }
    }
    
    public static function cleanupExpiredOTPs() {
        // Optional: Clean up expired OTPs older than 24 hours
        $expiryTime = date('Y-m-d H:i:s', time() - 86400);
        
        // This would require a more complex query or a Supabase function
        // For now, we'll handle expiry in the verification logic
    }
}
?>