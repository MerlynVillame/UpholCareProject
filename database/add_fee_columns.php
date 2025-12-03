<?php
/**
 * Add fee columns to bookings table
 */

require_once __DIR__ . '/../config/database.php';

echo "Adding fee columns to bookings table...\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    // Check current database
    $stmt = $db->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Connected to database: " . ($result['db_name'] ?? 'NULL') . "\n\n";
    
    // Check if columns already exist
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $existingColumns = array_column($columns, 'Field');
    
    $columnsToAdd = [
        'labor_fee' => "DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Fixed labor fee'",
        'pickup_fee' => "DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Pickup service fee'",
        'delivery_fee' => "DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Delivery COD service fee'",
        'gas_fee' => "DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Gas/fuel fee based on distance'",
        'travel_fee' => "DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Travel fee based on distance'",
        'distance_km' => "DECIMAL(8,2) DEFAULT 0.00 COMMENT 'Distance in kilometers for fee calculation'",
        'total_additional_fees' => "DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Sum of all additional fees'",
        'grand_total' => "DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Base service price + total additional fees'"
    ];
    
    echo "Checking existing columns...\n";
    foreach ($columnsToAdd as $columnName => $columnDef) {
        if (in_array($columnName, $existingColumns)) {
            echo "  ✓ $columnName already exists\n";
        } else {
            echo "  ✗ $columnName does NOT exist - will add\n";
        }
    }
    
    echo "\nAdding missing columns...\n";
    foreach ($columnsToAdd as $columnName => $columnDef) {
        if (!in_array($columnName, $existingColumns)) {
            try {
                $sql = "ALTER TABLE bookings ADD COLUMN `$columnName` $columnDef AFTER `total_amount`";
                $db->exec($sql);
                echo "  ✓ Added $columnName\n";
            } catch (Exception $e) {
                echo "  ✗ Error adding $columnName: " . $e->getMessage() . "\n";
            }
        }
    }
    
    // Verify the changes
    echo "\nVerifying columns...\n";
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($columnsToAdd as $columnName => $columnDef) {
        $found = false;
        foreach ($columns as $col) {
            if ($col['Field'] === $columnName) {
                echo "  ✓ $columnName: " . $col['Type'] . "\n";
                $found = true;
                break;
            }
        }
        if (!$found) {
            echo "  ✗ $columnName: NOT FOUND\n";
        }
    }
    
    echo "\n✓ Database update completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

