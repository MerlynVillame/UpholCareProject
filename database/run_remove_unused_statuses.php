<?php
/**
 * Remove unused statuses from bookings table
 * Removes: for_quotation, approved, in_queue, in_progress, start_repair, rejected
 * Run this script once to update the database schema
 */

// Database configuration
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Removing unused statuses from bookings table...\n\n";
    
    // First, check if any bookings have these statuses
    echo "Checking for bookings with statuses to be removed...\n";
    $checkStmt = $db->query("
        SELECT status, COUNT(*) as count 
        FROM bookings 
        WHERE status IN ('for_quotation', 'approved', 'in_queue', 'in_progress', 'start_repair', 'rejected')
        GROUP BY status
    ");
    $bookingsToMigrate = $checkStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($bookingsToMigrate)) {
        echo "Found bookings with statuses to migrate:\n";
        foreach ($bookingsToMigrate as $row) {
            echo "  - {$row['status']}: {$row['count']} booking(s)\n";
        }
        echo "\n";
        
        // Migrate existing bookings
        echo "Migrating bookings to appropriate statuses...\n";
        $db->exec("UPDATE bookings SET status = 'to_inspect' WHERE status = 'for_quotation'");
        $db->exec("UPDATE bookings SET status = 'pending' WHERE status = 'approved'");
        $db->exec("UPDATE bookings SET status = 'pending' WHERE status = 'in_queue'");
        $db->exec("UPDATE bookings SET status = 'under_repair' WHERE status = 'in_progress'");
        $db->exec("UPDATE bookings SET status = 'under_repair' WHERE status = 'start_repair'");
        $db->exec("UPDATE bookings SET status = 'cancelled' WHERE status = 'rejected'");
        echo "✓ Bookings migrated successfully!\n\n";
    } else {
        echo "✓ No bookings found with statuses to migrate.\n\n";
    }
    
    // Get current status ENUM
    $stmt = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'");
    $statusColumn = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Current status ENUM: " . $statusColumn['Type'] . "\n\n";
    
    // Remove statuses from ENUM
    echo "Removing statuses from ENUM...\n";
    $sql = "ALTER TABLE `bookings` 
            MODIFY COLUMN `status` ENUM(
                'pending', 
                'for_pickup', 
                'picked_up', 
                'for_inspection', 
                'to_inspect',
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
    
    // Verify no bookings have the removed statuses
    $verifyStmt = $db->query("
        SELECT status, COUNT(*) as count 
        FROM bookings 
        WHERE status IN ('for_quotation', 'approved', 'in_queue', 'in_progress', 'start_repair', 'rejected')
        GROUP BY status
    ");
    $remaining = $verifyStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($remaining)) {
        echo "✓ Verification passed: No bookings have removed statuses.\n";
    } else {
        echo "⚠ Warning: Some bookings still have removed statuses:\n";
        foreach ($remaining as $row) {
            echo "  - {$row['status']}: {$row['count']} booking(s)\n";
        }
    }
    
    echo "\n✓ Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

