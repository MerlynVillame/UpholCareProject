<?php
/**
 * Add payment_status column to bookings table
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Adding payment_status column to bookings table...\n";
    
    // Add the column after updated_at
    $sql = "ALTER TABLE bookings 
            ADD COLUMN payment_status ENUM('unpaid', 'paid', 'paid_full_cash', 'paid_on_delivery_cod', 'refunded', 'failed', 'cancelled') 
            DEFAULT 'unpaid' 
            AFTER updated_at";
    
    $db->exec($sql);
    echo "payment_status column added successfully!\n";
    
    // Also add total_amount if it doesn't exist
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $hasTotalAmount = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] === 'total_amount') {
            $hasTotalAmount = true;
            break;
        }
    }
    
    if (!$hasTotalAmount) {
        echo "Adding total_amount column...\n";
        $sql = "ALTER TABLE bookings 
                ADD COLUMN total_amount DECIMAL(10,2) DEFAULT 0.00 
                AFTER quotation_id";
        $db->exec($sql);
        echo "total_amount column added successfully!\n";
    }
    
    // Verify the update
    echo "\nUpdated bookings table structure:\n";
    echo str_repeat("=", 80) . "\n";
    
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $col) {
        if (in_array($col['Field'], ['payment_status', 'total_amount'])) {
            echo sprintf("%-30s %-30s %-10s %s\n", 
                $col['Field'], 
                $col['Type'], 
                $col['Null'], 
                $col['Default'] ?? 'NULL'
            );
        }
    }
    
    echo "\nDatabase update completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
        echo "Note: Column may already exist. Checking current structure...\n";
        $stmt = $db->query("DESCRIBE bookings");
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($columns as $col) {
            if ($col['Field'] === 'payment_status') {
                echo "payment_status column exists. Type: " . $col['Type'] . "\n";
                // Update the ENUM
                try {
                    $sql = "ALTER TABLE bookings 
                            MODIFY COLUMN payment_status ENUM('unpaid', 'paid', 'paid_full_cash', 'paid_on_delivery_cod', 'refunded', 'failed', 'cancelled') 
                            DEFAULT 'unpaid'";
                    $db->exec($sql);
                    echo "Updated payment_status ENUM successfully!\n";
                } catch (Exception $e2) {
                    echo "Could not update ENUM: " . $e2->getMessage() . "\n";
                }
                break;
            }
        }
    } else {
        echo "Stack trace: " . $e->getTraceAsString() . "\n";
        exit(1);
    }
}

