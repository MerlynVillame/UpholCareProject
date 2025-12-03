<?php
/**
 * Add 'delivered_and_paid' status to bookings table ENUM
 */

require_once __DIR__ . '/../config/database.php';

echo "Adding 'delivered_and_paid' status to bookings table...\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    // Check current database
    $stmt = $db->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Connected to database: " . ($result['db_name'] ?? 'NULL') . "\n\n";
    
    // Check current status ENUM
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $currentEnum = '';
    foreach ($columns as $col) {
        if ($col['Field'] === 'status') {
            $currentEnum = $col['Type'];
            echo "Current status ENUM: " . $currentEnum . "\n";
            break;
        }
    }
    
    // Add 'delivered_and_paid' to the ENUM
    echo "\nAdding 'delivered_and_paid' to status ENUM...\n";
    $alterSql = "ALTER TABLE bookings MODIFY COLUMN status ENUM(
        'pending',
        'approved',
        'in_queue',
        'under_repair',
        'for_quality_check',
        'ready_for_pickup',
        'out_for_delivery',
        'completed',
        'delivered_and_paid',
        'cancelled',
        'confirmed',
        'in_progress',
        'accepted',
        'rejected',
        'declined'
    ) DEFAULT 'pending'";
    
    $db->exec($alterSql);
    echo "✓ Status ENUM updated successfully!\n";
    
    // Verify the change
    echo "\nVerifying the change...\n";
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        if ($col['Field'] === 'status') {
            echo "New status ENUM: " . $col['Type'] . "\n";
            break;
        }
    }
    
    echo "\n✓ Database update completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

