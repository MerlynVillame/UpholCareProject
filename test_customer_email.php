<?php
/**
 * Test Customer Email Sending
 * Tests sending verification code to customer email
 */

// Define constants
define('ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);

// Load configuration
require_once ROOT . DS . 'config' . DS . 'config.php';
require_once ROOT . DS . 'core' . DS . 'Database.php';
require_once ROOT . DS . 'core' . DS . 'NotificationService.php';

// Test email
$testEmail = 'merlyn.villame@bisu.edu.ph';
$testName = 'Merlyn Villame';
$testCode = '1234';

echo "=== Customer Email Test ===\n\n";
echo "Testing email to: {$testEmail}\n";
echo "Name: {$testName}\n";
echo "Code: {$testCode}\n\n";

// Check database columns
echo "1. Checking database columns...\n";
try {
    $db = Database::getInstance()->getConnection();
    
    // Check if verification_code column exists
    $stmt = $db->query("
        SELECT COUNT(*) as col_count 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'users' 
        AND COLUMN_NAME = 'verification_code'
    ");
    $result = $stmt->fetch();
    $columnExists = $result['col_count'] > 0;
    
    if ($columnExists) {
        echo "✅ verification_code column exists\n";
    } else {
        echo "❌ verification_code column MISSING\n";
        echo "   Run: database/add_customer_verification_columns.sql\n";
    }
    
    // Check other columns
    $columns = ['verification_code_sent_at', 'verification_code_verified_at', 'verification_attempts'];
    foreach ($columns as $col) {
        $stmt = $db->query("
            SELECT COUNT(*) as col_count 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'users' 
            AND COLUMN_NAME = '{$col}'
        ");
        $result = $stmt->fetch();
        $exists = $result['col_count'] > 0;
        echo ($exists ? "✅" : "❌") . " {$col} " . ($exists ? "exists" : "MISSING") . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ Database error: " . $e->getMessage() . "\n";
}

echo "\n2. Checking email configuration...\n";
require_once ROOT . DS . 'config' . DS . 'email.php';

echo "EMAIL_SMTP_USERNAME: " . EMAIL_SMTP_USERNAME . "\n";
echo "EMAIL_SMTP_PASSWORD length: " . strlen(EMAIL_SMTP_PASSWORD) . " characters\n";
echo "EMAIL_SMTP_HOST: " . EMAIL_SMTP_HOST . "\n";
echo "EMAIL_SMTP_PORT: " . EMAIL_SMTP_PORT . "\n";

if (strlen(EMAIL_SMTP_PASSWORD) !== 16) {
    echo "❌ EMAIL_SMTP_PASSWORD must be 16 characters (Gmail App Password)\n";
} else {
    echo "✅ EMAIL_SMTP_PASSWORD length is correct\n";
}

echo "\n3. Testing email sending...\n";
try {
    $notificationService = new NotificationService();
    
    echo "Sending verification code email...\n";
    $result = $notificationService->sendAdminVerificationCode(
        $testEmail,
        $testName,
        $testCode
    );
    
    if ($result) {
        echo "✅ Email sent successfully!\n";
        echo "   Check inbox: {$testEmail}\n";
        echo "   (Also check spam/junk folder)\n";
    } else {
        echo "❌ Email sending FAILED\n";
        echo "   Check logs: logs/email_notifications.log\n";
        echo "   Check PHP error logs\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
}

echo "\n4. Checking email logs...\n";
$logFile = ROOT . DS . 'logs' . DS . 'email_notifications.log';
if (file_exists($logFile)) {
    $logs = file($logFile);
    $lastLog = end($logs);
    echo "Last log entry:\n";
    echo $lastLog . "\n";
} else {
    echo "❌ Log file not found: {$logFile}\n";
}

echo "\n=== Test Complete ===\n";

