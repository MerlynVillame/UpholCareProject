<?php
/**
 * Update bookings table status ENUM to include all workflow statuses
 * Run this script once to fix the database schema
 */

// Database configuration
$host = 'localhost';
$dbname = 'db_upholcare';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Updating bookings table status ENUM...\n";
    
    // Update the ENUM to include all required statuses
    $sql = "ALTER TABLE `bookings` 
            MODIFY COLUMN `status` ENUM(
                'pending',
                'approved',
                'in_queue',
                'under_repair',
                'for_quality_check',
                'ready_for_pickup',
                'out_for_delivery',
                'completed',
                'cancelled',
                'confirmed',
                'in_progress',
                'accepted',
                'rejected',
                'declined'
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
            echo "  " . ($status['status'] ?? 'NULL') . ": " . $status['count'] . "\n";
        }
    }
    
    echo "\n✓ Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

