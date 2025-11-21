<?php
/**
 * Fix Customer Status
 * Updates customer status to pending_verification if they need verification
 */

// Define constants
define('ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);

// Load configuration
require_once ROOT . DS . 'config' . DS . 'config.php';
require_once ROOT . DS . 'config' . DS . 'database.php';

// Customer email
$customerEmail = 'merlyn.villame@bisu.edu.ph';

echo "=== Fix Customer Status ===\n\n";
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
    
    // Check customer
    echo "1. Checking customer account...\n";
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'customer'");
    $stmt->execute([$customerEmail]);
    $customer = $stmt->fetch();
    
    if (!$customer) {
        echo "❌ Customer not found: {$customerEmail}\n";
        exit(1);
    }
    
    echo "✅ Customer found: {$customer['fullname']}\n";
    echo "   Current Status: " . ($customer['status'] ?? 'NULL') . "\n";
    echo "   Verification Code: " . ($customer['verification_code'] ?? 'NULL') . "\n";
    echo "   Code Sent At: " . ($customer['verification_code_sent_at'] ?? 'NULL') . "\n";
    echo "   Code Verified At: " . ($customer['verification_code_verified_at'] ?? 'NULL') . "\n";
    echo "   ID: {$customer['id']}\n\n";
    
    // Check if already verified
    if ($customer['verification_code_verified_at']) {
        echo "⚠️  Customer already verified at: {$customer['verification_code_verified_at']}\n";
        echo "   Status should be 'active'\n";
        
        if ($customer['status'] !== 'active') {
            echo "   Updating status to 'active'...\n";
            $updateStmt = $db->prepare("UPDATE users SET status = 'active' WHERE id = ?");
            $updateStmt->execute([$customer['id']]);
            echo "✅ Status updated to 'active'\n";
        }
    } else {
        // Not verified - update status to pending_verification
        echo "2. Customer not verified yet\n";
        echo "   Updating status to 'pending_verification'...\n";
        
        $updateStmt = $db->prepare("
            UPDATE users 
            SET status = 'pending_verification'
            WHERE id = ?
        ");
        $updateStmt->execute([$customer['id']]);
        echo "✅ Status updated to 'pending_verification'\n";
        
        // If no verification code exists, generate one
        if (empty($customer['verification_code'])) {
            echo "\n3. No verification code found\n";
            echo "   Generating new verification code...\n";
            
            $verificationCode = str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT);
            echo "   Code: {$verificationCode}\n";
            
            $updateCodeStmt = $db->prepare("
                UPDATE users 
                SET verification_code = ?,
                    verification_code_sent_at = NOW(),
                    verification_attempts = 0
                WHERE id = ?
            ");
            $updateCodeStmt->execute([$verificationCode, $customer['id']]);
            echo "✅ Verification code generated and stored\n";
        }
    }
    
    echo "\n=== Complete ===\n";
    echo "\nCustomer can now:\n";
    echo "1. Visit: auth/verifyCode?email=" . urlencode($customerEmail) . "&role=customer\n";
    echo "2. Click 'Resend Verification Code' if needed\n";
    echo "3. Enter verification code to activate account\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

