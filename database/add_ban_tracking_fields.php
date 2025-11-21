<?php
/**
 * Add Ban Tracking Fields
 * This script adds ban duration tracking fields to store_locations, admin_registrations, and users tables
 */

// Define DS and ROOT if not already defined
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('ROOT')) {
    define('ROOT', dirname(dirname(__FILE__)));
}

require_once ROOT . DS . 'config' . DS . 'config.php';
require_once ROOT . DS . 'config' . DS . 'database.php';

$db = Database::getInstance()->getConnection();

echo "<h2>Add Ban Tracking Fields</h2>";

try {
    $db->beginTransaction();
    
    // Add ban tracking fields to store_locations table
    echo "<h3>1. Adding ban tracking fields to store_locations table...</h3>";
    $alterations = [
        "ALTER TABLE store_locations ADD COLUMN IF NOT EXISTS banned_at TIMESTAMP NULL COMMENT 'When the store was banned'",
        "ALTER TABLE store_locations ADD COLUMN IF NOT EXISTS banned_until TIMESTAMP NULL COMMENT 'When the ban expires (NULL = permanent)'",
        "ALTER TABLE store_locations ADD COLUMN IF NOT EXISTS ban_duration_days INT NULL COMMENT 'Duration of ban in days (NULL = permanent)'",
        "ALTER TABLE store_locations ADD COLUMN IF NOT EXISTS ban_reason TEXT NULL COMMENT 'Reason for banning the store'",
        "ALTER TABLE store_locations ADD COLUMN IF NOT EXISTS banned_by INT NULL COMMENT 'Super admin who banned the store'"
    ];
    
    foreach ($alterations as $sql) {
        // Check if column exists first
        $columnName = preg_match("/ADD COLUMN IF NOT EXISTS (\w+)/", $sql, $matches) ? $matches[1] : null;
        if ($columnName) {
            $checkStmt = $db->query("
                SELECT COUNT(*) as cnt 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'store_locations' 
                AND COLUMN_NAME = '$columnName'
            ");
            $exists = $checkStmt->fetch()['cnt'] > 0;
            
            if (!$exists) {
                // Remove IF NOT EXISTS (MySQL doesn't support it)
                $sql = str_replace('IF NOT EXISTS ', '', $sql);
                $db->exec($sql);
                echo "✅ Added column: $columnName<br>";
            } else {
                echo "⏭️  Column already exists: $columnName<br>";
            }
        } else {
            $db->exec($sql);
        }
    }
    
    // Add ban tracking fields to admin_registrations table
    echo "<h3>2. Adding ban tracking fields to admin_registrations table...</h3>";
    $alterations = [
        "ALTER TABLE admin_registrations ADD COLUMN IF NOT EXISTS banned_at TIMESTAMP NULL COMMENT 'When the admin was banned'",
        "ALTER TABLE admin_registrations ADD COLUMN IF NOT EXISTS banned_until TIMESTAMP NULL COMMENT 'When the ban expires (NULL = permanent)'",
        "ALTER TABLE admin_registrations ADD COLUMN IF NOT EXISTS ban_duration_days INT NULL COMMENT 'Duration of ban in days (NULL = permanent)'",
        "ALTER TABLE admin_registrations ADD COLUMN IF NOT EXISTS ban_reason TEXT NULL COMMENT 'Reason for banning the admin'",
        "ALTER TABLE admin_registrations ADD COLUMN IF NOT EXISTS banned_by INT NULL COMMENT 'Super admin who banned the admin'"
    ];
    
    foreach ($alterations as $sql) {
        $columnName = preg_match("/ADD COLUMN IF NOT EXISTS (\w+)/", $sql, $matches) ? $matches[1] : null;
        if ($columnName) {
            $checkStmt = $db->query("
                SELECT COUNT(*) as cnt 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'admin_registrations' 
                AND COLUMN_NAME = '$columnName'
            ");
            $exists = $checkStmt->fetch()['cnt'] > 0;
            
            if (!$exists) {
                $sql = str_replace('IF NOT EXISTS ', '', $sql);
                $db->exec($sql);
                echo "✅ Added column: $columnName<br>";
            } else {
                echo "⏭️  Column already exists: $columnName<br>";
            }
        }
    }
    
    // Add ban tracking fields to users table
    echo "<h3>3. Adding ban tracking fields to users table...</h3>";
    $alterations = [
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS banned_at TIMESTAMP NULL COMMENT 'When the user was banned'",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS banned_until TIMESTAMP NULL COMMENT 'When the ban expires (NULL = permanent)'",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS ban_duration_days INT NULL COMMENT 'Duration of ban in days (NULL = permanent)'",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS ban_reason TEXT NULL COMMENT 'Reason for banning the user'",
        "ALTER TABLE users ADD COLUMN IF NOT EXISTS banned_by INT NULL COMMENT 'Super admin who banned the user'"
    ];
    
    foreach ($alterations as $sql) {
        $columnName = preg_match("/ADD COLUMN IF NOT EXISTS (\w+)/", $sql, $matches) ? $matches[1] : null;
        if ($columnName) {
            $checkStmt = $db->query("
                SELECT COUNT(*) as cnt 
                FROM INFORMATION_SCHEMA.COLUMNS 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = 'users' 
                AND COLUMN_NAME = '$columnName'
            ");
            $exists = $checkStmt->fetch()['cnt'] > 0;
            
            if (!$exists) {
                $sql = str_replace('IF NOT EXISTS ', '', $sql);
                $db->exec($sql);
                echo "✅ Added column: $columnName<br>";
            } else {
                echo "⏭️  Column already exists: $columnName<br>";
            }
        }
    }
    
    // Add indexes
    echo "<h3>4. Adding indexes...</h3>";
    $indexes = [
        ['store_locations', 'idx_banned_at', '(banned_at)'],
        ['store_locations', 'idx_banned_until', '(banned_until)'],
        ['admin_registrations', 'idx_banned_at', '(banned_at)'],
        ['admin_registrations', 'idx_banned_until', '(banned_until)'],
        ['users', 'idx_banned_at', '(banned_at)'],
        ['users', 'idx_banned_until', '(banned_until)']
    ];
    
    foreach ($indexes as $index) {
        list($table, $indexName, $columns) = $index;
        $checkStmt = $db->query("
            SELECT COUNT(*) as cnt 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = '$table' 
            AND INDEX_NAME = '$indexName'
        ");
        $exists = $checkStmt->fetch()['cnt'] > 0;
        
        if (!$exists) {
            $db->exec("ALTER TABLE $table ADD INDEX $indexName $columns");
            echo "✅ Added index: $table.$indexName<br>";
        } else {
            echo "⏭️  Index already exists: $table.$indexName<br>";
        }
    }
    
    $db->commit();
    echo "<h3 style='color: green;'>✅ All ban tracking fields added successfully!</h3>";
    
} catch (Exception $e) {
    $db->rollBack();
    echo "<h3 style='color: red;'>❌ Error: " . $e->getMessage() . "</h3>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

?>

