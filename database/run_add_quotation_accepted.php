<?php
/**
 * Add quotation_accepted columns to bookings table
 * Run this script once to update the database schema
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Adding quotation_accepted columns to bookings table...\n\n";
    
    // Check if quotation_accepted column exists
    $checkAccepted = $db->query("SHOW COLUMNS FROM bookings LIKE 'quotation_accepted'");
    $hasAccepted = $checkAccepted->fetch();
    
    if (!$hasAccepted) {
        echo "Adding 'quotation_accepted' column...\n";
        $db->exec("ALTER TABLE `bookings` 
            ADD COLUMN `quotation_accepted` TINYINT(1) DEFAULT 0 
            COMMENT 'Flag to track if quotation/receipt was accepted by customer'");
        echo "✓ 'quotation_accepted' column added successfully!\n\n";
    } else {
        echo "✓ 'quotation_accepted' column already exists.\n\n";
    }
    
    // Check if quotation_accepted_at column exists
    $checkAcceptedAt = $db->query("SHOW COLUMNS FROM bookings LIKE 'quotation_accepted_at'");
    $hasAcceptedAt = $checkAcceptedAt->fetch();
    
    if (!$hasAcceptedAt) {
        echo "Adding 'quotation_accepted_at' column...\n";
        $db->exec("ALTER TABLE `bookings` 
            ADD COLUMN `quotation_accepted_at` DATETIME NULL DEFAULT NULL 
            COMMENT 'Timestamp when customer accepted the quotation/receipt'");
        echo "✓ 'quotation_accepted_at' column added successfully!\n\n";
    } else {
        echo "✓ 'quotation_accepted_at' column already exists.\n\n";
    }
    
    // Verify the columns
    $stmt = $db->query("
        SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT
        FROM information_schema.COLUMNS 
        WHERE TABLE_SCHEMA = 'db_upholcare' 
        AND TABLE_NAME = 'bookings' 
        AND COLUMN_NAME LIKE '%quotation%'
    ");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Quotation-related columns in bookings table:\n";
    foreach ($columns as $col) {
        echo "  - {$col['COLUMN_NAME']} ({$col['DATA_TYPE']})\n";
    }
    
    echo "\n✓ Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    if (strpos($e->getMessage(), 'Duplicate column') !== false) {
        echo "Note: Column already exists. This is okay.\n";
    }
    exit(1);
}

