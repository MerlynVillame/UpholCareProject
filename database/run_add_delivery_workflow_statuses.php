<?php
/**
 * Add Delivery Workflow Statuses to bookings table
 * Run this script once to add: for_dropoff, for_inspect, inspection_completed_waiting_approval
 * 
 * Usage: php database/run_add_delivery_workflow_statuses.php
 * Or run the SQL file directly in phpMyAdmin
 */

// Load database configuration
require_once __DIR__ . '/../config/database.php';

try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "========================================\n";
    echo "Adding Delivery Workflow Statuses\n";
    echo "========================================\n\n";
    
    // First, get current ENUM values
    echo "1. Checking current status ENUM...\n";
    $checkStmt = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'");
    $column = $checkStmt->fetch(PDO::FETCH_ASSOC);
    $currentEnum = $column['Type'] ?? '';
    echo "   Current ENUM: {$currentEnum}\n\n";
    
    // Check if statuses already exist
    $hasForDropoff = stripos($currentEnum, 'for_dropoff') !== false;
    $hasForInspect = stripos($currentEnum, 'for_inspect') !== false;
    $hasInspectionCompletedWaitingApproval = stripos($currentEnum, 'inspection_completed_waiting_approval') !== false;
    
    if ($hasForDropoff && $hasForInspect && $hasInspectionCompletedWaitingApproval) {
        echo "✓ All delivery workflow statuses already exist in the ENUM!\n";
        echo "  - for_dropoff: ✓\n";
        echo "  - for_inspect: ✓\n";
        echo "  - inspection_completed_waiting_approval: ✓\n\n";
        echo "No migration needed. Exiting.\n";
        exit(0);
    }
    
    echo "2. Adding missing statuses to ENUM...\n";
    
    // Build the complete ENUM list
    $enumValues = [
        'pending',
        'approved',
        'for_pickup',
        'for_dropoff',  // NEW
        'for_inspect',  // NEW
        'picked_up',
        'to_inspect',
        'for_inspection',
        'inspect_completed',
        'inspection_completed_waiting_approval',  // NEW
        'preview_receipt_sent',
        'for_repair',
        'in_queue',
        'in_progress',
        'under_repair',
        'for_quality_check',
        'repair_completed',
        'repair_completed_ready_to_deliver',
        'ready_for_pickup',
        'out_for_delivery',
        'completed',
        'delivered_and_paid',
        'paid',
        'closed',
        'cancelled',
        'confirmed',
        'accepted',
        'rejected',
        'declined',
        'admin_review'
    ];
    
    $enumString = "'" . implode("','", $enumValues) . "'";
    
    $sql = "ALTER TABLE `bookings` 
            MODIFY COLUMN `status` ENUM({$enumString}) DEFAULT 'pending'";
    
    echo "   Executing: ALTER TABLE bookings MODIFY COLUMN status...\n";
    $db->exec($sql);
    
    echo "✓ Status ENUM updated successfully!\n\n";
    
    // Verify the change
    echo "3. Verifying the update...\n";
    $verifyStmt = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'");
    $verifyColumn = $verifyStmt->fetch(PDO::FETCH_ASSOC);
    $newEnum = $verifyColumn['Type'] ?? '';
    echo "   New ENUM: {$newEnum}\n\n";
    
    // Check each status
    $checkForDropoff = stripos($newEnum, 'for_dropoff') !== false;
    $checkForInspect = stripos($newEnum, 'for_inspect') !== false;
    $checkInspectionCompletedWaitingApproval = stripos($newEnum, 'inspection_completed_waiting_approval') !== false;
    
    if ($checkForDropoff && $checkForInspect && $checkInspectionCompletedWaitingApproval) {
        echo "✓ All delivery workflow statuses successfully added!\n";
        echo "  - for_dropoff: ✓\n";
        echo "  - for_inspect: ✓\n";
        echo "  - inspection_completed_waiting_approval: ✓\n\n";
    } else {
        echo "⚠ Warning: Some statuses may not have been added correctly.\n";
        echo "  - for_dropoff: " . ($checkForDropoff ? '✓' : '✗') . "\n";
        echo "  - for_inspect: " . ($checkForInspect ? '✓' : '✗') . "\n";
        echo "  - inspection_completed_waiting_approval: " . ($checkInspectionCompletedWaitingApproval ? '✓' : '✗') . "\n\n";
    }
    
    // Show current status distribution
    echo "4. Current bookings status distribution:\n";
    $distStmt = $db->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status ORDER BY count DESC");
    $distributions = $distStmt->fetchAll();
    
    if (empty($distributions)) {
        echo "   No bookings found.\n";
    } else {
        foreach ($distributions as $dist) {
            echo "   - {$dist['status']}: {$dist['count']}\n";
        }
    }
    
    echo "\n========================================\n";
    echo "Migration completed successfully!\n";
    echo "========================================\n";
    
} catch (PDOException $e) {
    echo "\n✗ Database Error:\n";
    echo "  " . $e->getMessage() . "\n";
    echo "\nPlease check:\n";
    echo "  1. Database connection settings in config/database.php\n";
    echo "  2. Database user has ALTER TABLE permissions\n";
    echo "  3. No other processes are using the bookings table\n";
    exit(1);
} catch (Exception $e) {
    echo "\n✗ Error:\n";
    echo "  " . $e->getMessage() . "\n";
    exit(1);
}

