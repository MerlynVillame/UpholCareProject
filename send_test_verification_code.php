<?php
/**
 * Send Test Verification Code
 * This script sends a test verification code to the specified email address
 */

// Define constants first
define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);

// Load configuration
require_once ROOT . DS . 'config' . DS . 'config.php';
require_once ROOT . DS . 'config' . DS . 'email.php';
require_once ROOT . DS . 'core' . DS . 'NotificationService.php';

// Get email from query parameter or use default
$testEmail = $_GET['email'] ?? 'merlyn.lagrimas122021@gmail.com';
$testName = $_GET['name'] ?? 'Test Admin';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Send Test Verification Code - UphoCare</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #667eea;
            padding-bottom: 10px;
        }
        .success {
            background: #d4edda;
            border: 2px solid #28a745;
            color: #155724;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .error {
            background: #f8d7da;
            border: 2px solid #dc3545;
            color: #721c24;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        .info {
            background: #d1ecf1;
            border: 2px solid #0c5460;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
        pre {
            background: #f5f5f5;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
            border-left: 4px solid #667eea;
        }
        .code-box {
            background: #fff3cd;
            border: 2px dashed #ffc107;
            padding: 20px;
            text-align: center;
            border-radius: 5px;
            margin: 20px 0;
            font-size: 24px;
            font-weight: bold;
            color: #856404;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📧 Send Test Verification Code</h1>
        
        <?php
        // Generate a test verification code
        $verificationCode = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        
        echo "<div class='info'>";
        echo "<strong>Test Configuration:</strong><br>";
        echo "Email: <strong>" . htmlspecialchars($testEmail) . "</strong><br>";
        echo "Name: <strong>" . htmlspecialchars($testName) . "</strong><br>";
        echo "Verification Code: <strong>" . $verificationCode . "</strong><br>";
        echo "</div>";
        
        // Check PHPMailer
        $phpmailerAvailable = false;
        if (file_exists(ROOT . DS . 'vendor' . DS . 'autoload.php')) {
            require_once ROOT . DS . 'vendor' . DS . 'autoload.php';
            $phpmailerAvailable = class_exists('PHPMailer\PHPMailer\PHPMailer');
        }
        
        if (!$phpmailerAvailable) {
            echo "<div class='error'>";
            echo "<strong>❌ PHPMailer not found!</strong><br>";
            echo "Please install PHPMailer: <code>composer install</code>";
            echo "</div>";
            exit;
        }
        
        // Check email configuration
        echo "<h2>Email Configuration</h2>";
        echo "<pre>";
        echo "EMAIL_SMTP_HOST: " . EMAIL_SMTP_HOST . "\n";
        echo "EMAIL_SMTP_PORT: " . EMAIL_SMTP_PORT . "\n";
        echo "EMAIL_SMTP_USERNAME: " . EMAIL_SMTP_USERNAME . "\n";
        echo "EMAIL_SMTP_PASSWORD: " . str_repeat('*', strlen(EMAIL_SMTP_PASSWORD)) . " (length: " . strlen(EMAIL_SMTP_PASSWORD) . ")\n";
        echo "EMAIL_FROM_ADDRESS: " . EMAIL_FROM_ADDRESS . "\n";
        echo "EMAIL_FROM_NAME: " . EMAIL_FROM_NAME . "\n";
        echo "</pre>";
        
        // Test SMTP connection first
        echo "<h2>Testing SMTP Connection</h2>";
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
            $mail->Timeout = 10;
            
            // Test connection
            echo "<div class='info'>";
            echo "<strong>📡 Testing SMTP connection...</strong><br>";
            echo "</div>";
            
            if (!$mail->smtpConnect()) {
                throw new Exception("SMTP connection failed");
            }
            
            $mail->smtpClose();
            
            echo "<div class='success'>";
            echo "<strong>✅ SMTP connection successful!</strong><br>";
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "<strong>❌ SMTP Connection Failed!</strong><br>";
            echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
            echo "Please check your email configuration in <code>config/email.php</code>";
            echo "</div>";
            exit;
        }
        
        // Send verification code
        echo "<h2>Sending Verification Code</h2>";
        
        try {
            $notificationService = new NotificationService();
            
            echo "<div class='info'>";
            echo "<strong>📧 Sending verification code email...</strong><br>";
            echo "To: <strong>" . htmlspecialchars($testEmail) . "</strong><br>";
            echo "Code: <strong>" . $verificationCode . "</strong><br>";
            echo "</div>";
            
            $result = $notificationService->sendAdminVerificationCode(
                $testEmail,
                $testName,
                $verificationCode
            );
            
            if ($result) {
                echo "<div class='success'>";
                echo "<strong>✅ Email sent successfully!</strong><br><br>";
                echo "The verification code has been sent to: <strong>" . htmlspecialchars($testEmail) . "</strong><br>";
                echo "Please check your inbox (and spam folder) for the email.<br><br>";
                echo "<div class='code-box'>";
                echo "Your Verification Code: <strong>" . $verificationCode . "</strong>";
                echo "</div>";
                echo "<p><strong>Note:</strong> This code is displayed here for testing purposes. In production, the code is only sent via email.</p>";
                echo "</div>";
            } else {
                echo "<div class='error'>";
                echo "<strong>❌ Email sending failed!</strong><br><br>";
                echo "The email could not be sent. Please check:<br>";
                echo "<ul>";
                echo "<li>Email configuration in <code>config/email.php</code></li>";
                echo "<li>Gmail App Password is correct</li>";
                echo "<li>2-Step Verification is enabled</li>";
                echo "<li>Email logs at <code>logs/email_notifications.log</code></li>";
                echo "</ul>";
                echo "</div>";
            }
            
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "<strong>❌ Error sending email!</strong><br>";
            echo "Error: " . htmlspecialchars($e->getMessage()) . "<br>";
            echo "</div>";
        }
        
        // Show recent email logs
        echo "<h2>Recent Email Logs</h2>";
        $logFile = ROOT . DS . 'logs' . DS . 'email_notifications.log';
        if (file_exists($logFile)) {
            $logs = file($logFile);
            $recentLogs = array_slice($logs, -5); // Last 5 entries
            echo "<pre>";
            foreach ($recentLogs as $log) {
                echo htmlspecialchars($log);
            }
            echo "</pre>";
        } else {
            echo "<p>No email logs found yet.</p>";
        }
        ?>
        
        <hr>
        <h2>Usage</h2>
        <p>You can test with different email addresses by adding parameters to the URL:</p>
        <pre>?email=your-email@gmail.com&name=Your Name</pre>
        <p>Example:</p>
        <pre>send_test_verification_code.php?email=merlyn.lagrimas122021@gmail.com&name=Merlyn Lagrimas</pre>
    </div>
</body>
</html>

