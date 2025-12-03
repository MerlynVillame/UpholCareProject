<?php
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Checking bookings table columns...\n\n";
    
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "All columns in bookings table:\n";
    foreach ($columns as $col) {
        echo "  - " . $col['Field'] . " (" . $col['Type'] . ")\n";
    }
    
    // Check for delivery/pickup related columns
    echo "\nDelivery/Pickup related columns:\n";
    $hasDeliveryType = false;
    $hasPickupDate = false;
    $hasDeliveryDate = false;
    $hasDeliveryAddress = false;
    
    foreach ($columns as $col) {
        $field = strtolower($col['Field']);
        if ($field === 'delivery_type') {
            echo "  ✓ delivery_type exists\n";
            $hasDeliveryType = true;
        }
        if ($field === 'pickup_date') {
            echo "  ✓ pickup_date exists\n";
            $hasPickupDate = true;
        }
        if ($field === 'delivery_date') {
            echo "  ✓ delivery_date exists\n";
            $hasDeliveryDate = true;
        }
        if ($field === 'delivery_address') {
            echo "  ✓ delivery_address exists\n";
            $hasDeliveryAddress = true;
        }
    }
    
    if (!$hasDeliveryType) echo "  ✗ delivery_type does NOT exist\n";
    if (!$hasPickupDate) echo "  ✗ pickup_date does NOT exist\n";
    if (!$hasDeliveryDate) echo "  ✗ delivery_date does NOT exist\n";
    if (!$hasDeliveryAddress) echo "  ✗ delivery_address does NOT exist\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

