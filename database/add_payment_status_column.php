<?php
/**
 * Add payment_status column to bookings table if it doesn't exist
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Checking bookings table structure...\n";
    
    // Check if payment_status column exists
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasPaymentStatus = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] === 'payment_status') {
            $hasPaymentStatus = true;
            echo "payment_status column already exists. Type: " . $col['Type'] . "\n";
            break;
        }
    }
    
    if (!$hasPaymentStatus) {
        echo "Adding payment_status column...\n";
        // Add the column after total_amount if it exists, otherwise after booking_date
        $sql = "ALTER TABLE bookings 
                ADD COLUMN payment_status ENUM('unpaid', 'paid', 'paid_full_cash', 'paid_on_delivery_cod', 'refunded', 'failed', 'cancelled') 
                DEFAULT 'unpaid' 
                AFTER total_amount";
        
        // If total_amount doesn't exist, add after booking_date
        $hasTotalAmount = false;
        foreach ($columns as $col) {
            if ($col['Field'] === 'total_amount') {
                $hasTotalAmount = true;
                break;
            }
        }
        
        if (!$hasTotalAmount) {
            $sql = "ALTER TABLE bookings 
                    ADD COLUMN payment_status ENUM('unpaid', 'paid', 'paid_full_cash', 'paid_on_delivery_cod', 'refunded', 'failed', 'cancelled') 
                    DEFAULT 'unpaid' 
                    AFTER booking_date";
        }
        
        $db->exec($sql);
        echo "payment_status column added successfully\n";
    } else {
        // Column exists, update the ENUM
        echo "Updating payment_status ENUM...\n";
        
        // First, update any existing 'partial' values to 'unpaid'
        try {
            $stmt = $db->prepare("UPDATE bookings SET payment_status = 'unpaid' WHERE payment_status = 'partial'");
            $stmt->execute();
            $affected = $stmt->rowCount();
            echo "Updated {$affected} records from 'partial' to 'unpaid'\n";
        } catch (Exception $e) {
            echo "Note: Could not update partial values (may not exist): " . $e->getMessage() . "\n";
        }
        
        // Update the ENUM
        $sql = "ALTER TABLE bookings 
                MODIFY COLUMN payment_status ENUM('unpaid', 'paid', 'paid_full_cash', 'paid_on_delivery_cod', 'refunded', 'failed', 'cancelled') 
                DEFAULT 'unpaid'";
        
        $db->exec($sql);
        echo "Updated payment_status ENUM successfully\n";
        
        // Update any old 'paid' values to 'paid_full_cash' for consistency
        try {
            $stmt = $db->prepare("UPDATE bookings SET payment_status = 'paid_full_cash' WHERE payment_status = 'paid'");
            $stmt->execute();
            $affected = $stmt->rowCount();
            echo "Updated {$affected} records from 'paid' to 'paid_full_cash'\n";
        } catch (Exception $e) {
            echo "Note: Could not update paid values: " . $e->getMessage() . "\n";
        }
    }
    
    // Verify the update
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        if ($col['Field'] === 'payment_status') {
            echo "\nFinal payment_status type: " . $col['Type'] . "\n";
            break;
        }
    }
    
    echo "\nDatabase update completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

