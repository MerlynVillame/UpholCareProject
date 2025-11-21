<?php
/**
 * Email Configuration Test Script
 * This script tests the email configuration and helps diagnose email sending issues
 */

// Define constants first (before loading config files)
define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);

// Load configuration files
require_once ROOT . DS . 'config' . DS . 'config.php';
require_once ROOT . DS . 'config' . DS . 'email.php'; // Load email config explicitly
require_once ROOT . DS . 'config' . DS . 'database.php';
require_once ROOT . DS . 'core' . DS . 'NotificationService.php';

// Test email address
$testEmail = 'merlyn.lagrimas122021@gmail.com';

echo "<h2>📧 Email Configuration Test</h2>";
echo "<hr>";

// Check email configuration
echo "<h3>1. Email Configuration Check</h3>";
echo "<pre>";
echo "EMAIL_ENABLED: " . (defined('EMAIL_ENABLED') && EMAIL_ENABLED ? '✅ TRUE' : '❌ FALSE') . "\n";
echo "EMAIL_TEST_MODE: " . (defined('EMAIL_TEST_MODE') && EMAIL_TEST_MODE ? '✅ TRUE' : '❌ FALSE') . "\n";
echo "EMAIL_SMTP_HOST: " . (defined('EMAIL_SMTP_HOST') ? EMAIL_SMTP_HOST : '❌ NOT DEFINED') . "\n";
echo "EMAIL_SMTP_PORT: " . (defined('EMAIL_SMTP_PORT') ? EMAIL_SMTP_PORT : '❌ NOT DEFINED') . "\n";
echo "EMAIL_SMTP_USERNAME: " . (defined('EMAIL_SMTP_USERNAME') ? (EMAIL_SMTP_USERNAME === 'your-email@gmail.com' ? '❌ NOT CONFIGURED (placeholder)' : '✅ ' . EMAIL_SMTP_USERNAME) : '❌ NOT DEFINED') . "\n";
echo "EMAIL_SMTP_PASSWORD: " . (defined('EMAIL_SMTP_PASSWORD') ? (EMAIL_SMTP_PASSWORD === 'your-app-password' ? '❌ NOT CONFIGURED (placeholder)' : '✅ CONFIGURED') : '❌ NOT DEFINED') . "\n";
echo "EMAIL_FROM_ADDRESS: " . (defined('EMAIL_FROM_ADDRESS') ? EMAIL_FROM_ADDRESS : '❌ NOT DEFINED') . "\n";
echo "EMAIL_FROM_NAME: " . (defined('EMAIL_FROM_NAME') ? EMAIL_FROM_NAME : '❌ NOT DEFINED') . "\n";
echo "</pre>";

// Check PHPMailer
echo "<h3>2. PHPMailer Check</h3>";
$phpmailerAvailable = false;
$phpmailerPath = '';

if (file_exists(ROOT . DS . 'vendor' . DS . 'autoload.php')) {
    require_once ROOT . DS . 'vendor' . DS . 'autoload.php';
    $phpmailerAvailable = class_exists('PHPMailer\PHPMailer\PHPMailer');
    $phpmailerPath = 'vendor/autoload.php';
    echo "<p>✅ PHPMailer found via Composer autoload</p>";
} else if (file_exists(ROOT . DS . 'vendor' . DS . 'phpmailer' . DS . 'phpmailer' . DS . 'src' . DS . 'PHPMailer.php')) {
    require_once ROOT . DS . 'vendor' . DS . 'phpmailer' . DS . 'phpmailer' . DS . 'src' . DS . 'PHPMailer.php';
    require_once ROOT . DS . 'vendor' . DS . 'phpmailer' . DS . 'phpmailer' . DS . 'src' . DS . 'SMTP.php';
    require_once ROOT . DS . 'vendor' . DS . 'phpmailer' . DS . 'phpmailer' . DS . 'src' . DS . 'Exception.php';
    $phpmailerAvailable = class_exists('PHPMailer\PHPMailer\PHPMailer');
    $phpmailerPath = 'vendor/phpmailer/phpmailer/src/';
    echo "<p>✅ PHPMailer found manually</p>";
} else {
    echo "<p>❌ PHPMailer NOT FOUND - Will use PHP mail() function</p>";
    echo "<p><strong>Recommendation:</strong> Install PHPMailer via Composer: <code>composer install</code></p>";
}

// Test email sending
echo "<h3>3. Test Email Sending</h3>";

