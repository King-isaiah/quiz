<?php
// debug-email.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

echo "<h2>Email System Debug</h2>";

// Test 1: Check PHP version and extensions
echo "<h3>1. PHP Environment</h3>";
echo "<p>PHP Version: " . phpversion() . "</p>";
echo "<p>OpenSSL: " . (extension_loaded('openssl') ? '✓ Loaded' : '✗ Missing') . "</p>";
echo "<p>cURL: " . (extension_loaded('curl') ? '✓ Loaded' : '✗ Missing') . "</p>";
echo "<p>SMTP: " . (function_exists('fsockopen') ? '✓ Available' : '✗ Unavailable') . "</p>";

// Test 2: Check file permissions and paths
echo "<h3>2. File System Check</h3>";
$files = [
    'includes/mail-config.php',
    'includes/supabase-config.php',
    'PHPMailer/src/PHPMailer.php',
    '.env'
];

foreach ($files as $file) {
    $path = __DIR__ . '/' . $file;
    echo "<p>" . $file . ": ";
    if (file_exists($path)) {
        echo "✓ Exists, ";
        echo is_readable($path) ? "✓ Readable, " : "✗ Not readable, ";
        echo "Size: " . filesize($path) . " bytes";
    } else {
        echo "✗ Missing";
    }
    echo "</p>";
}

// Test 3: Check .env file content (hide password)
echo "<h3>3. Environment Variables</h3>";
$envPath = __DIR__ . '/.env';
if (file_exists($envPath)) {
    $content = file_get_contents($envPath);
    echo "<pre style='background: #f5f5f5; padding: 10px;'>";
    // Hide password for security
    echo htmlspecialchars(preg_replace('/GMAIL_PASSWORD=.*/i', 'GMAIL_PASSWORD=*****', $content));
    echo "</pre>";
} else {
    echo "<p style='color: red;'>✗ .env file not found!</p>";
}

// Test 4: Test PHPMailer loading
echo "<h3>4. PHPMailer Test</h3>";
try {
    require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
    require_once __DIR__ . '/PHPMailer/src/SMTP.php';
    require_once __DIR__ . '/PHPMailer/src/Exception.php';
    
    echo "<p>PHPMailer: ✓ Loaded successfully</p>";
    
    // Try to create instance
    $mail = new PHPMailer\PHPMailer\PHPMailer(true);
    echo "<p>PHPMailer instance: ✓ Created</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>PHPMailer error: " . $e->getMessage() . "</p>";
}

// Test 5: Simple SMTP test
echo "<h3>5. Simple SMTP Connection Test</h3>";
$host = 'smtp.gmail.com';
$ports = [587, 465, 25];

foreach ($ports as $port) {
    $connection = @fsockopen($host, $port, $errno, $errstr, 5);
    if (is_resource($connection)) {
        echo "<p>Port $port: ✓ Open</p>";
        fclose($connection);
    } else {
        echo "<p>Port $port: ✗ Closed ($errstr)</p>";
    }
}

// Test 6: Test configuration loading
echo "<h3>6. Configuration Loading Test</h3>";
try {
    if (file_exists(__DIR__ . '/includes/mail-config.php')) {
        require_once __DIR__ . '/includes/mail-config.php';
        
        // Test EnvironmentConfig
        if (class_exists('EnvironmentConfig')) {
            $config = EnvironmentConfig::load();
            echo "<p>EnvironmentConfig: ✓ Loaded</p>";
            echo "<p>GMAIL_USERNAME: " . (!empty($config['GMAIL_USERNAME']) ? '✓ Set' : '✗ Empty') . "</p>";
            echo "<p>GMAIL_PASSWORD: " . (!empty($config['GMAIL_PASSWORD']) ? '✓ Set' : '✗ Empty') . "</p>";
            
            // Try to create MailSender
            if (class_exists('MailSender')) {
                echo "<p>MailSender class: ✓ Found</p>";
                try {
                    $mailSender = new MailSender();
                    echo "<p>MailSender instance: ✓ Created</p>";
                    
                    // Try simple test
                    echo "<h3>7. Quick Send Test</h3>";
                    $testResult = $mailSender->processOTPRequest('test@example.com');
                    echo "<pre>";
                    print_r($testResult);
                    echo "</pre>";
                    
                } catch (Exception $e) {
                    echo "<p style='color: red;'>MailSender creation failed: " . $e->getMessage() . "</p>";
                }
            } else {
                echo "<p style='color: red;'>✗ MailSender class not found!</p>";
            }
        } else {
            echo "<p style='color: red;'>✗ EnvironmentConfig class not found!</p>";
        }
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Configuration error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr><h3>PHP Error Log</h3>";
echo "<pre>";
$logPath = __DIR__ . '/logs/php-errors.log';
if (file_exists($logPath)) {
    echo htmlspecialchars(file_get_contents($logPath));
} else {
    echo "No error log found at: $logPath";
}
echo "</pre>";
?>