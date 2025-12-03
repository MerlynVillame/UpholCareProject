<?php
/**
 * Verify which database is actually being used and check table structure
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== Database Connection Verification ===\n\n";
    
    // Get current database name
    $stmt = $db->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Currently connected to database: " . ($result['db_name'] ?? 'NULL') . "\n";
    echo "Expected database: db_upholcare\n\n";
    
    // Check if bookings table exists
    echo "=== Checking bookings table ===\n";
    try {
        $stmt = $db->query("SHOW TABLES LIKE 'bookings'");
        $tableExists = $stmt->rowCount() > 0;
        echo "Bookings table exists: " . ($tableExists ? 'YES' : 'NO') . "\n";
        
        if ($tableExists) {
            // Get table structure
            $stmt = $db->query("DESCRIBE bookings");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "\nColumns in bookings table:\n";
            $hasBookingId = false;
            $hasId = false;
            foreach ($columns as $col) {
                echo "  - " . $col['Field'] . " (" . $col['Type'] . ")";
                if ($col['Key'] === 'PRI') {
                    echo " [PRIMARY KEY]";
                }
                echo "\n";
                
                if ($col['Field'] === 'booking_id') {
                    $hasBookingId = true;
                }
                if ($col['Field'] === 'id') {
                    $hasId = true;
                }
            }
            
            echo "\nPrimary key check:\n";
            if ($hasBookingId) {
                echo "  ✓ booking_id column exists\n";
            } else {
                echo "  ✗ booking_id column NOT FOUND\n";
            }
            
            if ($hasId) {
                echo "  ✓ id column exists\n";
            } else {
                echo "  ✗ id column NOT FOUND\n";
            }
            
            // Get primary key info
            $stmt = $db->query("SHOW KEYS FROM bookings WHERE Key_name = 'PRIMARY'");
            $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "\nPrimary key information:\n";
            foreach ($keys as $key) {
                echo "  Primary key column: " . $key['Column_name'] . "\n";
            }
            
            // Test a query
            echo "\n=== Testing queries ===\n";
            try {
                $testStmt = $db->prepare("SELECT booking_id FROM bookings LIMIT 1");
                $testStmt->execute();
                $testResult = $testStmt->fetch(PDO::FETCH_ASSOC);
                echo "✓ SELECT with booking_id: SUCCESS\n";
                if ($testResult) {
                    echo "  Sample booking_id: " . ($testResult['booking_id'] ?? 'NULL') . "\n";
                }
            } catch (Exception $e) {
                echo "✗ SELECT with booking_id: FAILED - " . $e->getMessage() . "\n";
            }
            
            // Test UPDATE query
            try {
                $testStmt = $db->prepare("UPDATE bookings SET updated_at = NOW() WHERE booking_id = ?");
                echo "✓ UPDATE query preparation with booking_id: SUCCESS\n";
            } catch (Exception $e) {
                echo "✗ UPDATE query preparation with booking_id: FAILED - " . $e->getMessage() . "\n";
            }
        }
    } catch (Exception $e) {
        echo "Error checking bookings table: " . $e->getMessage() . "\n";
    }
    
    // Check config values
    echo "\n=== Configuration Values ===\n";
    echo "DB_NAME constant: " . (defined('DB_NAME') ? DB_NAME : 'NOT DEFINED') . "\n";
    echo "DB_HOST constant: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "\n";
    echo "DB_USER constant: " . (defined('DB_USER') ? DB_USER : 'NOT DEFINED') . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

