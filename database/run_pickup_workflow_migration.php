<?php
/**
 * Update bookings table status ENUM to include PICKUP workflow statuses
 * Run this script once to update the database schema
 */

// Database configuration
$host = 'localhost';
$dbname = 'db_upholcare';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Updating bookings table status ENUM for PICKUP workflow...\n\n";
    
    // Update the ENUM to include all required statuses
    $sql = "ALTER TABLE `bookings` 
            MODIFY COLUMN `status` ENUM(
                'pending',
                'for_pickup',
                'picked_up',
                'for_inspection',
                'for_quotation',
                'approved',
                'in_queue',
                'in_progress',
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
                'rejected',
                'declined',
                'admin_review'
            ) DEFAULT 'pending'";
    
    $db->exec($sql);
    
    echo "✓ Status ENUM updated successfully!\n\n";
    
    // Verify the change
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columns as $column) {
        if ($column['Field'] === 'status') {
            echo "Current status column definition:\n";
            echo "Type: " . $column['Type'] . "\n";
            echo "Default: " . $column['Default'] . "\n\n";
            break;
        }
    }
    
    // Show current status distribution
    echo "Current bookings status distribution:\n";
    $stmt = $db->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status ORDER BY count DESC");
    $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($statuses)) {
        echo "No bookings found.\n";
    } else {
        foreach ($statuses as $status) {
            echo "  " . ($status['status'] ?: '(null)') . ": " . $status['count'] . "\n";
        }
    }
    
    echo "\n✓ Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

