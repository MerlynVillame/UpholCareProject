<?php
/**
 * Check db_upholcare database structure
 */

// Try connecting to db_upholcare directly
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    // Connect without specifying database first
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== Checking db_upholcare database ===\n\n";
    
    // Check if database exists
    $stmt = $pdo->query("SHOW DATABASES LIKE 'db_upholcare'");
    $dbExists = $stmt->rowCount() > 0;
    echo "Database 'db_upholcare' exists: " . ($dbExists ? 'YES' : 'NO') . "\n\n";
    
    if ($dbExists) {
        // Switch to db_upholcare
        $pdo->exec("USE db_upholcare");
        
        // Check if bookings table exists
        $stmt = $pdo->query("SHOW TABLES LIKE 'bookings'");
        $tableExists = $stmt->rowCount() > 0;
        echo "Bookings table exists in db_upholcare: " . ($tableExists ? 'YES' : 'NO') . "\n\n";
        
        if ($tableExists) {
            // Get table structure
            $stmt = $pdo->query("DESCRIBE bookings");
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo "Columns in bookings table (db_upholcare):\n";
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
            $stmt = $pdo->query("SHOW KEYS FROM bookings WHERE Key_name = 'PRIMARY'");
            $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo "\nPrimary key information:\n";
            foreach ($keys as $key) {
                echo "  Primary key column: " . $key['Column_name'] . "\n";
            }
        }
    }
    
    // Also check upholcare_customers
    echo "\n\n=== Checking upholcare_customers database ===\n\n";
    $stmt = $pdo->query("SHOW DATABASES LIKE 'upholcare_customers'");
    $dbExists2 = $stmt->rowCount() > 0;
    echo "Database 'upholcare_customers' exists: " . ($dbExists2 ? 'YES' : 'NO') . "\n";
    
    if ($dbExists2) {
        $pdo->exec("USE upholcare_customers");
        $stmt = $pdo->query("SHOW KEYS FROM bookings WHERE Key_name = 'PRIMARY'");
        $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo "Primary key in upholcare_customers.bookings:\n";
        foreach ($keys as $key) {
            echo "  Primary key column: " . $key['Column_name'] . "\n";
        }
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

