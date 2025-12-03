<?php
/**
 * Migration: Add price_per_meter column to inventory table
 * Run this script once to add the price_per_meter column
 */

// Database configuration
require_once __DIR__ . '/../config/database.php';

try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "Connected to database successfully.\n";
    
    // Check if column already exists
    $checkColumn = $pdo->query("SHOW COLUMNS FROM `inventory` LIKE 'price_per_meter'");
    if ($checkColumn->rowCount() > 0) {
        echo "Column 'price_per_meter' already exists in inventory table.\n";
        echo "Migration not needed.\n";
        exit(0);
    }
    
    // Add the column
    $sql = "ALTER TABLE `inventory`
            ADD COLUMN `price_per_meter` DECIMAL(10,2) DEFAULT 0.00 
            COMMENT 'Price per meter for leather material' 
            AFTER `premium_price`";
    
    $pdo->exec($sql);
    
    echo "Successfully added 'price_per_meter' column to inventory table.\n";
    echo "Migration completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

