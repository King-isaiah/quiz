<?php
// lumers/includes/smtp-direct.php

class DirectSMTP {
    
    public static function sendEmail($to, $subject, $message) {
        $config = self::loadConfig();
        
        if (empty($config['smtp_host'])) {
            // Try to determine SMTP server from hosting
            $config = self::detectSMTP();
        }
        
        return self::sendViaSMTP($to, $subject, $message, $config);
    }
    
    private static function loadConfig() {
        // Try to load from .env
        $envPath = __DIR__ . '/../.env';
        $config = [];
        
        if (file_exists($envPath)) {
            $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            foreach ($lines as $line) {
                if (strpos(trim($line), '#') === 0) continue;
                if (strpos($line, '=') !== false) {
                    list($key, $value) = explode('=', $line, 2);
                    $config[trim($key)] = trim($value);
                }
            }
        }
        
        return $config;
    }
    
    private static function detectSMTP() {
        // Common hosting SMTP servers
        $hostingSMTP = [
            'cpanel' => [
                'host' => 'localhost',
                'port' => 25,
                'auth' => false
            ],
            'plesk' => [
                'host' => 'localhost',
                'port' => 25,
                'auth' => false
            ],
            'directadmin' => [
                'host' => 'localhost',
                'port' => 25,
                'auth' => false
            ]
        ];
        
        // Return first available
        return $hostingSMTP['cpanel'];
    }
    
    private static function sendViaSMTP($to, $subject, $message, $config) {
        $smtpHost = $config['host'] ?? 'localhost';
        $smtpPort = $config['port'] ?? 25;
        $useAuth = $config['auth'] ?? false;
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';
        
        // Create the email headers
        $headers = [
            'From: no-reply@' . $_SERVER['HTTP_HOST'],
            'Reply-To: no-reply@' . $_SERVER['HTTP_HOST'],
            'Content-Type: text/html; charset=UTF-8',
            'X-Mailer: PHP/' . phpversion()
        ];
        
        // Try to send
        return mail($to, $subject, $message, implode("\r\n", $headers));
    }
}
?>