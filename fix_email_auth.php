<?php
/**
 * Email Authentication Fix Script
 * This script helps diagnose and fix Gmail SMTP authentication issues
 */

// Define constants first
define('ROOT', dirname(__FILE__));
define('DS', DIRECTORY_SEPARATOR);

// Load configuration
require_once ROOT . DS . 'config' . DS . 'config.php';
require_once ROOT . DS . 'config' . DS . 'email.php';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Fix Email Authentication - UphoCare</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 900px;
            margin: 20px auto;
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
        h2 {
            color: #667eea;
            margin-top: 30px;
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
        .warning {
            background: #fff3cd;
            border: 2px solid #ffc107;
            color: #856404;
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
        .step {
            background: #f8f9fa;
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid #667eea;
            border-radius: 5px;
        }
        .step-number {
            display: inline-block;
            background: #667eea;
            color: white;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            text-align: center;
            line-height: 30px;
            font-weight: bold;
            margin-right: 10px;
        }
        a {
            color: #667eea;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Email Authentication Fix</h1>
        
        <?php
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
        
        // Display current configuration
        echo "<h2>1. Current Configuration</h2>";
        echo "<pre>";
        echo "EMAIL_SMTP_HOST: " . EMAIL_SMTP_HOST . "\n";
        echo "EMAIL_SMTP_PORT: " . EMAIL_SMTP_PORT . "\n";
        echo "EMAIL_SMTP_USERNAME: " . EMAIL_SMTP_USERNAME . "\n";
        $passwordLength = strlen(EMAIL_SMTP_PASSWORD);
        $passwordDisplay = str_repeat('*', $passwordLength);
        echo "EMAIL_SMTP_PASSWORD: " . $passwordDisplay . " (length: " . $passwordLength . " characters)\n";
        echo "EMAIL_FROM_ADDRESS: " . EMAIL_FROM_ADDRESS . "\n";
        echo "EMAIL_FROM_NAME: " . EMAIL_FROM_NAME . "\n";
        echo "</pre>";
        
        // Validate configuration
        $issues = [];
        
        if (EMAIL_SMTP_USERNAME === 'your-email@gmail.com') {
            $issues[] = "EMAIL_SMTP_USERNAME is not configured (still using placeholder)";
        }
        
        if (EMAIL_SMTP_PASSWORD === 'your-app-password') {
            $issues[] = "EMAIL_SMTP_PASSWORD is not configured (still using placeholder)";
        }
        
        if ($passwordLength !== 16) {
            $issues[] = "EMAIL_SMTP_PASSWORD is " . $passwordLength . " characters (must be exactly 16 for Gmail App Password)";
        }
        
        if (preg_match('/\s/', EMAIL_SMTP_PASSWORD)) {
            $issues[] = "EMAIL_SMTP_PASSWORD contains spaces (remove all spaces)";
        }
        
        if (!empty($issues)) {
            echo "<div class='warning'>";
            echo "<strong>⚠️ Configuration Issues Found:</strong><br>";
            echo "<ul>";
            foreach ($issues as $issue) {
                echo "<li>" . htmlspecialchars($issue) . "</li>";
            }
            echo "</ul>";
            echo "</div>";
        } else {
            echo "<div class='info'>";
            echo "<strong>✅ Configuration format looks correct</strong><br>";
            echo "Password length is 16 characters and contains no spaces.";
            echo "</div>";
        }
        
        // Test SMTP authentication
        echo "<h2>2. Testing SMTP Authentication</h2>";
        
        try {
            $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
            
            // Server settings
            $mail->isSMTP();
            $mail->Host = EMAIL_SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = EMAIL_SMTP_USERNAME;
            $mail->Password = EMAIL_SMTP_PASSWORD;
            $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = EMAIL_SMTP_PORT;
            $mail->CharSet = 'UTF-8';
            $mail->Timeout = 10;
            
            // Enable debug output
            $debugOutput = [];
            $mail->SMTPDebug = 2;
            $mail->Debugoutput = function($str, $level) use (&$debugOutput) {
                $debugOutput[] = $str;
            };
            
            echo "<div class='info'>";
            echo "<strong>📡 Testing SMTP connection and authentication...</strong><br>";
            echo "</div>";
            
            // Use smtpConnect() which is a public method
            // This will connect to the SMTP server and authenticate
            // If authentication fails, it will throw an exception
            $connected = $mail->smtpConnect();
            
            if (!$connected) {
                throw new Exception("SMTP connection failed - Could not connect to " . EMAIL_SMTP_HOST . ":" . EMAIL_SMTP_PORT);
            }
            
            // If we get here, connection and authentication were successful
            // Close the connection properly
            $mail->smtpClose();
            
            echo "<div class='success'>";
            echo "<strong>✅ SMTP Authentication Successful!</strong><br>";
            echo "Your Gmail App Password is working correctly. Emails should now send successfully.";
            echo "</div>";
            
            // Show debug output
            if (!empty($debugOutput)) {
                echo "<h3>Debug Output:</h3>";
                echo "<pre>";
                foreach ($debugOutput as $line) {
                    echo htmlspecialchars($line) . "\n";
                }
                echo "</pre>";
            }
            
        } catch (\PHPMailer\PHPMailer\Exception $e) {
            $errorMsg = $e->getMessage();
            
            echo "<div class='error'>";
            echo "<strong>❌ SMTP Authentication Failed!</strong><br>";
            echo "<strong>Error:</strong> " . htmlspecialchars($errorMsg) . "<br><br>";
            echo "</div>";
            
            // Show debug output
            if (!empty($debugOutput)) {
                echo "<h3>Debug Output:</h3>";
                echo "<pre>";
                foreach ($debugOutput as $line) {
                    echo htmlspecialchars($line) . "\n";
                }
                echo "</pre>";
            }
            
            // Provide solution
            echo "<div class='warning'>";
            echo "<strong>🔧 How to Fix Authentication Error:</strong><br><br>";
            
            echo "<div class='step'>";
            echo "<span class='step-number'>1</span>";
            echo "<strong>Enable 2-Step Verification</strong><br>";
            echo "Go to: <a href='https://myaccount.google.com/security' target='_blank'>https://myaccount.google.com/security</a><br>";
            echo "Make sure <strong>2-Step Verification</strong> is enabled (required for App Passwords)";
            echo "</div>";
            
            echo "<div class='step'>";
            echo "<span class='step-number'>2</span>";
            echo "<strong>Generate Gmail App Password</strong><br>";
            echo "Go to: <a href='https://myaccount.google.com/apppasswords' target='_blank'>https://myaccount.google.com/apppasswords</a><br>";
            echo "<ul>";
            echo "<li>Select <strong>'Mail'</strong> as the app</li>";
            echo "<li>Select <strong>'Other (Custom name)'</strong> as device</li>";
            echo "<li>Enter name: <strong>'UphoCare System'</strong></li>";
            echo "<li>Click <strong>'Generate'</strong></li>";
            echo "</ul>";
            echo "</div>";
            
            echo "<div class='step'>";
            echo "<span class='step-number'>3</span>";
            echo "<strong>Copy the App Password</strong><br>";
            echo "Google will show a password like: <code>abcd efgh ijkl mnop</code><br>";
            echo "<strong>Remove all spaces:</strong> <code>abcdefghijklmnop</code><br>";
            echo "Copy this exact 16-character password (no spaces)";
            echo "</div>";
            
            echo "<div class='step'>";
            echo "<span class='step-number'>4</span>";
            echo "<strong>Update config/email.php</strong><br>";
            echo "Open <code>config/email.php</code> and update line 30:<br>";
            echo "<pre>define('EMAIL_SMTP_PASSWORD', 'your-16-character-app-password');</pre>";
            echo "Replace <code>your-16-character-app-password</code> with the password from step 3";
            echo "</div>";
            
            echo "<div class='step'>";
            echo "<span class='step-number'>5</span>";
            echo "<strong>Test Again</strong><br>";
            echo "Refresh this page to test the authentication again";
            echo "</div>";
            
            echo "<br><strong>⚠️ Important Notes:</strong><br>";
            echo "<ul>";
            echo "<li>❌ Do NOT use your regular Gmail password</li>";
            echo "<li>✅ Use ONLY the 16-character App Password from Google</li>";
            echo "<li>✅ Remove all spaces from the password</li>";
            echo "<li>✅ Password must be exactly 16 characters</li>";
            echo "<li>✅ If you had an old App Password, generate a new one (old ones may be revoked)</li>";
            echo "</ul>";
            
            echo "</div>";
            
        } catch (Exception $e) {
            echo "<div class='error'>";
            echo "<strong>❌ Error:</strong> " . htmlspecialchars($e->getMessage());
            echo "</div>";
        }
        
        echo "<hr>";
        echo "<h2>3. Next Steps</h2>";
        echo "<ol>";
        echo "<li>If authentication failed, follow the steps above to generate a new Gmail App Password</li>";
        echo "<li>Update <code>config/email.php</code> with the new password</li>";
        echo "<li>Refresh this page to test again</li>";
        echo "<li>Once authentication succeeds, test sending an email using the registration form</li>";
        echo "</ol>";
        ?>
    </div>
</body>
</html>

