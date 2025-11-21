<?php
/**
 * SMTP Authentication Test Script
 * This script tests Gmail SMTP authentication with detailed debugging
 */

// Define constants first
define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);

// Load configuration
require_once ROOT . DS . 'config' . DS . 'config.php';
require_once ROOT . DS . 'config' . DS . 'email.php';

echo "<h2>🔐 Gmail SMTP Authentication Test</h2>";
echo "<hr>";

// Check PHPMailer
$phpmailerAvailable = false;
if (file_exists(ROOT . DS . 'vendor' . DS . 'autoload.php')) {
    require_once ROOT . DS . 'vendor' . DS . 'autoload.php';
    $phpmailerAvailable = class_exists('PHPMailer\PHPMailer\PHPMailer');
}

if (!$phpmailerAvailable) {
    echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>❌ PHPMailer not found!</strong><br>";
    echo "Please install PHPMailer: <code>composer install</code>";
    echo "</div>";
    exit;
}

// Display current configuration (mask password)
echo "<h3>1. Current Configuration</h3>";
echo "<pre style='background: #f5f5f5; padding: 15px; border-radius: 5px;'>";
echo "EMAIL_SMTP_HOST: " . EMAIL_SMTP_HOST . "\n";
echo "EMAIL_SMTP_PORT: " . EMAIL_SMTP_PORT . "\n";
echo "EMAIL_SMTP_USERNAME: " . EMAIL_SMTP_USERNAME . "\n";
echo "EMAIL_SMTP_PASSWORD: " . str_repeat('*', strlen(EMAIL_SMTP_PASSWORD)) . " (length: " . strlen(EMAIL_SMTP_PASSWORD) . " characters)\n";
echo "EMAIL_FROM_ADDRESS: " . EMAIL_FROM_ADDRESS . "\n";
echo "EMAIL_FROM_NAME: " . EMAIL_FROM_NAME . "\n";
echo "</pre>";

// Check password length
if (strlen(EMAIL_SMTP_PASSWORD) !== 16) {
    echo "<div style='background: #fff3cd; border: 2px solid #ffc107; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>⚠️ Password Length Issue!</strong><br>";
    echo "Gmail App Passwords must be exactly 16 characters.<br>";
    echo "Current password length: <strong>" . strlen(EMAIL_SMTP_PASSWORD) . " characters</strong><br>";
    echo "Please generate a new Gmail App Password.";
    echo "</div>";
}

// Test SMTP connection
echo "<h3>2. Testing SMTP Authentication</h3>";
echo "<p>Attempting to connect to Gmail SMTP server...</p>";

