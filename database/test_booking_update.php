<?php
/**
 * Test booking update query
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Testing booking update query...\n";
    
    // Test 1: Check if booking_id column exists
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nColumns in bookings table:\n";
    foreach ($columns as $col) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    
    // Test 2: Try a simple SELECT with booking_id
    echo "\nTesting SELECT with booking_id:\n";
    try {
        $stmt = $db->query("SELECT booking_id FROM bookings LIMIT 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "  SUCCESS: SELECT works with booking_id\n";
        if ($result) {
            echo "  Sample booking_id: " . $result['booking_id'] . "\n";
        }
    } catch (Exception $e) {
        echo "  ERROR: " . $e->getMessage() . "\n";
    }
    
    // Test 3: Try UPDATE with booking_id (dry run - no actual update)
    echo "\nTesting UPDATE query structure:\n";
    $testBookingId = 16;
    $testStatus = 'ready_for_pickup';
    $testPaymentStatus = 'paid_full_cash';
    
    $updateFields = ['status' => $testStatus, 'payment_status' => $testPaymentStatus];
    $updateQuery = "UPDATE bookings SET " . implode(', ', array_map(function($k, $v) {
        return "$k = ?";
    }, array_keys($updateFields), $updateFields)) . ", updated_at = NOW() WHERE booking_id = ?";
    
    echo "  Query: " . $updateQuery . "\n";
    
    // Test 4: Check if we can prepare the statement
    try {
        $stmt = $db->prepare($updateQuery);
        echo "  SUCCESS: Query prepared successfully\n";
    } catch (Exception $e) {
        echo "  ERROR preparing query: " . $e->getMessage() . "\n";
    }
    
    // Test 5: Check table name case sensitivity
    echo "\nTesting table name variations:\n";
    $tableNames = ['bookings', 'Bookings', 'BOOKINGS', '`bookings`'];
    foreach ($tableNames as $tableName) {
        try {
            $stmt = $db->query("SELECT COUNT(*) FROM $tableName");
            $count = $stmt->fetchColumn();
            echo "  SUCCESS with table name '$tableName': Found $count records\n";
        } catch (Exception $e) {
            echo "  ERROR with table name '$tableName': " . $e->getMessage() . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