if (!defined('EMAIL_SMTP_USERNAME') || !defined('EMAIL_SMTP_PASSWORD') || 
    EMAIL_SMTP_USERNAME === 'your-email@gmail.com' || EMAIL_SMTP_PASSWORD === 'your-app-password') {
    echo "<div style='background: #fff3cd; border: 2px solid #ffc107; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>⚠️ Email Configuration Not Set!</strong><br>";
    echo "Please update <code>config/email.php</code> with your Gmail credentials:<br>";
    echo "<ul>";
    echo "<li>Set <code>EMAIL_SMTP_USERNAME</code> to your Gmail address</li>";
    echo "<li>Set <code>EMAIL_SMTP_PASSWORD</code> to your Gmail App Password</li>";
    echo "</ul>";
    echo "<strong>How to get Gmail App Password:</strong><br>";
    echo "<ol>";
    echo "<li>Go to your Google Account → Security</li>";
    echo "<li>Enable 2-Step Verification</li>";
    echo "<li>Go to App passwords</li>";
    echo "<li>Generate a new app password for 'Mail'</li>";
    echo "<li>Use the 16-character password in EMAIL_SMTP_PASSWORD</li>";
    echo "</ol>";
    echo "<p><strong>Current values:</strong></p>";
    echo "<ul>";
    echo "<li>EMAIL_SMTP_USERNAME: " . (defined('EMAIL_SMTP_USERNAME') ? htmlspecialchars(EMAIL_SMTP_USERNAME) : 'NOT DEFINED') . "</li>";
    echo "<li>EMAIL_SMTP_PASSWORD: " . (defined('EMAIL_SMTP_PASSWORD') ? (EMAIL_SMTP_PASSWORD === 'your-app-password' ? 'NOT CONFIGURED' : 'CONFIGURED') : 'NOT DEFINED') . "</li>";
    echo "</ul>";
    echo "</div>";
} else {
    try {
        $notificationService = new NotificationService();
        
        // Generate a test verification code
        $testCode = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        
        echo "<p>Sending test email to: <strong>{$testEmail}</strong></p>";
        echo "<p>Test verification code: <strong>{$testCode}</strong></p>";
        
        $result = $notificationService->sendAdminVerificationCode(
            $testEmail,
            'Test Admin',
            $testCode
        );
        
        if ($result) {
            echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<strong>✅ Email sent successfully!</strong><br>";
            echo "Please check the inbox (and spam folder) for: <strong>{$testEmail}</strong>";
            echo "</div>";
        } else {
            echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
            echo "<strong>❌ Email sending failed!</strong><br>";
            echo "Please check the email logs at: <code>logs/email_notifications.log</code><br>";
            echo "Also check PHP error logs for detailed error messages.";
            echo "</div>";
        }
    } catch (Exception $e) {
        echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage());
        echo "</div>";
    }
}

// Check email logs
echo "<h3>4. Recent Email Logs</h3>";
$logFile = ROOT . DS . 'logs' . DS . 'email_notifications.log';
if (file_exists($logFile)) {
    $logs = file($logFile);
    $recentLogs = array_slice($logs, -10); // Last 10 entries
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;'>";
    foreach ($recentLogs as $log) {
        echo htmlspecialchars($log);
    }
    echo "</pre>";
} else {
    echo "<p>No email logs found yet.</p>";
}

// PHPMailer detailed test
if ($phpmailerAvailable && defined('EMAIL_SMTP_USERNAME') && defined('EMAIL_SMTP_PASSWORD') && 
    EMAIL_SMTP_USERNAME !== 'your-email@gmail.com' && EMAIL_SMTP_PASSWORD !== 'your-app-password') {
    echo "<h3>5. PHPMailer Detailed Test</h3>";
    try {
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        $mail->isSMTP();
        $mail->Host = EMAIL_SMTP_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_SMTP_USERNAME;
        $mail->Password = EMAIL_SMTP_PASSWORD;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = EMAIL_SMTP_PORT;
        $mail->CharSet = 'UTF-8';
        $mail->SMTPDebug = 2; // Enable verbose debug output
        $mail->Debugoutput = function($str, $level) {
            echo "<pre style='background: #f5f5f5; padding: 5px; font-size: 11px;'>" . htmlspecialchars($str) . "</pre>";
        };
        
        $mail->setFrom(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);
        $mail->addAddress($testEmail);
        $mail->isHTML(true);
        $mail->Subject = 'Test Email - UphoCare';
        $mail->Body = '<h1>Test Email</h1><p>This is a test email from UphoCare system.</p>';
        
        echo "<p>Attempting to send test email with PHPMailer...</p>";
        $mail->send();
        echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>✅ PHPMailer test successful!</strong>";
        echo "</div>";
    } catch (\PHPMailer\PHPMailer\Exception $e) {
        echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
        echo "<strong>❌ PHPMailer Error:</strong><br>";
        echo htmlspecialchars($e->getMessage());
        echo "</div>";
    }
}

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>If email configuration is not set, update <code>config/email.php</code></li>";
echo "<li>If PHPMailer is not installed, run: <code>composer install</code></li>";
echo "<li>Check email logs at: <code>logs/email_notifications.log</code></li>";
echo "<li>Check PHP error logs for detailed error messages</li>";
echo "</ol>";
