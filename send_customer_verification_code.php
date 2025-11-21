<?php
/**
 * Send Customer Verification Code
 * Manually send verification code to customer email
 */

// Define constants
define('ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);

// Load configuration
require_once ROOT . DS . 'config' . DS . 'config.php';
require_once ROOT . DS . 'config' . DS . 'database.php';
require_once ROOT . DS . 'core' . DS . 'NotificationService.php';

// Customer email
$customerEmail = 'merlyn.villame@bisu.edu.ph';

echo "=== Send Customer Verification Code ===\n\n";
echo "Email: {$customerEmail}\n\n";

try {
    // Get database connection
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    // Check if customer exists
    echo "1. Checking customer account...\n";
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'customer'");
    $stmt->execute([$customerEmail]);
    $customer = $stmt->fetch();
    
    if (!$customer) {
        echo "❌ Customer not found: {$customerEmail}\n";
        echo "   Please register the customer first.\n";
        exit(1);
    }
    
    echo "✅ Customer found: {$customer['fullname']}\n";
    echo "   Status: {$customer['status']}\n";
    echo "   ID: {$customer['id']}\n\n";
    
    // Check if verification columns exist
    echo "2. Checking database columns...\n";
    $stmt = $db->query("
        SELECT COUNT(*) as col_count 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'users' 
        AND COLUMN_NAME = 'verification_code'
    ");
    $result = $stmt->fetch();
    $columnExists = $result['col_count'] > 0;
    
    if (!$columnExists) {
        echo "❌ verification_code column MISSING\n";
        echo "   Run: database/add_customer_verification_columns.sql\n";
        echo "   Or run this SQL:\n";
        echo "   ALTER TABLE users ADD COLUMN verification_code VARCHAR(10) NULL AFTER status;\n";
        echo "   ALTER TABLE users ADD COLUMN verification_code_sent_at TIMESTAMP NULL AFTER verification_code;\n";
        echo "   ALTER TABLE users ADD COLUMN verification_code_verified_at TIMESTAMP NULL AFTER verification_code_sent_at;\n";
        echo "   ALTER TABLE users ADD COLUMN verification_attempts INT DEFAULT 0 AFTER verification_code_verified_at;\n";
        exit(1);
    }
    
    echo "✅ verification_code column exists\n\n";
    
    // Generate verification code
    echo "3. Generating verification code...\n";
    $verificationCode = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
    echo "   Code: {$verificationCode}\n\n";
    
    // Update user with verification code
    echo "4. Updating customer record...\n";
    $updateStmt = $db->prepare("
        UPDATE users 
        SET verification_code = ?,
            verification_code_sent_at = NOW(),
            verification_attempts = 0,
            status = 'pending_verification'
        WHERE id = ?
    ");
    $updateStmt->execute([$verificationCode, $customer['id']]);
    echo "✅ Customer record updated\n\n";
    
    // Send verification code via email
    echo "5. Sending verification code email...\n";
    require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
    $notificationService = new NotificationService();
    
    $emailSent = $notificationService->sendAdminVerificationCode(
        $customerEmail,
        $customer['fullname'],
        $verificationCode
    );
    
    if ($emailSent) {
        echo "✅ Email sent successfully!\n";
        echo "   Check inbox: {$customerEmail}\n";
        echo "   (Also check spam/junk folder)\n";
        echo "   Verification code: {$verificationCode}\n";
    } else {
        echo "❌ Email sending FAILED\n";
        echo "   Check logs: logs/email_notifications.log\n";
        echo "   Check PHP error logs\n";
        echo "   Verification code: {$verificationCode}\n";
        echo "   (You can manually provide this code to the customer)\n";
    }
    
    echo "\n=== Complete ===\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

