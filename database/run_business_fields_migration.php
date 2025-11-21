<?php
/**
 * Run Business Fields Migration
 * This script adds business information fields to admin_registrations table
 * 
 * Access this file in your browser:
 * http://localhost/UphoCare/database/run_business_fields_migration.php
 */

// Database configuration
require_once __DIR__ . '/../config/config.php';

try {
    // Connect to database
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "<h2>Running Business Fields Migration</h2>";
    echo "<pre>";
    
    // Check if columns already exist
    $checkColumns = $db->query("
        SELECT COLUMN_NAME 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'admin_registrations' 
        AND COLUMN_NAME IN ('business_name', 'business_address', 'business_city', 'business_province', 'business_latitude', 'business_longitude', 'business_permit_path', 'business_permit_filename')
    ")->fetchAll(PDO::FETCH_COLUMN);
    
    $existingColumns = array_flip($checkColumns);
    
    // Add columns if they don't exist
    $columnsToAdd = [
        'business_name' => "VARCHAR(255) NULL AFTER phone",
        'business_address' => "TEXT NULL AFTER business_name",
        'business_city' => "VARCHAR(100) NULL DEFAULT 'Bohol' AFTER business_address",
        'business_province' => "VARCHAR(100) NULL DEFAULT 'Bohol' AFTER business_city",
        'business_latitude' => "DECIMAL(10, 8) NULL AFTER business_province",
        'business_longitude' => "DECIMAL(11, 8) NULL AFTER business_latitude",
        'business_permit_path' => "VARCHAR(255) NULL COMMENT 'Path to uploaded PDF file' AFTER business_longitude",
        'business_permit_filename' => "VARCHAR(255) NULL COMMENT 'Original filename of uploaded permit' AFTER business_permit_path"
    ];
    
    foreach ($columnsToAdd as $columnName => $columnDefinition) {
        if (!isset($existingColumns[$columnName])) {
            try {
                $sql = "ALTER TABLE `admin_registrations` ADD COLUMN `{$columnName}` {$columnDefinition}";
                $db->exec($sql);
                echo "✅ Added column: {$columnName}\n";
            } catch (PDOException $e) {
                echo "❌ Error adding column {$columnName}: " . $e->getMessage() . "\n";
            }
        } else {
            echo "⏭️  Column {$columnName} already exists, skipping...\n";
        }
    }
    
    // Add indexes
    $indexesToAdd = [
        'idx_business_location' => "(business_latitude, business_longitude)",
        'idx_business_city' => "(business_city)"
    ];
    
    // Check existing indexes
    $checkIndexes = $db->query("
        SELECT INDEX_NAME 
        FROM INFORMATION_SCHEMA.STATISTICS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'admin_registrations' 
        AND INDEX_NAME IN ('idx_business_location', 'idx_business_city')
    ")->fetchAll(PDO::FETCH_COLUMN);
    
    $existingIndexes = array_flip($checkIndexes);
    
    foreach ($indexesToAdd as $indexName => $indexDefinition) {
        if (!isset($existingIndexes[$indexName])) {
            try {
                $sql = "ALTER TABLE `admin_registrations` ADD INDEX `{$indexName}` {$indexDefinition}";
                $db->exec($sql);
                echo "✅ Added index: {$indexName}\n";
            } catch (PDOException $e) {
                echo "❌ Error adding index {$indexName}: " . $e->getMessage() . "\n";
            }
        } else {
            echo "⏭️  Index {$indexName} already exists, skipping...\n";
        }
    }
    
    echo "\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "Migration completed!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n";
    
    // Show table structure
    echo "Current admin_registrations table structure:\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $columns = $db->query("DESCRIBE admin_registrations")->fetchAll();
    foreach ($columns as $column) {
        echo sprintf("%-30s %-20s %s\n", $column['Field'], $column['Type'], $column['Null'] === 'YES' ? 'NULL' : 'NOT NULL');
    }
    
    echo "</pre>";
    echo "<p><strong>✅ Migration completed successfully!</strong></p>";
    echo "<p>You can now close this page and try registering an admin again.</p>";
    echo "<p><a href='" . BASE_URL . "auth/registerAdmin'>Go to Admin Registration</a></p>";
    
} catch (PDOException $e) {
    echo "<h2>Error</h2>";
    echo "<pre>";
    echo "Database Error: " . $e->getMessage() . "\n";
    echo "</pre>";
    echo "<p>Please check your database configuration in config/config.php</p>";
} catch (Exception $e) {
    echo "<h2>Error</h2>";
    echo "<pre>";
    echo "Error: " . $e->getMessage() . "\n";
    echo "</pre>";
}

