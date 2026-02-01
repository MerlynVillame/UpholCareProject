<?php
/**
 * Add 'inspect_completed' status to bookings table
 * Run this script once to update the database schema
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Adding 'inspect_completed' status to bookings table...\n\n";
    
    // Get current status ENUM
    $stmt = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'");
    $statusColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Current status ENUM: " . $statusColumn['Type'] . "\n\n";
    
    // Check if inspect_completed already exists
    if (strpos($statusColumn['Type'], 'inspect_completed') !== false) {
        echo "✓ 'inspect_completed' status already exists in ENUM.\n";
        exit(0);
    }
    
    // Add inspect_completed status to ENUM
    echo "Adding 'inspect_completed' status...\n";
    $sql = "ALTER TABLE `bookings` 
            MODIFY COLUMN `status` ENUM(
                'pending', 
                'for_pickup', 
                'picked_up', 
                'for_inspection', 
                'to_inspect',
                'inspect_completed',
                'preview_receipt_sent',
                'under_repair', 
                'for_quality_check', 
                'ready_for_pickup',
                'out_for_delivery', 
                'completed', 
                'paid', 
                'closed', 
                'cancelled', 
                'confirmed', 
                'accepted', 
                'declined', 
                'admin_review',
                'repair_completed',
                'repair_completed_ready_to_deliver',
                'delivered_and_paid'
            ) DEFAULT 'pending'";
    
    $db->exec($sql);
    echo "✓ Status ENUM updated successfully!\n\n";
    
    // Verify the change
    $stmt = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'");
    $statusColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "New status ENUM: " . $statusColumn['Type'] . "\n\n";
    
    echo "✓ Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

