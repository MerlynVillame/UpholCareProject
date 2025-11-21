<?php
/**
 * Fix Kyle Store Ban Status
 * This script will:
 * 1. Check if ban tracking fields exist, if not, add them
 * 2. Find the kyle store
 * 3. If it's banned but missing ban info, add the ban information
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

echo "<h2>Fix Kyle Store Ban Status</h2>";

try {
    // Step 1: Check if ban tracking fields exist
    echo "<h3>Step 1: Checking ban tracking fields...</h3>";
    $checkStmt = $db->query("
        SELECT COUNT(*) as cnt 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'store_locations' 
        AND COLUMN_NAME = 'banned_at'
    ");
    $hasBannedAt = $checkStmt->fetch()['cnt'] > 0;
    
    if (!$hasBannedAt) {
        echo "<p style='color: orange;'>⚠️ Ban tracking fields do not exist. Adding them...</p>";
        
        // Add ban tracking fields
        $alterations = [
            "ALTER TABLE store_locations ADD COLUMN banned_at TIMESTAMP NULL COMMENT 'When the store was banned'",
            "ALTER TABLE store_locations ADD COLUMN banned_until TIMESTAMP NULL COMMENT 'When the ban expires (NULL = permanent)'",
            "ALTER TABLE store_locations ADD COLUMN ban_duration_days INT NULL COMMENT 'Duration of ban in days (NULL = permanent)'",
            "ALTER TABLE store_locations ADD COLUMN ban_reason TEXT NULL COMMENT 'Reason for banning the store'",
            "ALTER TABLE store_locations ADD COLUMN banned_by INT NULL COMMENT 'Super admin who banned the store'"
        ];
        
        foreach ($alterations as $sql) {
            try {
                $db->exec($sql);
                echo "<p style='color: green;'>✅ Added column: " . preg_match("/ADD COLUMN (\w+)/", $sql, $matches) ? $matches[1] : 'column' . "</p>";
            } catch (Exception $e) {
                echo "<p style='color: red;'>❌ Error adding column: " . $e->getMessage() . "</p>";
            }
        }
        
        echo "<p style='color: green;'>✅ Ban tracking fields added!</p>";
    } else {
        echo "<p style='color: green;'>✅ Ban tracking fields already exist.</p>";
    }
    
    // Step 2: Find kyle store
    echo "<h3>Step 2: Finding kyle store...</h3>";
    $stmt = $db->prepare("
        SELECT 
            id,
            store_name,
            address,
            status,
            banned_at,
            banned_until,
            ban_duration_days,
            ban_reason,
            banned_by
        FROM store_locations 
        WHERE LOWER(store_name) LIKE '%kyle%' 
           OR LOWER(store_name) = 'kyle store'
        ORDER BY id DESC
        LIMIT 1
    ");
    $stmt->execute();
    $store = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$store) {
        echo "<p style='color: red;'>❌ Kyle store not found in database.</p>";
        
        // List all stores
        echo "<h4>All stores in database:</h4>";
        $allStmt = $db->query("SELECT id, store_name, status FROM store_locations ORDER BY id DESC LIMIT 10");
        $allStores = $allStmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<ul>";
        foreach ($allStores as $s) {
            echo "<li>ID: {$s['id']} - Name: {$s['store_name']} - Status: {$s['status']}</li>";
        }
        echo "</ul>";
        exit;
    }
    
    echo "<p style='color: green;'>✅ Found kyle store: <strong>{$store['store_name']}</strong> (ID: {$store['id']})</p>";
    echo "<p>Current status: <strong>{$store['status']}</strong></p>";
    echo "<p>Banned at: " . ($store['banned_at'] ?? '<span style="color: red;">NULL</span>') . "</p>";
    
    // Step 3: Check if store should be marked as banned
    echo "<h3>Step 3: Checking ban status...</h3>";
    
    // Get super admin ID for banned_by
    $superAdminStmt = $db->query("SELECT id FROM control_panel_admins WHERE role = 'super_admin' LIMIT 1");
    $superAdmin = $superAdminStmt->fetch();
    $superAdminId = $superAdmin ? $superAdmin['id'] : 1;
    
    $needsUpdate = false;
    $updateReason = '';
    
    // If status is inactive but banned_at is NULL, we need to set it
    if ($store['status'] == 'inactive' && (empty($store['banned_at']) || $store['banned_at'] === null)) {
        echo "<p style='color: orange;'>⚠️ Store status is 'inactive' but banned_at is NULL. Store should be marked as banned.</p>";
        $needsUpdate = true;
        $updateReason = 'Store is inactive but missing ban information';
    }
    // If status is active, assume it should be banned (user said it's banned)
    elseif ($store['status'] == 'active') {
        echo "<p style='color: orange;'>⚠️ Store status is 'active' but you mentioned it's banned. Setting it as banned.</p>";
        $needsUpdate = true;
        $updateReason = 'Store marked as banned by user request';
    }
    // If banned_at exists but status is active, fix the status
    elseif (!empty($store['banned_at']) && $store['status'] == 'active') {
        echo "<p style='color: orange;'>⚠️ Store has ban info but status is 'active'. Setting status to 'inactive'.</p>";
        $needsUpdate = true;
        $updateReason = 'Store has ban info but incorrect status';
    }
    
    // Always update if store is inactive (user confirmed it's banned)
    if ($store['status'] == 'inactive') {
        if (empty($store['banned_at']) || $store['banned_at'] === null) {
            $needsUpdate = true;
            $updateReason = 'Store is inactive and should have ban information';
        }
    }
    
    if ($needsUpdate) {
        echo "<h3>Step 4: Updating store ban status...</h3>";
        
        // Build UPDATE query using prepared statement
        $updateSql = "UPDATE store_locations SET 
            status = 'inactive',
            banned_at = NOW(),
            banned_by = ?,
            ban_reason = ?,
            banned_until = NULL,
            ban_duration_days = NULL,
            updated_at = NOW()
            WHERE id = ?";
        
        // Use existing ban_reason or set a default
        $banReason = !empty($store['ban_reason']) && $store['ban_reason'] !== null 
            ? $store['ban_reason'] 
            : 'Store banned due to low ratings or policy violation';
        
        echo "<p><strong>Update Reason:</strong> {$updateReason}</p>";
        echo "<p>Updating store with:</p>";
        echo "<ul>";
        echo "<li>Status: <strong>inactive</strong></li>";
        echo "<li>Banned At: NOW() (will be set)</li>";
        echo "<li>Banned By: {$superAdminId}</li>";
        echo "<li>Ban Reason: {$banReason}</li>";
        echo "<li>Ban Duration: Permanent (NULL)</li>";
        echo "</ul>";
        
        try {
            $db->beginTransaction();
            $updateStmt = $db->prepare($updateSql);
            $updateStmt->execute([$superAdminId, $banReason, $store['id']]);
            $db->commit();
            
            echo "<p style='color: green;'><strong>✅ Store ban status updated successfully!</strong></p>";
            echo "<p>Store should now appear in the banned stores list.</p>";
            
            // Verify the update
            $verifyStmt = $db->prepare("
                SELECT id, store_name, status, banned_at, ban_reason, banned_by 
                FROM store_locations 
                WHERE id = ?
            ");
            $verifyStmt->execute([$store['id']]);
            $updatedStore = $verifyStmt->fetch(PDO::FETCH_ASSOC);
            
            echo "<h4>Updated Store Information:</h4>";
            echo "<table border='1' cellpadding='10' style='border-collapse: collapse; margin: 20px 0; width: 100%;'>";
            echo "<tr><th>Field</th><th>Value</th></tr>";
            echo "<tr><td>Store Name</td><td><strong>{$updatedStore['store_name']}</strong></td></tr>";
            echo "<tr><td>Status</td><td><strong style='color: red;'>{$updatedStore['status']}</strong></td></tr>";
            echo "<tr><td>Banned At</td><td><strong>{$updatedStore['banned_at']}</strong></td></tr>";
            echo "<tr><td>Ban Reason</td><td>{$updatedStore['ban_reason']}</td></tr>";
            echo "<tr><td>Banned By</td><td>{$updatedStore['banned_by']}</td></tr>";
            echo "</table>";
            
            // Test if it would appear in banned list
            $testStmt = $db->prepare("
                SELECT id, store_name, status, banned_at
                FROM store_locations 
                WHERE id = ? 
                    AND status = 'inactive' 
                    AND banned_at IS NOT NULL
            ");
            $testStmt->execute([$store['id']]);
            $testResult = $testStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($testResult) {
                echo "<p style='color: green; font-size: 18px; padding: 15px; background: #d4edda; border: 2px solid #28a745; border-radius: 5px;'><strong>✅ SUCCESS! Store now matches the banned stores query criteria and will appear in the banned list!</strong></p>";
            } else {
                echo "<p style='color: orange;'>⚠️ Store updated but may not match query criteria. Please check manually.</p>";
            }
            
            echo "<p style='margin-top: 30px;'>";
            echo "<a href='../control-panel/bannedStores' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-right: 10px; font-weight: bold;'>Go to Banned Stores List</a>";
            echo "<a href='fix_kyle_store_ban.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px; display: inline-block;'>Refresh This Page</a>";
            echo "</p>";
            
        } catch (Exception $e) {
            $db->rollBack();
            echo "<p style='color: red;'>❌ Error updating store: " . $e->getMessage() . "</p>";
            echo "<pre>" . $e->getTraceAsString() . "</pre>";
        }
    } else {
        if ($store['status'] == 'inactive' && !empty($store['banned_at'])) {
            echo "<p style='color: green;'>✅ Store is already properly marked as banned and should appear in the banned list.</p>";
            echo "<p>If it's not showing up, there might be an issue with the query. Let's verify:</p>";
            
            // Test the banned stores query
            $testStmt = $db->prepare("
                SELECT id, store_name, status, banned_at
                FROM store_locations 
                WHERE id = ? 
                    AND status = 'inactive' 
                    AND banned_at IS NOT NULL
            ");
            $testStmt->execute([$store['id']]);
            $testResult = $testStmt->fetch(PDO::FETCH_ASSOC);
            
            if ($testResult) {
                echo "<p style='color: green;'>✅ Store matches the banned stores query criteria. It should appear in the list.</p>";
            } else {
                echo "<p style='color: red;'>❌ Store does NOT match the banned stores query criteria. There might be a data type issue.</p>";
            }
        } else {
            echo "<p style='color: orange;'>ℹ️ Store status: {$store['status']}, Banned at: " . ($store['banned_at'] ?? 'NULL') . "</p>";
            echo "<p>Store is not currently marked as banned. If you want to ban it, you can do so from the Store Ratings page.</p>";
        }
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

?>

