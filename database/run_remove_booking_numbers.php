<?php
/**
 * Remove Booking Numbers Migration
 * 
 * This script removes all booking number related columns and tables
 * from the database since the system now uses availability based on
 * fabric/color stock and store capacity instead of booking numbers.
 * 
 * Run this script once to clean up the database.
 */

// Set up path constants
define('ROOT', dirname(__DIR__));
define('DS', DIRECTORY_SEPARATOR);

// Load database configuration
require_once ROOT . DS . 'config' . DS . 'database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting booking number removal migration...\n\n";
    
    // Step 1: Drop foreign key constraints
    echo "Step 1: Dropping foreign key constraints...\n";
    $stmt = $db->query("
        SELECT CONSTRAINT_NAME 
        FROM information_schema.KEY_COLUMN_USAGE 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'bookings' 
        AND COLUMN_NAME = 'booking_number_id'
        AND REFERENCED_TABLE_NAME = 'booking_numbers'
        LIMIT 1
    ");
    $constraint = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($constraint && isset($constraint['CONSTRAINT_NAME'])) {
        $constraintName = $constraint['CONSTRAINT_NAME'];
        $db->exec("ALTER TABLE `bookings` DROP FOREIGN KEY `{$constraintName}`");
        echo "  ✓ Dropped foreign key: {$constraintName}\n";
    } else {
        echo "  ✓ No foreign key constraint found\n";
    }
    
    // Step 2: Drop indexes
    echo "\nStep 2: Dropping indexes...\n";
    $indexes = ['booking_number_id', 'idx_booking_number_id', 'customer_booking_number_id'];
    foreach ($indexes as $index) {
        try {
            $db->exec("ALTER TABLE `bookings` DROP INDEX `{$index}`");
            echo "  ✓ Dropped index: {$index}\n";
        } catch (PDOException $e) {
            if (strpos($e->getMessage(), "Unknown key") === false) {
                echo "  - Index {$index} does not exist (skipped)\n";
            }
        }
    }
    
    // Step 3: Remove columns from bookings table
    echo "\nStep 3: Removing columns from bookings table...\n";
    
    // Check if booking_number_id exists
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'bookings' 
        AND COLUMN_NAME = 'booking_number_id'
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        $db->exec("ALTER TABLE `bookings` DROP COLUMN `booking_number_id`");
        echo "  ✓ Removed column: booking_number_id\n";
    } else {
        echo "  - Column booking_number_id does not exist (skipped)\n";
    }
    
    // Check if customer_booking_number_id exists
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'bookings' 
        AND COLUMN_NAME = 'customer_booking_number_id'
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        $db->exec("ALTER TABLE `bookings` DROP COLUMN `customer_booking_number_id`");
        echo "  ✓ Removed column: customer_booking_number_id\n";
    } else {
        echo "  - Column customer_booking_number_id does not exist (skipped)\n";
    }
    
    // Step 4: Check if repair_items uses customer_booking_number_id
    echo "\nStep 4: Checking repair_items table...\n";
    $stmt = $db->query("
        SELECT COUNT(*) as count 
        FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'repair_items' 
        AND COLUMN_NAME = 'customer_booking_number_id'
    ");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($result['count'] > 0) {
        echo "  ℹ repair_items still uses customer_booking_number_id - keeping customer_booking_numbers table\n";
        $dropCustomerBookingNumbers = false;
    } else {
        echo "  ✓ repair_items does not use customer_booking_number_id\n";
        $dropCustomerBookingNumbers = true;
    }
    
    // Step 5: Drop booking_numbers table
    echo "\nStep 5: Dropping booking_numbers table...\n";
    try {
        $db->exec("DROP TABLE IF EXISTS `booking_numbers`");
        echo "  ✓ Dropped table: booking_numbers\n";
    } catch (PDOException $e) {
        echo "  - Error dropping booking_numbers table: " . $e->getMessage() . "\n";
    }
    
    // Step 6: Drop customer_booking_numbers table (if not used by repair_items)
    if ($dropCustomerBookingNumbers) {
        echo "\nStep 6: Dropping customer_booking_numbers table...\n";
        try {
            $db->exec("DROP TABLE IF EXISTS `customer_booking_numbers`");
            echo "  ✓ Dropped table: customer_booking_numbers\n";
        } catch (PDOException $e) {
            echo "  - Error dropping customer_booking_numbers table: " . $e->getMessage() . "\n";
        }
    } else {
        echo "\nStep 6: Keeping customer_booking_numbers table (used by repair_items)\n";
    }
    
    // Step 7: Verify removal
    echo "\nStep 7: Verifying removal...\n";
    $stmt = $db->query("SHOW COLUMNS FROM `bookings` LIKE '%booking%'");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($columns)) {
        echo "  ✓ No booking-related columns found in bookings table\n";
    } else {
        echo "  ⚠ Remaining booking-related columns:\n";
        foreach ($columns as $column) {
            echo "    - " . $column['Field'] . "\n";
        }
    }
    
    echo "\n✅ Migration completed successfully!\n";
    echo "\nThe system now uses availability based on:\n";
    echo "  - Fabric/color stock (quantity > 0)\n";
    echo "  - Store availability (color assigned to store)\n";
    echo "  - Store capacity (max 50 active bookings per store)\n";
    
} catch (Exception $e) {
    echo "\n❌ Error during migration: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

