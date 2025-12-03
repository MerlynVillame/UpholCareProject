<?php
/**
 * Add service_option column to bookings table
 * This column is essential for the PICKUP workflow and admin approval process
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
    
    echo "Adding service_option column to bookings table...\n\n";
    
    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM bookings LIKE 'service_option'");
    $columnExists = $stmt->fetch();
    
    if ($columnExists) {
        echo "ℹ Column 'service_option' already exists in bookings table.\n";
    } else {
        // Add the column
        $sql = "ALTER TABLE `bookings` 
                ADD COLUMN `service_option` VARCHAR(50) DEFAULT 'pickup' AFTER `service_type`";
        
        $db->exec($sql);
        
        echo "✓ Column 'service_option' added successfully!\n\n";
    }
    
    // Verify the change
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Relevant bookings table columns:\n";
    foreach ($columns as $column) {
        if (in_array($column['Field'], ['service_type', 'service_option', 'status', 'payment_status'])) {
            echo "  - " . $column['Field'] . " (" . $column['Type'] . ") DEFAULT " . ($column['Default'] ?? 'NULL') . "\n";
        }
    }
    
    // Show sample data
    echo "\nSample bookings data:\n";
    $stmt = $db->query("SELECT id, service_type, service_option, status FROM bookings LIMIT 5");
    $samples = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($samples)) {
        echo "No bookings found.\n";
    } else {
        foreach ($samples as $sample) {
            echo "  ID: " . $sample['id'] . 
                 " | Type: " . ($sample['service_type'] ?? 'N/A') . 
                 " | Option: " . ($sample['service_option'] ?? 'N/A') . 
                 " | Status: " . ($sample['status'] ?? 'N/A') . "\n";
        }
    }
    
    echo "\n✓ Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

