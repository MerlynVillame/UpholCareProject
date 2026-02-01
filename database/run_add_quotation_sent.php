<?php
/**
 * Add quotation_sent column to bookings table
 * This tracks if the quotation/receipt was sent to the customer
 * Run this script once to update the database schema
 */

// Database configuration
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Adding quotation_sent column to bookings table...\n\n";
    
    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM bookings LIKE 'quotation_sent'");
    $columnExists = $stmt->fetch();
    
    if ($columnExists) {
        echo "ℹ Column 'quotation_sent' already exists in bookings table.\n";
    } else {
        // Add the column
        $sql = "ALTER TABLE `bookings` 
                ADD COLUMN `quotation_sent` TINYINT(1) DEFAULT 0 
                COMMENT 'Flag to track if quotation/receipt was sent to customer'";
        
        $db->exec($sql);
        
        echo "✓ Column 'quotation_sent' added successfully!\n\n";
    }
    
    // Verify the change
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Bookings table columns related to quotation:\n";
    foreach ($columns as $column) {
        if (strpos($column['Field'], 'quotation') !== false) {
            echo "  - " . $column['Field'] . " (" . $column['Type'] . ")\n";
        }
    }
    
    echo "\n✓ Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

