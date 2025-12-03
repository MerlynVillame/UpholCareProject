<?php
/**
 * Test booking_id column and queries
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Testing bookings table structure...\n\n";
    
    // Get all columns
    $stmt = $db->query("SHOW COLUMNS FROM bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "All columns in bookings table:\n";
    foreach ($columns as $col) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
        if (stripos($col['Field'], 'id') !== false) {
            echo "    ^ This contains 'id'\n";
        }
    }
    
    // Test a simple SELECT query
    echo "\nTesting SELECT query with booking_id:\n";
    try {
        $testStmt = $db->prepare("SELECT `booking_id` FROM `bookings` LIMIT 1");
        $testStmt->execute();
        $result = $testStmt->fetch(PDO::FETCH_ASSOC);
        echo "  SUCCESS: SELECT works\n";
        if ($result) {
            echo "  Sample booking_id: " . ($result['booking_id'] ?? 'NULL') . "\n";
        }
    } catch (Exception $e) {
        echo "  ERROR: " . $e->getMessage() . "\n";
    }
    
    // Test UPDATE query structure
    echo "\nTesting UPDATE query structure:\n";
    $testBookingId = 16;
    $testStatus = 'ready_for_pickup';
    $testPaymentStatus = 'paid_full_cash';
    
    $updateQuery = "UPDATE `bookings` SET `status` = ?, `payment_status` = ?, `updated_at` = NOW() WHERE `booking_id` = ?";
    echo "  Query: " . $updateQuery . "\n";
    
    try {
        $testStmt = $db->prepare($updateQuery);
        echo "  SUCCESS: Query prepared\n";
        
        // Try to execute with test values (but don't commit)
        $db->beginTransaction();
        $result = $testStmt->execute([$testStatus, $testPaymentStatus, $testBookingId]);
        echo "  Execute result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
        $db->rollBack();
        echo "  Transaction rolled back (test only)\n";
    } catch (Exception $e) {
        echo "  ERROR: " . $e->getMessage() . "\n";
        echo "  Error code: " . $e->getCode() . "\n";
        if ($db->inTransaction()) {
            $db->rollBack();
        }
    }
    
    // Check if there's an 'id' column instead
    echo "\nChecking for 'id' column:\n";
    $hasId = false;
    foreach ($columns as $col) {
        if ($col['Field'] === 'id') {
            $hasId = true;
            echo "  Found 'id' column\n";
            break;
        }
    }
    
    if (!$hasId) {
        echo "  No 'id' column found\n";
    }
    
    // Show primary key
    echo "\nPrimary key information:\n";
    $stmt = $db->query("SHOW KEYS FROM bookings WHERE Key_name = 'PRIMARY'");
    $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($keys as $key) {
        echo "  Primary key column: " . $key['Column_name'] . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

