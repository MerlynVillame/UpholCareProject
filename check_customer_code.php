<?php
/**
 * Check Customer Verification Code
 * Debug verification code issue
 */

// Define constants
define('ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);

// Load configuration
require_once ROOT . DS . 'config' . DS . 'config.php';
require_once ROOT . DS . 'config' . DS . 'database.php';

// Customer email
$customerEmail = 'merlyn.villame@bisu.edu.ph';

echo "=== Check Customer Verification Code ===\n\n";
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
    echo "1. Checking customer record...\n";
    $stmt = $db->prepare("
        SELECT 
            id,
            email,
            fullname,
            status,
            verification_code,
            verification_code_sent_at,
            verification_code_verified_at,
            verification_attempts,
            LENGTH(verification_code) as code_length,
            TRIM(verification_code) as code_trimmed,
            verification_code as code_raw
        FROM users
        WHERE email = ? AND role = 'customer'
    ");
    $stmt->execute([$customerEmail]);
    $customer = $stmt->fetch();
    
    if (!$customer) {
        echo "❌ Customer not found\n";
        exit(1);
    }
    
    echo "✅ Customer found:\n";
    echo "   ID: {$customer['id']}\n";
    echo "   Name: {$customer['fullname']}\n";
    echo "   Status: " . ($customer['status'] ?? 'NULL') . "\n";
    echo "   Verification Code (raw): '" . ($customer['code_raw'] ?? 'NULL') . "'\n";
    echo "   Verification Code (trimmed): '" . ($customer['code_trimmed'] ?? 'NULL') . "'\n";
    echo "   Code Length: " . ($customer['code_length'] ?? '0') . " characters\n";
    echo "   Code Sent At: " . ($customer['verification_code_sent_at'] ?? 'NULL') . "\n";
    echo "   Code Verified At: " . ($customer['verification_code_verified_at'] ?? 'NULL') . "\n";
    echo "   Verification Attempts: " . ($customer['verification_attempts'] ?? '0') . "\n\n";
    
    // Test code comparison
    $storedCode = $customer['verification_code'] ?? null;
    if ($storedCode) {
        echo "2. Testing code comparison...\n";
        $testCode = '9699'; // The code we sent
        
        echo "   Test Code: '{$testCode}'\n";
        echo "   Stored Code (raw): '{$storedCode}'\n";
        echo "   Stored Code (trimmed): '" . trim($storedCode) . "'\n";
        echo "   Stored Code (padded): '" . str_pad(trim($storedCode), 4, '0', STR_PAD_LEFT) . "'\n\n";
        
        // Test different comparison methods
        echo "   Comparison Results:\n";
        echo "   - Direct match: " . ($storedCode === $testCode ? '✅' : '❌') . "\n";
        echo "   - Trimmed match: " . (trim($storedCode) === trim($testCode) ? '✅' : '❌') . "\n";
        echo "   - Case-insensitive: " . (strtolower(trim($storedCode)) === strtolower(trim($testCode)) ? '✅' : '❌') . "\n";
        echo "   - Padded match: " . (str_pad(trim($storedCode), 4, '0', STR_PAD_LEFT) === str_pad(trim($testCode), 4, '0', STR_PAD_LEFT) ? '✅' : '❌') . "\n\n";
        
        // Show hex dump to check for hidden characters
        echo "3. Code analysis (hex dump):\n";
        echo "   Test Code: ";
        for ($i = 0; $i < strlen($testCode); $i++) {
            echo sprintf('%02X ', ord($testCode[$i]));
        }
        echo "\n";
        echo "   Stored Code: ";
        for ($i = 0; $i < strlen($storedCode); $i++) {
            echo sprintf('%02X ', ord($storedCode[$i]));
        }
        echo "\n\n";
    } else {
        echo "❌ No verification code found in database\n";
    }
    
    // Check if code has expired
    if ($customer['verification_code_sent_at']) {
        $codeSentAt = strtotime($customer['verification_code_sent_at']);
        $now = time();
        $hoursSinceSent = ($now - $codeSentAt) / 3600;
        
        echo "4. Code expiration check:\n";
        echo "   Code sent: " . date('Y-m-d H:i:s', $codeSentAt) . "\n";
        echo "   Current time: " . date('Y-m-d H:i:s', $now) . "\n";
        echo "   Hours since sent: " . round($hoursSinceSent, 2) . "\n";
        echo "   Expired (>24 hours): " . ($hoursSinceSent > 24 ? '❌ YES' : '✅ NO') . "\n\n";
    }
    
    echo "=== Summary ===\n";
    echo "Stored Code: '{$storedCode}'\n";
    echo "Expected Code: '9699'\n";
    echo "Match: " . (trim($storedCode) === '9699' ? '✅ YES' : '❌ NO') . "\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "   Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

