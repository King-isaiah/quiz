<?php
// lumers/includes/mail-simple.php

class SimpleMailSystem {
    
    public static function sendOTP($email) {
        error_log("SimpleMailSystem: Starting OTP process for $email");
        
        try {
            // 1. Validate email
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return ['success' => false, 'message' => 'Invalid email'];
            }
            
            // 2. Generate OTP
            $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
            $resetToken = bin2hex(random_bytes(32));
            
            error_log("Generated OTP: $otp");
            
            // 3. Save to database (you'll need to adapt this)
            // For now, just log it
            error_log("Would save OTP to database for: $email");
            
            // 4. Send email using PHP's mail() function
            $subject = 'Quiz System - Password Reset OTP';
            $message = self::getEmailTemplate($otp);
            $headers = [
                'From: no-reply@quizsystem.com',
                'Reply-To: support@quizsystem.com',
                'Content-Type: text/html; charset=UTF-8',
                'X-Mailer: PHP/' . phpversion()
            ];
            
            if (mail($email, $subject, $message, implode("\r\n", $headers))) {
                error_log("Email sent successfully to: $email");
                return [
                    'success' => true,
                    'message' => 'OTP sent successfully!',
                    'reset_token' => $resetToken
                ];
            } else {
                error_log("Failed to send email to: $email");
                return [
                    'success' => false,
                    'message' => 'Failed to send email. Please try again.'
                ];
            }
            
        } catch (Exception $e) {
            error_log("SimpleMailSystem error: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ];
        }
    }
    
    private static function getEmailTemplate($otp) {
        return '
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <title>Password Reset OTP</title>
        </head>
        <body style="font-family: Arial, sans-serif; line-height: 1.6;">
            <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
                <h2>Password Reset OTP</h2>
                <p>Your OTP code is: <strong style="font-size: 24px; color: #28a745;">' . $otp . '</strong></p>
                <p>This code will expire in 10 minutes.</p>
                <p>If you didn\'t request this, please ignore this email.</p>
                <hr>
                <p style="color: #666; font-size: 12px;">This is an automated message from Quiz System.</p>
            </div>
        </body>
        </html>
        ';
    }
}
?>