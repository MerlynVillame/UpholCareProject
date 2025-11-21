<?php
/**
 * Ban Khayel Admin Account
 * This script ensures that the admin account "khayel" (owner of "kyle store") is properly banned
 * Run this in your browser: http://localhost/UphoCare/database/ban_khayel_admin.php
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

echo "<!DOCTYPE html><html><head><title>Ban Khayel Admin Account</title>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;background:#f5f5f5;}";
echo ".success{color:green;padding:10px;background:#d4edda;border:1px solid #c3e6cb;border-radius:5px;margin:10px 0;}";
echo ".error{color:red;padding:10px;background:#f8d7da;border:1px solid #f5c6cb;border-radius:5px;margin:10px 0;}";
echo ".info{color:blue;padding:10px;background:#d1ecf1;border:1px solid #bee5eb;border-radius:5px;margin:10px 0;}";
echo ".warning{color:orange;padding:10px;background:#fff3cd;border:1px solid #ffc107;border-radius:5px;margin:10px 0;}";
echo "table{border-collapse:collapse;width:100%;margin:20px 0;}";
echo "th,td{border:1px solid #ddd;padding:8px;text-align:left;}";
echo "th{background-color:#4CAF50;color:white;}</style></head><body>";
echo "<h2>Ban Khayel Admin Account (Owner of Kyle Store)</h2>";

try {
    // Step 1: Find kyle store
    echo "<h3>Step 1: Finding 'kyle store'...</h3>";
    $stmt = $db->prepare("SELECT id, store_name, address, status, banned_at FROM store_locations WHERE LOWER(store_name) LIKE '%kyle%' LIMIT 1");
    $stmt->execute();
    $store = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$store) {
        echo "<div class='error'>❌ Kyle store not found in database.</div>";
        echo "<p>Let's list all stores:</p>";
        $allStoresStmt = $db->query("SELECT id, store_name, status FROM store_locations ORDER BY store_name");
        $allStores = $allStoresStmt->fetchAll();
        echo "<table><tr><th>ID</th><th>Store Name</th><th>Status</th></tr>";
        foreach ($allStores as $s) {
            echo "<tr><td>{$s['id']}</td><td>{$s['store_name']}</td><td>{$s['status']}</td></tr>";
        }
        echo "</table>";
        exit;
    }
    
    echo "<div class='success'>✅ Found kyle store: <strong>{$store['store_name']}</strong> (ID: {$store['id']})</div>";
    echo "<p>Store Status: <strong>{$store['status']}</strong></p>";
    echo "<p>Banned At: " . ($store['banned_at'] ?? '<span style="color:red;">NULL</span>') . "</p>";
    
    // Step 2: Find admin account linked to kyle store
    echo "<h3>Step 2: Finding admin account linked to kyle store...</h3>";
    
    // Try to find by business name and address
    $adminStmt = $db->prepare("
        SELECT ar.email, ar.business_name, ar.business_address, ar.registration_status,
               u.id as user_id, u.email as user_email, u.fullname, u.status as user_status
        FROM admin_registrations ar
        LEFT JOIN users u ON ar.email = u.email AND u.role = 'admin'
        WHERE (
            (LOWER(TRIM(ar.business_name)) LIKE LOWER('%kyle%')
             OR LOWER(TRIM(ar.business_name)) LIKE LOWER('%" . str_replace("'", "''", $store['store_name']) . "%'))
            OR (LOWER(TRIM(?)) LIKE LOWER(CONCAT('%', TRIM(ar.business_name), '%'))
                AND ar.business_name IS NOT NULL 
                AND TRIM(ar.business_name) != '')
        )
        LIMIT 1
    ");
    $adminStmt->execute([$store['store_name']]);
    $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        echo "<div class='warning'>⚠️ No admin found by business name. Trying to find by email containing 'khayel'...</div>";
        $adminStmt2 = $db->prepare("
            SELECT ar.email, ar.business_name, ar.business_address, ar.registration_status,
                   u.id as user_id, u.email as user_email, u.fullname, u.status as user_status
            FROM admin_registrations ar
            LEFT JOIN users u ON ar.email = u.email AND u.role = 'admin'
            WHERE LOWER(ar.email) LIKE '%khayel%'
               OR LOWER(u.email) LIKE '%khayel%'
               OR LOWER(u.fullname) LIKE '%khayel%'
            LIMIT 1
        ");
        $adminStmt2->execute();
        $admin = $adminStmt2->fetch(PDO::FETCH_ASSOC);
    }
    
    if (!$admin) {
        echo "<div class='error'>❌ Admin account not found. Listing all admins:</div>";
        $allAdminsStmt = $db->query("
            SELECT ar.email, ar.business_name, u.id, u.fullname, u.status
            FROM admin_registrations ar
            LEFT JOIN users u ON ar.email = u.email AND u.role = 'admin'
            ORDER BY ar.email
        ");
        $allAdmins = $allAdminsStmt->fetchAll();
        echo "<table><tr><th>Email</th><th>Business Name</th><th>User ID</th><th>Fullname</th><th>Status</th></tr>";
        foreach ($allAdmins as $a) {
            echo "<tr><td>{$a['email']}</td><td>{$a['business_name']}</td><td>{$a['id']}</td><td>{$a['fullname']}</td><td>{$a['status']}</td></tr>";
        }
        echo "</table>";
        exit;
    }
    
    echo "<div class='success'>✅ Found admin account:</div>";
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>Email</td><td><strong>{$admin['email']}</strong></td></tr>";
    echo "<tr><td>Business Name</td><td>{$admin['business_name']}</td></tr>";
    echo "<tr><td>User ID</td><td>{$admin['user_id']}</td></tr>";
    echo "<tr><td>Fullname</td><td>{$admin['fullname']}</td></tr>";
    echo "<tr><td>User Status</td><td><strong style='color:" . ($admin['user_status'] === 'inactive' ? 'red' : 'green') . ";'>{$admin['user_status']}</strong></td></tr>";
    echo "<tr><td>Registration Status</td><td>{$admin['registration_status']}</td></tr>";
    echo "</table>";
    
    // Step 3: Check if store is banned
    echo "<h3>Step 3: Checking if store is banned...</h3>";
    $storeIsBanned = ($store['status'] === 'inactive' || !empty($store['banned_at']));
    
    if ($storeIsBanned) {
        echo "<div class='warning'>⚠️ Store is banned (status: {$store['status']}, banned_at: " . ($store['banned_at'] ?? 'NULL') . ")</div>";
    } else {
        echo "<div class='info'>ℹ️ Store is NOT banned yet. Status: {$store['status']}</div>";
    }
    
    // Step 4: Ban the admin account
    echo "<h3>Step 4: Banning admin account...</h3>";
    
    $db->beginTransaction();
    
    // Get super admin ID for banned_by
    $superAdminStmt = $db->query("SELECT id FROM control_panel_admins WHERE role = 'super_admin' LIMIT 1");
    $superAdmin = $superAdminStmt->fetch();
    $superAdminId = $superAdmin ? $superAdmin['id'] : 1;
    
    // Check if ban columns exist
    $hasUserBanColumns = false;
    $hasAdminRegBanColumns = false;
    try {
        $checkUserStmt = $db->query("
            SELECT COUNT(*) as cnt 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'users' 
            AND COLUMN_NAME = 'banned_at'
        ");
        $hasUserBanColumns = $checkUserStmt->fetch()['cnt'] > 0;
        
        $checkAdminRegStmt = $db->query("
            SELECT COUNT(*) as cnt 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = DATABASE() 
            AND TABLE_NAME = 'admin_registrations' 
            AND COLUMN_NAME = 'banned_at'
        ");
        $hasAdminRegBanColumns = $checkAdminRegStmt->fetch()['cnt'] > 0;
    } catch (Exception $e) {
        echo "<div class='warning'>⚠️ Could not check for ban columns: " . $e->getMessage() . "</div>";
    }
    
    // Ban user account
    if ($admin['user_id']) {
        if ($hasUserBanColumns) {
            $banReason = 'Store (kyle store) has been banned';
            $updateUserStmt = $db->prepare("
                UPDATE users 
                SET status = 'inactive',
                    banned_at = NOW(),
                    banned_until = NULL,
                    ban_duration_days = NULL,
                    ban_reason = ?,
                    banned_by = ?,
                    updated_at = NOW()
                WHERE id = ?
            ");
            $updateUserStmt->execute([$banReason, $superAdminId, $admin['user_id']]);
            echo "<div class='success'>✅ Updated users table (with ban tracking columns)</div>";
        } else {
            $updateUserStmt = $db->prepare("
                UPDATE users 
                SET status = 'inactive',
                    updated_at = NOW()
                WHERE id = ?
            ");
            $updateUserStmt->execute([$admin['user_id']]);
            echo "<div class='success'>✅ Updated users table (status only - ban columns don't exist)</div>";
        }
    }
    
    // Ban admin_registrations
    if ($hasAdminRegBanColumns) {
        $banReason = 'Store (kyle store) has been banned';
        $updateAdminRegStmt = $db->prepare("
            UPDATE admin_registrations 
            SET registration_status = 'banned',
                banned_at = NOW(),
                banned_until = NULL,
                ban_duration_days = NULL,
                ban_reason = ?,
                banned_by = ?,
                updated_at = NOW()
            WHERE email = ?
        ");
        $updateAdminRegStmt->execute([$banReason, $superAdminId, $admin['email']]);
        echo "<div class='success'>✅ Updated admin_registrations table (with ban tracking columns)</div>";
    } else {
        $updateAdminRegStmt = $db->prepare("
            UPDATE admin_registrations 
            SET registration_status = 'banned',
                updated_at = NOW()
            WHERE email = ?
        ");
        $updateAdminRegStmt->execute([$admin['email']]);
        echo "<div class='success'>✅ Updated admin_registrations table (status only - ban columns don't exist)</div>";
    }
    
    $db->commit();
    
    // Step 5: Verify the ban
    echo "<h3>Step 5: Verifying the ban...</h3>";
    $verifyStmt = $db->prepare("
        SELECT u.id, u.email, u.fullname, u.status, u.banned_at, u.ban_reason,
               ar.email as reg_email, ar.registration_status, ar.banned_at as reg_banned_at
        FROM users u
        LEFT JOIN admin_registrations ar ON u.email = ar.email
        WHERE u.id = ?
    ");
    $verifyStmt->execute([$admin['user_id']]);
    $verified = $verifyStmt->fetch();
    
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>User ID</td><td>{$verified['id']}</td></tr>";
    echo "<tr><td>Email</td><td><strong>{$verified['email']}</strong></td></tr>";
    echo "<tr><td>Fullname</td><td>{$verified['fullname']}</td></tr>";
    echo "<tr><td>User Status</td><td><strong style='color:red;'>{$verified['status']}</strong></td></tr>";
    echo "<tr><td>Banned At</td><td>" . ($verified['banned_at'] ?? '<span style="color:red;">NULL</span>') . "</td></tr>";
    echo "<tr><td>Ban Reason</td><td>" . ($verified['ban_reason'] ?? 'N/A') . "</td></tr>";
    echo "<tr><td>Registration Status</td><td><strong style='color:red;'>{$verified['registration_status']}</strong></td></tr>";
    echo "<tr><td>Reg Banned At</td><td>" . ($verified['reg_banned_at'] ?? '<span style="color:red;">NULL</span>') . "</td></tr>";
    echo "</table>";
    
    if ($verified['status'] === 'inactive') {
        echo "<div class='success' style='font-size:18px;font-weight:bold;margin-top:20px;'>";
        echo "✅ SUCCESS! Admin account '{$verified['email']}' has been banned and cannot log in!";
        echo "</div>";
        echo "<p><strong>Test:</strong> Try to log in with email: <strong>{$verified['email']}</strong></p>";
        echo "<p>You should see an error message: 'Your account has been banned because your store(s) have been banned. You cannot log in.'</p>";
    } else {
        echo "<div class='error' style='font-size:18px;font-weight:bold;margin-top:20px;'>";
        echo "❌ ERROR! Admin account status is still '{$verified['status']}'. The ban may not have worked.";
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

