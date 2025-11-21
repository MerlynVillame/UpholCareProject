<?php
/**
 * Fix Customer Verification
 * Adds missing columns and sends verification code
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

echo "=== Fix Customer Verification ===\n\n";
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
    
    // Step 1: Add missing columns
    echo "1. Adding missing columns...\n";
    
    $columns = [
        'verification_code' => "ALTER TABLE users ADD COLUMN verification_code VARCHAR(10) NULL COMMENT '4-digit verification code' AFTER status;",
        'verification_code_sent_at' => "ALTER TABLE users ADD COLUMN verification_code_sent_at TIMESTAMP NULL DEFAULT NULL COMMENT 'When verification code was sent' AFTER verification_code;",
        'verification_code_verified_at' => "ALTER TABLE users ADD COLUMN verification_code_verified_at TIMESTAMP NULL DEFAULT NULL COMMENT 'When verification code was verified' AFTER verification_code_sent_at;",
        'verification_attempts' => "ALTER TABLE users ADD COLUMN verification_attempts INT DEFAULT 0 COMMENT 'Number of verification attempts' AFTER verification_code_verified_at;"
    ];
    
    foreach ($columns as $colName => $sql) {
        // Check if column exists
        $stmt = $db->query("
            SELECT COUNT(*) as col_count 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'users' 
            AND COLUMN_NAME = '{$colName}'
        ");
        $result = $stmt->fetch();
        $exists = $result['col_count'] > 0;
        
        if (!$exists) {
            try {
                $db->exec($sql);
                echo "✅ Added column: {$colName}\n";
            } catch (PDOException $e) {
                if (strpos($e->getMessage(), 'Duplicate column') !== false) {
                    echo "⚠️  Column already exists: {$colName}\n";
                } else {
                    echo "❌ Error adding column {$colName}: " . $e->getMessage() . "\n";
                }
            }
        } else {
            echo "✅ Column already exists: {$colName}\n";
        }
    }
    
    echo "\n2. Checking customer account...\n";
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'customer'");
    $stmt->execute([$customerEmail]);
    $customer = $stmt->fetch();
    
    if (!$customer) {
        echo "❌ Customer not found: {$customerEmail}\n";
        exit(1);
    }
    
    echo "✅ Customer found: {$customer['fullname']}\n";
    echo "   Status: {$customer['status']}\n";
    echo "   ID: {$customer['id']}\n\n";
    
    // Step 3: Generate and store verification code
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
    
    // Step 4: Send verification code via email
    echo "5. Sending verification code email...\n";
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
    echo "\nNext steps:\n";
    echo "1. Customer should check email: {$customerEmail}\n";
    echo "2. Customer should enter code: {$verificationCode}\n";
    echo "3. Customer will be auto-logged in after verification\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

