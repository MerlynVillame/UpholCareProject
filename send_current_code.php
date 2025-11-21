<?php
/**
 * Send Current Verification Code
 * Sends the current verification code stored in database to customer
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

echo "=== Send Current Verification Code ===\n\n";
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
    
    // Get customer record
    echo "1. Getting customer record...\n";
    $stmt = $db->prepare("
        SELECT * FROM users
        WHERE email = ? AND role = 'customer'
    ");
    $stmt->execute([$customerEmail]);
    $customer = $stmt->fetch();
    
    if (!$customer) {
        echo "❌ Customer not found\n";
        exit(1);
    }
    
    echo "✅ Customer found: {$customer['fullname']}\n";
    echo "   Current Verification Code: " . ($customer['verification_code'] ?? 'NULL') . "\n";
    echo "   Status: " . ($customer['status'] ?? 'NULL') . "\n\n";
    
    // Get current code
    $currentCode = $customer['verification_code'] ?? null;
    
    if (!$currentCode) {
        echo "❌ No verification code found\n";
        echo "   Generating new code...\n";
        $currentCode = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        
        $updateStmt = $db->prepare("
            UPDATE users 
            SET verification_code = ?,
                verification_code_sent_at = NOW(),
                verification_attempts = 0,
                status = 'pending_verification'
            WHERE id = ?
        ");
        $updateStmt->execute([$currentCode, $customer['id']]);
        echo "✅ New code generated: {$currentCode}\n\n";
    } else {
        echo "2. Using current code: {$currentCode}\n";
        echo "   Updating sent_at timestamp...\n";
        
        $updateStmt = $db->prepare("
            UPDATE users 
            SET verification_code_sent_at = NOW(),
                status = 'pending_verification'
            WHERE id = ?
        ");
        $updateStmt->execute([$customer['id']]);
        echo "✅ Timestamp updated\n\n";
    }
    
    // Send verification code via email
    echo "3. Sending verification code email...\n";
    $notificationService = new NotificationService();
    
    $emailSent = $notificationService->sendAdminVerificationCode(
        $customerEmail,
        $customer['fullname'],
        $currentCode
    );
    
    if ($emailSent) {
        echo "✅ Email sent successfully!\n";
        echo "   Check inbox: {$customerEmail}\n";
        echo "   (Also check spam/junk folder)\n";
        echo "   Verification code: {$currentCode}\n";
    } else {
        echo "❌ Email sending FAILED\n";
        echo "   Check logs: logs/email_notifications.log\n";
        echo "   Verification code: {$currentCode}\n";
        echo "   (You can manually provide this code to the customer)\n";
    }
    
    echo "\n=== Complete ===\n";
    echo "\nCustomer should:\n";
    echo "1. Check email: {$customerEmail}\n";
    echo "2. Enter code: {$currentCode}\n";
    echo "3. Account will be activated\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

