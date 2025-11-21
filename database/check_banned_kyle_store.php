<?php
/**
 * Check Kyle Store Ban Status
 * This script checks the status of the kyle store in the database
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

echo "<h2>Check Kyle Store Ban Status</h2>";

try {
    // Check if kyle store exists
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
            banned_by,
            rating,
            created_at,
            updated_at
        FROM store_locations 
        WHERE LOWER(store_name) LIKE '%kyle%' 
           OR LOWER(store_name) = 'kyle store'
        ORDER BY id DESC
    ");
    $stmt->execute();
    $stores = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($stores)) {
        echo "<p style='color: red;'>❌ No stores found with 'kyle' in the name.</p>";
        
        // List all stores
        echo "<h3>All Stores in Database:</h3>";
        $allStmt = $db->query("SELECT id, store_name, status, banned_at FROM store_locations ORDER BY id DESC LIMIT 20");
        $allStores = $allStmt->fetchAll(PDO::FETCH_ASSOC);
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Store Name</th><th>Status</th><th>Banned At</th></tr>";
        foreach ($allStores as $store) {
            echo "<tr>";
            echo "<td>{$store['id']}</td>";
            echo "<td>{$store['store_name']}</td>";
            echo "<td>{$store['status']}</td>";
            echo "<td>" . ($store['banned_at'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: green;'>✅ Found " . count($stores) . " store(s) with 'kyle' in the name.</p>";
        
        foreach ($stores as $store) {
            echo "<h3>Store: {$store['store_name']} (ID: {$store['id']})</h3>";
            echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%; margin-bottom: 20px;'>";
            echo "<tr><th>Field</th><th>Value</th></tr>";
            echo "<tr><td>Store Name</td><td>{$store['store_name']}</td></tr>";
            echo "<tr><td>Address</td><td>{$store['address']}</td></tr>";
            echo "<tr><td>Status</td><td><strong>{$store['status']}</strong></td></tr>";
            echo "<tr><td>Banned At</td><td>" . ($store['banned_at'] ?? '<span style="color: red;">NULL</span>') . "</td></tr>";
            echo "<tr><td>Banned Until</td><td>" . ($store['banned_until'] ?? 'NULL') . "</td></tr>";
            echo "<tr><td>Ban Duration (Days)</td><td>" . ($store['ban_duration_days'] ?? 'NULL') . "</td></tr>";
            echo "<tr><td>Ban Reason</td><td>" . ($store['ban_reason'] ?? 'NULL') . "</td></tr>";
            echo "<tr><td>Banned By</td><td>" . ($store['banned_by'] ?? 'NULL') . "</td></tr>";
            echo "<tr><td>Rating</td><td>{$store['rating']}</td></tr>";
            echo "<tr><td>Created At</td><td>{$store['created_at']}</td></tr>";
            echo "<tr><td>Updated At</td><td>{$store['updated_at']}</td></tr>";
            echo "</table>";
            
            // Check if it should appear in banned list
            $shouldAppear = ($store['status'] == 'inactive' && !empty($store['banned_at']));
            if ($shouldAppear) {
                echo "<p style='color: green;'>✅ This store SHOULD appear in the banned list.</p>";
            } else {
                echo "<p style='color: orange;'>⚠️ This store does NOT meet the criteria to appear in the banned list.</p>";
                echo "<p><strong>Required:</strong> status = 'inactive' AND banned_at IS NOT NULL</p>";
                echo "<p><strong>Current:</strong> status = '{$store['status']}' AND banned_at = " . ($store['banned_at'] ?? 'NULL') . "</p>";
                
                // Offer to fix it
                if ($store['status'] != 'inactive' || empty($store['banned_at'])) {
                    echo "<h4>Fix Options:</h4>";
                    echo "<form method='POST' style='margin-top: 20px;'>";
                    echo "<input type='hidden' name='store_id' value='{$store['id']}'>";
                    
                    if ($store['status'] != 'inactive') {
                        echo "<p><strong>Issue:</strong> Status is '{$store['status']}' but should be 'inactive'</p>";
                        echo "<button type='submit' name='action' value='set_inactive' style='padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer;'>Set Status to 'inactive'</button>";
                    }
                    
                    if (empty($store['banned_at'])) {
                        echo "<p><strong>Issue:</strong> banned_at is NULL</p>";
                        echo "<button type='submit' name='action' value='set_banned' style='padding: 10px 20px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;'>Set banned_at to NOW()</button>";
                    }
                    
                    echo "</form>";
                }
            }
        }
    }
    
    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && isset($_POST['store_id'])) {
        $storeId = intval($_POST['store_id']);
        $action = $_POST['action'];
        
        echo "<hr><h3>Processing Fix...</h3>";
        
        try {
            $db->beginTransaction();
            
            if ($action === 'set_inactive') {
                $stmt = $db->prepare("UPDATE store_locations SET status = 'inactive', updated_at = NOW() WHERE id = ?");
                $stmt->execute([$storeId]);
                echo "<p style='color: green;'>✅ Status set to 'inactive'</p>";
            }
            
            if ($action === 'set_banned') {
                // Get a super admin ID (or use 1 as default)
                $superAdminStmt = $db->query("SELECT id FROM control_panel_admins WHERE role = 'super_admin' LIMIT 1");
                $superAdmin = $superAdminStmt->fetch();
                $superAdminId = $superAdmin ? $superAdmin['id'] : 1;
                
                $stmt = $db->prepare("
                    UPDATE store_locations 
                    SET banned_at = NOW(),
                        ban_reason = 'Manually marked as banned',
                        banned_by = ?,
                        status = 'inactive',
                        updated_at = NOW()
                    WHERE id = ?
                ");
                $stmt->execute([$superAdminId, $storeId]);
                echo "<p style='color: green;'>✅ banned_at set to NOW() and status set to 'inactive'</p>";
            }
            
            $db->commit();
            echo "<p style='color: green;'><strong>✅ Fix applied successfully! Refresh the page to see the updated status.</strong></p>";
            echo "<p><a href='check_banned_kyle_store.php' style='padding: 10px 20px; background: #007bff; color: white; text-decoration: none; border-radius: 5px;'>Refresh Page</a></p>";
            echo "<p><a href='../control-panel/bannedStores' style='padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-left: 10px;'>Go to Banned Stores List</a></p>";
            
        } catch (Exception $e) {
            $db->rollBack();
            echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
        }
    }
    
    // Check what the banned stores query would return
    echo "<hr><h3>Banned Stores Query Test</h3>";
    $bannedStmt = $db->query("
        SELECT 
            id,
            store_name,
            status,
            banned_at
        FROM store_locations 
        WHERE status = 'inactive' 
            AND banned_at IS NOT NULL
        ORDER BY banned_at DESC
    ");
    $bannedStores = $bannedStmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "<p>Stores that WOULD appear in banned list: " . count($bannedStores) . "</p>";
    if (!empty($bannedStores)) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr><th>ID</th><th>Store Name</th><th>Status</th><th>Banned At</th></tr>";
        foreach ($bannedStores as $store) {
            echo "<tr>";
            echo "<td>{$store['id']}</td>";
            echo "<td>{$store['store_name']}</td>";
            echo "<td>{$store['status']}</td>";
            echo "<td>{$store['banned_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>⚠️ No stores match the banned stores query criteria.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

?>

