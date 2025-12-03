<?php
/**
 * Comprehensive Database Schema Checker and Fixer
 * This script checks for all required columns in the bookings table
 * and adds any missing columns automatically
 * 
 * Run this script to ensure your database has all required fields
 */

// Database configuration
$host = 'localhost';
$dbname = 'db_upholcare';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "╔════════════════════════════════════════════════════════════════╗\n";
    echo "║  UphoCare Database Schema Checker and Fixer                   ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n\n";
    
    // Define all required columns with their definitions
    $requiredColumns = [
        // Basic booking info
        'user_id' => "INT(11) NOT NULL",
        'service_id' => "INT(11) NULL",
        'booking_number_id' => "INT(11) NULL",
        'store_location_id' => "INT(11) NULL",
        
        // Service details
        'service_type' => "VARCHAR(100) NULL",
        'service_option' => "VARCHAR(50) DEFAULT 'pickup'",
        
        // Item information
        'item_description' => "TEXT NULL",
        
        // Addresses and dates
        'pickup_address' => "TEXT NULL",
        'pickup_date' => "DATE NULL",
        'delivery_address' => "TEXT NULL",
        'delivery_date' => "DATE NULL",
        
        // Notes
        'notes' => "TEXT NULL",
        'admin_notes' => "TEXT NULL",
        
        // Color/Material selection
        'selected_color_id' => "INT(11) NULL",
        'color_type' => "VARCHAR(50) NULL",
        'color_price' => "DECIMAL(10,2) DEFAULT 0.00",
        
        // Pricing fields
        'total_amount' => "DECIMAL(10,2) DEFAULT 0.00",
        'labor_fee' => "DECIMAL(10,2) DEFAULT 0.00",
        'pickup_fee' => "DECIMAL(10,2) DEFAULT 0.00",
        'delivery_fee' => "DECIMAL(10,2) DEFAULT 0.00",
        'gas_fee' => "DECIMAL(10,2) DEFAULT 0.00",
        'travel_fee' => "DECIMAL(10,2) DEFAULT 0.00",
        'distance_km' => "DECIMAL(10,2) DEFAULT 0.00",
        'total_additional_fees' => "DECIMAL(10,2) DEFAULT 0.00",
        'grand_total' => "DECIMAL(10,2) DEFAULT 0.00",
        
        // Payment
        'payment_status' => "ENUM('unpaid','paid','paid_full_cash','paid_on_delivery_cod','refunded','failed','cancelled') DEFAULT 'unpaid'",
        
        // Booking type
        'booking_type' => "VARCHAR(50) DEFAULT 'personal'",
        
        // Status
        'status' => "ENUM('pending','for_pickup','picked_up','for_inspection','for_quotation','approved','in_queue','in_progress','under_repair','for_quality_check','ready_for_pickup','out_for_delivery','completed','paid','closed','cancelled','confirmed','accepted','rejected','declined','admin_review') DEFAULT 'pending'",
        
        // Timestamps
        'quotation_sent_at' => "DATETIME NULL",
        'created_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP",
        'updated_at' => "TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP"
    ];
    
    // Get existing columns
    $stmt = $db->query("SHOW COLUMNS FROM bookings");
    $existingColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $existingColumnNames = array_column($existingColumns, 'Field');
    
    echo "Checking bookings table...\n\n";
    
    $missingColumns = [];
    $existingCount = 0;
    
    foreach ($requiredColumns as $columnName => $definition) {
        if (!in_array($columnName, $existingColumnNames)) {
            $missingColumns[$columnName] = $definition;
        } else {
            $existingCount++;
        }
    }
    
    echo "Summary:\n";
    echo "  ✓ Existing columns: $existingCount\n";
    echo "  " . (count($missingColumns) > 0 ? "⚠" : "✓") . " Missing columns: " . count($missingColumns) . "\n\n";
    
    if (count($missingColumns) > 0) {
        echo "Adding missing columns...\n\n";
        
        foreach ($missingColumns as $columnName => $definition) {
            try {
                // Special handling for ENUM and special columns
                if ($columnName === 'status' || $columnName === 'payment_status') {
                    // Don't add these if missing - they should always exist
                    echo "⚠ Column '$columnName' is critical and missing! Manual intervention required.\n";
                    continue;
                }
                
                if ($columnName === 'created_at' || $columnName === 'updated_at') {
                    // Special handling for timestamp columns
                    $sql = "ALTER TABLE `bookings` ADD COLUMN `$columnName` $definition";
                } else {
                    $sql = "ALTER TABLE `bookings` ADD COLUMN `$columnName` $definition";
                }
                
                $db->exec($sql);
                echo "  ✓ Added '$columnName'\n";
            } catch (PDOException $e) {
                echo "  ✗ Failed to add '$columnName': " . $e->getMessage() . "\n";
            }
        }
        
        echo "\n";
    } else {
        echo "✓ All required columns exist! No changes needed.\n\n";
    }
    
    // Final verification
    echo "Final verification:\n";
    $stmt = $db->query("DESCRIBE bookings");
    $allColumns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Total columns in bookings table: " . count($allColumns) . "\n\n";
    
    echo "Key columns status:\n";
    $keyColumns = ['service_option', 'pickup_address', 'delivery_address', 'pickup_date', 'delivery_date', 'status', 'payment_status'];
    foreach ($allColumns as $column) {
        if (in_array($column['Field'], $keyColumns)) {
            echo "  ✓ " . $column['Field'] . " (" . $column['Type'] . ")\n";
        }
    }
    
    echo "\n╔════════════════════════════════════════════════════════════════╗\n";
    echo "║  Migration Completed Successfully!                            ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    
} catch (PDOException $e) {
    echo "\n╔════════════════════════════════════════════════════════════════╗\n";
    echo "║  ERROR: Migration Failed!                                     ║\n";
    echo "╚════════════════════════════════════════════════════════════════╝\n";
    echo "\nError: " . $e->getMessage() . "\n";
    exit(1);
}

