<?php
/**
 * Update payment_status ENUM to support new payment types
 * Remove 'partial' and add 'paid_full_cash' and 'paid_on_delivery_cod'
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting payment_status update...\n";
    
    // Step 1: Update any existing 'partial' values to 'unpaid'
    $stmt = $db->prepare("UPDATE bookings SET payment_status = 'unpaid' WHERE payment_status = 'partial'");
    $stmt->execute();
    $affected = $stmt->rowCount();
    echo "Updated {$affected} records from 'partial' to 'unpaid'\n";
    
    // Step 2: Modify the ENUM to include new values
    // First, let's check current structure
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        if ($col['Field'] === 'payment_status') {
            echo "Current payment_status type: " . $col['Type'] . "\n";
            break;
        }
    }
    
    // Update the ENUM
    $sql = "ALTER TABLE bookings 
            MODIFY COLUMN payment_status ENUM('unpaid', 'paid', 'paid_full_cash', 'paid_on_delivery_cod', 'refunded', 'failed', 'cancelled') 
            DEFAULT 'unpaid'";
    
    $db->exec($sql);
    echo "Updated payment_status ENUM successfully\n";
    
    // Step 3: Update any old 'paid' values to 'paid_full_cash' for consistency
    $stmt = $db->prepare("UPDATE bookings SET payment_status = 'paid_full_cash' WHERE payment_status = 'paid'");
    $stmt->execute();
    $affected = $stmt->rowCount();
    echo "Updated {$affected} records from 'paid' to 'paid_full_cash'\n";
    
    // Verify the update
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        if ($col['Field'] === 'payment_status') {
            echo "\nFinal payment_status type: " . $col['Type'] . "\n";
            break;
        }
    }
    
    echo "\nDatabase updated successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