try {
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    
    // Enable verbose debug output
    $mail->SMTPDebug = 2; // Enable detailed debug output
    $mail->Debugoutput = function($str, $level) {
        echo "<pre style='background: #f5f5f5; padding: 5px; font-size: 11px; margin: 2px 0; border-left: 3px solid #007bff;'>";
        echo htmlspecialchars($str);
        echo "</pre>";
    };
    
    // Server settings
    $mail->isSMTP();
    $mail->Host = EMAIL_SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = EMAIL_SMTP_USERNAME;
    $mail->Password = EMAIL_SMTP_PASSWORD;
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = EMAIL_SMTP_PORT;
    $mail->CharSet = 'UTF-8';
    $mail->Timeout = 10; // 10 second timeout
    
    // Test connection (don't send email, just test auth)
    echo "<div style='background: #d1ecf1; border: 2px solid #0c5460; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>📡 Connecting to SMTP server...</strong><br>";
    echo "</div>";
    
    // Try to connect and authenticate
    if (!$mail->smtpConnect()) {
        throw new Exception("SMTP connection failed");
    }
    
    // Try to authenticate
    if (!$mail->smtp->authenticate(EMAIL_SMTP_USERNAME, EMAIL_SMTP_PASSWORD)) {
        throw new Exception("SMTP authentication failed");
    }
    
    $mail->smtp->quit();
    
    echo "<div style='background: #d4edda; border: 2px solid #28a745; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>✅ SMTP Authentication Successful!</strong><br>";
    echo "Your Gmail App Password is working correctly.";
    echo "</div>";
    
} catch (\PHPMailer\PHPMailer\Exception $e) {
    $errorMsg = $e->getMessage();
    
    echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>❌ SMTP Authentication Failed!</strong><br>";
    echo "<strong>Error:</strong> " . htmlspecialchars($errorMsg) . "<br><br>";
    
    // Provide specific solutions based on error
    if (strpos($errorMsg, 'authentication failed') !== false || strpos($errorMsg, '535') !== false || strpos($errorMsg, 'Could not authenticate') !== false) {
        echo "<strong>🔧 Solution Steps:</strong><br>";
        echo "<ol>";
        echo "<li><strong>Verify 2-Step Verification is enabled:</strong><br>";
        echo "   Go to: <a href='https://myaccount.google.com/security' target='_blank'>https://myaccount.google.com/security</a><br>";
        echo "   Make sure 2-Step Verification is <strong>enabled</strong></li>";
        echo "<li><strong>Generate a new Gmail App Password:</strong><br>";
        echo "   - Go to: <a href='https://myaccount.google.com/apppasswords' target='_blank'>https://myaccount.google.com/apppasswords</a><br>";
        echo "   - Select 'Mail' as the app<br>";
        echo "   - Select 'Other (Custom name)' as device<br>";
        echo "   - Enter name: 'UphoCare System'<br>";
        echo "   - Click 'Generate'<br>";
        echo "   - Copy the 16-character password (remove spaces)</li>";
        echo "<li><strong>Update config/email.php:</strong><br>";
        echo "   Change <code>EMAIL_SMTP_PASSWORD</code> to the new 16-character App Password</li>";
        echo "<li><strong>Important Notes:</strong><br>";
        echo "   - ❌ Do NOT use your regular Gmail password<br>";
        echo "   - ✅ Use ONLY the 16-character App Password<br>";
        echo "   - ✅ Remove all spaces from the password<br>";
        echo "   - ✅ Password must be exactly 16 characters</li>";
        echo "</ol>";
    } else if (strpos($errorMsg, 'SMTP connect() failed') !== false) {
        echo "<strong>🔧 Solution:</strong><br>";
        echo "Check your internet connection and firewall settings.<br>";
        echo "Gmail SMTP server might be blocked.";
    } else {
        echo "<strong>🔧 Solution:</strong><br>";
        echo "Check the error message above and verify your email configuration.";
    }
    
    echo "</div>";
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
    echo "<strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage());
    echo "</div>";
}

echo "<hr>";
echo "<h3>3. Common Issues & Solutions</h3>";
echo "<div style='background: #fff3cd; border: 2px solid #ffc107; padding: 15px; border-radius: 5px; margin: 10px 0;'>";
echo "<strong>⚠️ If authentication is still failing:</strong><br>";
echo "<ul>";
echo "<li>Make sure 2-Step Verification is <strong>enabled</strong> on your Google Account</li>";
echo "<li>Generate a <strong>new</strong> App Password (old ones might be revoked)</li>";
echo "<li>Copy the password <strong>exactly</strong> as shown (remove spaces)</li>";
echo "<li>Make sure the password is exactly <strong>16 characters</strong> (no more, no less)</li>";
echo "<li>Wait a few minutes after generating the App Password before testing</li>";
echo "<li>Check if your Google Account has any security restrictions</li>";
echo "</ul>";
echo "</div>";

echo "<hr>";
echo "<p><strong>Next Steps:</strong></p>";
echo "<ol>";
echo "<li>If authentication failed, follow the solution steps above</li>";
echo "<li>Update <code>config/email.php</code> with the correct App Password</li>";
echo "<li>Run this test again to verify</li>";
echo "<li>Once authentication succeeds, test sending an email</li>";
echo "</ol>";

