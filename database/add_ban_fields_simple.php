<?php
/**
 * Add Ban Tracking Fields - Simple Version
 * This script adds ban tracking fields to store_locations, admin_registrations, and users tables
 * Run this in your browser: http://localhost/UphoCare/database/add_ban_fields_simple.php
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

echo "<!DOCTYPE html><html><head><title>Add Ban Tracking Fields</title>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;background:#f5f5f5;}";
echo ".success{color:green;padding:10px;background:#d4edda;border:1px solid #c3e6cb;border-radius:5px;margin:10px 0;}";
echo ".error{color:red;padding:10px;background:#f8d7da;border:1px solid #f5c6cb;border-radius:5px;margin:10px 0;}";
echo ".info{color:blue;padding:10px;background:#d1ecf1;border:1px solid #bee5eb;border-radius:5px;margin:10px 0;}";
echo "table{border-collapse:collapse;width:100%;margin:20px 0;}";
echo "th,td{border:1px solid #ddd;padding:8px;text-align:left;}";
echo "th{background-color:#4CAF50;color:white;}</style></head><body>";
echo "<h2>Add Ban Tracking Fields to Database</h2>";

try {
    $db->beginTransaction();
    $errors = [];
    $success = [];
    
    // Function to check if column exists
    function columnExists($db, $table, $column) {
        $stmt = $db->query("
            SELECT COUNT(*) as cnt 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = '$table' 
            AND COLUMN_NAME = '$column'
        ");
        return $stmt->fetch()['cnt'] > 0;
    }
    
    // Add ban tracking fields to store_locations table
    echo "<h3>1. Adding ban tracking fields to store_locations table...</h3>";
    $storeLocationsFields = [
        'banned_at' => "ALTER TABLE store_locations ADD COLUMN banned_at TIMESTAMP NULL COMMENT 'When the store was banned'",
        'banned_until' => "ALTER TABLE store_locations ADD COLUMN banned_until TIMESTAMP NULL COMMENT 'When the ban expires (NULL = permanent)'",
        'ban_duration_days' => "ALTER TABLE store_locations ADD COLUMN ban_duration_days INT NULL COMMENT 'Duration of ban in days (NULL = permanent)'",
        'ban_reason' => "ALTER TABLE store_locations ADD COLUMN ban_reason TEXT NULL COMMENT 'Reason for banning the store'",
        'banned_by' => "ALTER TABLE store_locations ADD COLUMN banned_by INT NULL COMMENT 'Super admin who banned the store'"
    ];
    
    foreach ($storeLocationsFields as $field => $sql) {
        if (!columnExists($db, 'store_locations', $field)) {
            try {
                $db->exec($sql);
                $success[] = "store_locations.$field";
                echo "<div class='success'>✅ Added column: store_locations.$field</div>";
            } catch (Exception $e) {
                $errors[] = "store_locations.$field: " . $e->getMessage();
                echo "<div class='error'>❌ Error adding store_locations.$field: " . $e->getMessage() . "</div>";
            }
        } else {
            echo "<div class='info'>⏭️ Column already exists: store_locations.$field</div>";
        }
    }
    
    // Add ban tracking fields to admin_registrations table
    echo "<h3>2. Adding ban tracking fields to admin_registrations table...</h3>";
    $adminRegistrationsFields = [
        'banned_at' => "ALTER TABLE admin_registrations ADD COLUMN banned_at TIMESTAMP NULL COMMENT 'When the admin was banned'",
        'banned_until' => "ALTER TABLE admin_registrations ADD COLUMN banned_until TIMESTAMP NULL COMMENT 'When the ban expires (NULL = permanent)'",
        'ban_duration_days' => "ALTER TABLE admin_registrations ADD COLUMN ban_duration_days INT NULL COMMENT 'Duration of ban in days (NULL = permanent)'",
        'ban_reason' => "ALTER TABLE admin_registrations ADD COLUMN ban_reason TEXT NULL COMMENT 'Reason for banning the admin'",
        'banned_by' => "ALTER TABLE admin_registrations ADD COLUMN banned_by INT NULL COMMENT 'Super admin who banned the admin'"
    ];
    
    foreach ($adminRegistrationsFields as $field => $sql) {
        if (!columnExists($db, 'admin_registrations', $field)) {
            try {
                $db->exec($sql);
                $success[] = "admin_registrations.$field";
                echo "<div class='success'>✅ Added column: admin_registrations.$field</div>";
            } catch (Exception $e) {
                $errors[] = "admin_registrations.$field: " . $e->getMessage();
                echo "<div class='error'>❌ Error adding admin_registrations.$field: " . $e->getMessage() . "</div>";
            }
        } else {
            echo "<div class='info'>⏭️ Column already exists: admin_registrations.$field</div>";
        }
    }
    
    // Add ban tracking fields to users table
    echo "<h3>3. Adding ban tracking fields to users table...</h3>";
    $usersFields = [
        'banned_at' => "ALTER TABLE users ADD COLUMN banned_at TIMESTAMP NULL COMMENT 'When the user was banned'",
        'banned_until' => "ALTER TABLE users ADD COLUMN banned_until TIMESTAMP NULL COMMENT 'When the ban expires (NULL = permanent)'",
        'ban_duration_days' => "ALTER TABLE users ADD COLUMN ban_duration_days INT NULL COMMENT 'Duration of ban in days (NULL = permanent)'",
        'ban_reason' => "ALTER TABLE users ADD COLUMN ban_reason TEXT NULL COMMENT 'Reason for banning the user'",
        'banned_by' => "ALTER TABLE users ADD COLUMN banned_by INT NULL COMMENT 'Super admin who banned the user'"
    ];
    
    foreach ($usersFields as $field => $sql) {
        if (!columnExists($db, 'users', $field)) {
            try {
                $db->exec($sql);
                $success[] = "users.$field";
                echo "<div class='success'>✅ Added column: users.$field</div>";
            } catch (Exception $e) {
                $errors[] = "users.$field: " . $e->getMessage();
                echo "<div class='error'>❌ Error adding users.$field: " . $e->getMessage() . "</div>";
            }
        } else {
            echo "<div class='info'>⏭️ Column already exists: users.$field</div>";
        }
    }
    
    // Add indexes
    echo "<h3>4. Adding indexes...</h3>";
    $indexes = [
        ['store_locations', 'idx_banned_at', 'banned_at'],
        ['store_locations', 'idx_banned_until', 'banned_until'],
        ['admin_registrations', 'idx_banned_at', 'banned_at'],
        ['admin_registrations', 'idx_banned_until', 'banned_until'],
        ['users', 'idx_banned_at', 'banned_at'],
        ['users', 'idx_banned_until', 'banned_until']
    ];
    
    foreach ($indexes as $index) {
        list($table, $indexName, $column) = $index;
        $checkStmt = $db->query("
            SELECT COUNT(*) as cnt 
            FROM INFORMATION_SCHEMA.STATISTICS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = '$table' 
            AND INDEX_NAME = '$indexName'
        ");
        $exists = $checkStmt->fetch()['cnt'] > 0;
        
        if (!$exists) {
            try {
                $db->exec("ALTER TABLE $table ADD INDEX $indexName ($column)");
                $success[] = "Index $table.$indexName";
                echo "<div class='success'>✅ Added index: $table.$indexName</div>";
            } catch (Exception $e) {
                $errors[] = "Index $table.$indexName: " . $e->getMessage();
                echo "<div class='error'>❌ Error adding index $table.$indexName: " . $e->getMessage() . "</div>";
            }
        } else {
            echo "<div class='info'>⏭️ Index already exists: $table.$indexName</div>";
        }
    }
    
    if (empty($errors)) {
        $db->commit();
        echo "<div class='success' style='font-size:18px;font-weight:bold;margin-top:20px;'>";
        echo "✅ SUCCESS! All ban tracking fields have been added successfully!";
        echo "</div>";
        echo "<p><strong>Summary:</strong></p>";
        echo "<ul>";
        echo "<li>Columns added: " . count($success) . "</li>";
        echo "<li>Errors: 0</li>";
        echo "</ul>";
    } else {
        $db->rollBack();
        echo "<div class='error' style='font-size:18px;font-weight:bold;margin-top:20px;'>";
        echo "❌ Some errors occurred. Rolling back transaction.";
        echo "</div>";
        echo "<p><strong>Errors:</strong></p>";
        echo "<ul>";
        foreach ($errors as $error) {
            echo "<li>$error</li>";
        }
        echo "</ul>";
    }
    
    // Verify columns exist
    echo "<h3>5. Verification...</h3>";
    echo "<table>";
    echo "<tr><th>Table</th><th>Column</th><th>Status</th></tr>";
    
    $allFields = [
        'store_locations' => ['banned_at', 'banned_until', 'ban_duration_days', 'ban_reason', 'banned_by'],
        'admin_registrations' => ['banned_at', 'banned_until', 'ban_duration_days', 'ban_reason', 'banned_by'],
        'users' => ['banned_at', 'banned_until', 'ban_duration_days', 'ban_reason', 'banned_by']
    ];
    
    $allExist = true;
    foreach ($allFields as $table => $fields) {
        foreach ($fields as $field) {
            $exists = columnExists($db, $table, $field);
            $status = $exists ? "✅ Exists" : "❌ Missing";
            $color = $exists ? "green" : "red";
            echo "<tr><td>$table</td><td>$field</td><td style='color:$color;'>$status</td></tr>";
            if (!$exists) {
                $allExist = false;
            }
        }
    }
    echo "</table>";
    
    if ($allExist) {
        echo "<div class='success' style='font-size:18px;font-weight:bold;margin-top:20px;'>";
        echo "✅ All ban tracking fields are now in place! You can now use the ban functionality.";
        echo "</div>";
        echo "<p><a href='../control-panel/bannedStores' style='padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;display:inline-block;margin-top:20px;'>Go to Banned Stores List</a></p>";
    } else {
        echo "<div class='error' style='font-size:18px;font-weight:bold;margin-top:20px;'>";
        echo "❌ Some columns are still missing. Please check the errors above and try again.";
        echo "</div>";
    }
    
} catch (Exception $e) {
    $db->rollBack();
    echo "<div class='error'>";
    echo "❌ Fatal Error: " . $e->getMessage();
    echo "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "</body></html>";

?>

