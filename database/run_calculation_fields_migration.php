<?php
/**
 * Migration: Add calculation fields for total payment (bayronon)
 * 
 * This migration adds fields for admin to:
 * - Measure fabric (length, width, area)
 * - Calculate fabric cost
 * - Add material costs
 * - Calculate total payment before approval
 */

require_once __DIR__ . '/../config/database.php';

$sqlFile = __DIR__ . '/add_calculation_fields.sql';

if (!file_exists($sqlFile)) {
    die("Error: SQL file not found: $sqlFile\n");
}

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting migration: Add calculation fields...\n";
    
    // Read SQL file
    $sql = file_get_contents($sqlFile);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue; // Skip empty statements and comments
        }
        
        try {
            $db->exec($statement);
            echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
        } catch (PDOException $e) {
            // Check if error is about column already existing
            if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                echo "⚠ Column already exists, skipping: " . substr($statement, 0, 50) . "...\n";
            } else {
                throw $e;
            }
        }
    }
    
    echo "\n✅ Migration completed successfully!\n";
    echo "Calculation fields added to bookings table.\n";
    
} catch (Exception $e) {
    echo "\n❌ Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}

