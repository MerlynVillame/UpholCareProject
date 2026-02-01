<?php
define('ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);

require_once ROOT . '/config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if column already exists
    $stmt = $db->query("SHOW COLUMNS FROM bookings LIKE 'is_archived'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $sql = "ALTER TABLE bookings 
                ADD COLUMN is_archived TINYINT(1) NOT NULL DEFAULT 0 AFTER status";
        
        $db->exec($sql);
        echo "Successfully added is_archived column to bookings table.\n";
    } else {
        echo "Column is_archived already exists.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
