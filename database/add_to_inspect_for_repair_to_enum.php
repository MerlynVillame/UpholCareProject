<?php
/**
 * Add 'to_inspect' and 'for_repair' to bookings.status ENUM
 * This is CRITICAL for the inspection workflow to work
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Adding 'to_inspect' and 'for_repair' to bookings.status ENUM...\n\n";
    
    // Get current ENUM values
    $stmt = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$result) {
        die("Error: Could not find 'status' column in bookings table\n");
    }
    
    echo "Current ENUM: " . $result['Type'] . "\n\n";
    
    // Check if to_inspect and for_repair are already in the ENUM
    $currentType = $result['Type'];
    $hasToInspect = strpos($currentType, 'to_inspect') !== false;
    $hasForRepair = strpos($currentType, 'for_repair') !== false;
    
    if ($hasToInspect && $hasForRepair) {
        echo "✓ 'to_inspect' and 'for_repair' are already in the ENUM.\n";
        exit(0);
    }
    
    // Build new ENUM with to_inspect and for_repair
    // Keep all existing values and add the new ones
    $newEnumValues = [
        'pending',
        'for_pickup',
        'picked_up',
        'to_inspect',        // NEW
        'for_inspection',
        'for_repair',        // NEW
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
    ];
    
    $enumString = "ENUM('" . implode("','", $newEnumValues) . "')";
    
    echo "New ENUM will be: " . $enumString . "\n\n";
    
    // Update the column
    $sql = "ALTER TABLE `bookings` MODIFY COLUMN `status` {$enumString} DEFAULT 'pending'";
    
    echo "Executing: " . $sql . "\n\n";
    
    $db->exec($sql);
    
    echo "✓ Successfully updated bookings.status ENUM!\n\n";
    
    // Verify the change
    $verifyStmt = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'");
    $verifyResult = $verifyStmt->fetch(PDO::FETCH_ASSOC);
    
    echo "Updated ENUM: " . $verifyResult['Type'] . "\n\n";
    
    // Check if to_inspect and for_repair are now in the ENUM
    $newType = $verifyResult['Type'];
    $hasToInspectNow = strpos($newType, 'to_inspect') !== false;
    $hasForRepairNow = strpos($newType, 'for_repair') !== false;
    
    if ($hasToInspectNow && $hasForRepairNow) {
        echo "✓ Verification passed: 'to_inspect' and 'for_repair' are now in the ENUM.\n";
    } else {
        echo "✗ Verification failed!\n";
        if (!$hasToInspectNow) echo "  - 'to_inspect' is missing\n";
        if (!$hasForRepairNow) echo "  - 'for_repair' is missing\n";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
    exit(1);
}

