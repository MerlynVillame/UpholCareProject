<?php
define('ROOT', __DIR__);
define('DS', DIRECTORY_SEPARATOR);

require_once ROOT . '/config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Check if columns already exist
    $stmt = $db->query("SHOW COLUMNS FROM users LIKE 'reset_token'");
    $exists = $stmt->fetch();
    
    if (!$exists) {
        $sql = "ALTER TABLE users 
                ADD COLUMN reset_token VARCHAR(255) NULL AFTER verification_attempts,
                ADD COLUMN reset_token_expiry DATETIME NULL AFTER reset_token";
        
        $db->exec($sql);
        echo "Successfully added reset_token and reset_token_expiry columns to users table.\n";
    } else {
        echo "Columns already exist.\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
