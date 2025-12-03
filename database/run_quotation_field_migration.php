<?php
/**
 * Add quotation_sent_at field to bookings table
 * This tracks when the final quotation email was sent after inspection (for PICKUP workflow)
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
    
    echo "Adding quotation_sent_at field to bookings table...\n\n";
    
    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM bookings LIKE 'quotation_sent_at'");
    $columnExists = $stmt->fetch();
    
    if ($columnExists) {
        echo "ℹ Column 'quotation_sent_at' already exists in bookings table.\n";
    } else {
        // Add the column (without AFTER clause since preview_sent_at might not exist)
        $sql = "ALTER TABLE `bookings` 
                ADD COLUMN `quotation_sent_at` DATETIME NULL DEFAULT NULL";
        
        $db->exec($sql);
        
        echo "✓ Column 'quotation_sent_at' added successfully!\n\n";
    }
    
    // Verify the change
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Bookings table columns:\n";
    foreach ($columns as $column) {
        if (in_array($column['Field'], ['quotation_sent_at', 'preview_sent_at', 'service_option', 'status'])) {
            echo "  - " . $column['Field'] . " (" . $column['Type'] . ")\n";
        }
    }
    
    echo "\n✓ Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

