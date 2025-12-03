<?php
/**
 * Check fabric_type values in inventory table
 * Run this to verify what values are actually stored in the database
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "=== Checking inventory table fabric_type values ===\n\n";
    
    // Check table structure
    $stmt = $db->query("SHOW COLUMNS FROM inventory LIKE 'fabric_type'");
    $columnInfo = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Column structure:\n";
    print_r($columnInfo);
    echo "\n";
    
    // Get all inventory items with their fabric_type
    $stmt = $db->query("SELECT id, color_code, color_name, fabric_type FROM inventory ORDER BY id DESC LIMIT 10");
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Recent inventory items:\n";
    foreach ($items as $item) {
        echo sprintf(
            "ID: %d | Code: %s | Name: %s | fabric_type: %s\n",
            $item['id'],
            $item['color_code'],
            $item['color_name'],
            $item['fabric_type'] ?? 'NULL'
        );
    }
    
    // Count by type
    $stmt = $db->query("SELECT fabric_type, COUNT(*) as count FROM inventory GROUP BY fabric_type");
    $counts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nCount by fabric_type:\n";
    foreach ($counts as $count) {
        echo sprintf("%s: %d\n", $count['fabric_type'] ?? 'NULL', $count['count']);
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}

