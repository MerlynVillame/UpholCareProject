<?php
/**
 * Check if payment_status column exists in db_upholcare
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== Checking payment_status column in db_upholcare ===\n\n";
    
    // Get current database
    $stmt = $db->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Connected to database: " . ($result['db_name'] ?? 'NULL') . "\n\n";
    
    // Check all columns
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasPaymentStatus = false;
    echo "All columns in bookings table:\n";
    foreach ($columns as $col) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")";
        if ($col['Key'] === 'PRI') {
            echo " [PRIMARY KEY]";
        }
        echo "\n";
        
        if ($col['Field'] === 'payment_status') {
            $hasPaymentStatus = true;
        }
    }
    
    echo "\n";
    if ($hasPaymentStatus) {
        echo "✓ payment_status column EXISTS\n";
        
        // Check a sample booking's payment_status
        $stmt = $db->query("SELECT id, status, payment_status FROM bookings LIMIT 5");
        $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "\nSample bookings with payment_status:\n";
        foreach ($bookings as $booking) {
            echo "  ID: " . $booking['id'] . ", Status: " . ($booking['status'] ?? 'NULL') . ", Payment: " . ($booking['payment_status'] ?? 'NULL') . "\n";
        }
        
        // Test UPDATE query
        echo "\nTesting UPDATE query for payment_status:\n";
        try {
            $testStmt = $db->prepare("UPDATE `bookings` SET `payment_status` = ? WHERE `id` = ?");
            echo "  ✓ UPDATE query prepared successfully\n";
            
            // Test with a sample booking (don't actually update)
            $db->beginTransaction();
            $testResult = $testStmt->execute(['paid_full_cash', 16]);
            echo "  Execute result: " . ($testResult ? 'SUCCESS' : 'FAILED') . "\n";
            $db->rollBack();
            echo "  Transaction rolled back (test only)\n";
        } catch (Exception $e) {
            echo "  ✗ ERROR: " . $e->getMessage() . "\n";
            if ($db->inTransaction()) {
                $db->rollBack();
            }
        }
    } else {
        echo "✗ payment_status column DOES NOT EXIST\n";
        echo "\nNeed to add payment_status column to bookings table.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

