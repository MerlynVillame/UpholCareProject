<?php
/**
 * Add pickup/delivery address and date columns to bookings table
 * These columns are essential for managing pickup and delivery logistics
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
    
    echo "Adding pickup/delivery address and date columns to bookings table...\n\n";
    
    $columnsToAdd = [
        'pickup_address' => "TEXT NULL",
        'pickup_date' => "DATE NULL",
        'delivery_address' => "TEXT NULL",
        'delivery_date' => "DATE NULL"
    ];
    
    $addedColumns = [];
    $existingColumns = [];
    
    foreach ($columnsToAdd as $columnName => $columnDefinition) {
        // Check if column already exists
        $stmt = $db->query("SHOW COLUMNS FROM bookings LIKE '$columnName'");
        $columnExists = $stmt->fetch();
        
        if ($columnExists) {
            echo "ℹ Column '$columnName' already exists.\n";
            $existingColumns[] = $columnName;
        } else {
            // Add the column
            $sql = "ALTER TABLE `bookings` ADD COLUMN `$columnName` $columnDefinition";
            $db->exec($sql);
            echo "✓ Column '$columnName' added successfully!\n";
            $addedColumns[] = $columnName;
        }
    }
    
    echo "\n";
    
    if (count($addedColumns) > 0) {
        echo "Columns added: " . implode(', ', $addedColumns) . "\n";
    }
    if (count($existingColumns) > 0) {
        echo "Columns already existed: " . implode(', ', $existingColumns) . "\n";
    }
    
    echo "\n";
    
    // Verify the changes
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Relevant bookings table columns:\n";
    foreach ($columns as $column) {
        if (in_array($column['Field'], ['service_option', 'pickup_address', 'pickup_date', 'delivery_address', 'delivery_date', 'status'])) {
            $type = $column['Type'];
            $default = $column['Default'] !== null ? $column['Default'] : 'NULL';
            echo "  - " . $column['Field'] . " (" . $type . ") DEFAULT " . $default . "\n";
        }
    }
    
    // Show sample data
    echo "\nSample bookings data:\n";
    $stmt = $db->query("SELECT id, service_option, pickup_date, delivery_date, status FROM bookings LIMIT 5");
    $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($samples)) {
        echo "No bookings found.\n";
    } else {
        foreach ($samples as $sample) {
            echo "  ID: " . $sample['id'] . 
                 " | Option: " . ($sample['service_option'] ?? 'N/A') . 
                 " | Pickup: " . ($sample['pickup_date'] ?? 'N/A') . 
                 " | Delivery: " . ($sample['delivery_date'] ?? 'N/A') . 
                 " | Status: " . ($sample['status'] ?? 'N/A') . "\n";
        }
    }
    
    echo "\n✓ Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

