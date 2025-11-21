<?php
/**
 * Ban Khayel Admin Account NOW
 * This script immediately bans the admin account khayelmagallen@gmail.com (owner of kyle store)
 * Run this in your browser: http://localhost/UphoCare/database/ban_khayel_admin_now.php
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

echo "<!DOCTYPE html><html><head><title>Ban Khayel Admin Account NOW</title>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;background:#f5f5f5;}";
echo ".success{color:green;padding:10px;background:#d4edda;border:1px solid #c3e6cb;border-radius:5px;margin:10px 0;}";
echo ".error{color:red;padding:10px;background:#f8d7da;border:1px solid #f5c6cb;border-radius:5px;margin:10px 0;}";
echo ".info{color:blue;padding:10px;background:#d1ecf1;border:1px solid #bee5eb;border-radius:5px;margin:10px 0;}";
echo "table{border-collapse:collapse;width:100%;margin:20px 0;}";
echo "th,td{border:1px solid #ddd;padding:8px;text-align:left;}";
echo "th{background-color:#4CAF50;color:white;}</style></head><body>";
echo "<h2>Ban Khayel Admin Account (khayelmagallen@gmail.com)</h2>";

try {
    $email = 'khayelmagallen@gmail.com';
    
    // Step 1: Find admin by email
    echo "<h3>Step 1: Finding admin account by email: {$email}</h3>";
    
    $adminStmt = $db->prepare("
        SELECT ar.email, ar.business_name, ar.business_address, ar.registration_status,
               u.id as user_id, u.email as user_email, u.fullname, u.status as user_status, u.role
        FROM admin_registrations ar
        LEFT JOIN users u ON ar.email = u.email AND u.role = 'admin'
        WHERE LOWER(ar.email) = LOWER(?)
        LIMIT 1
    ");
    $adminStmt->execute([$email]);
    $admin = $adminStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$admin) {
        echo "<div class='error'>❌ Admin account not found with email: {$email}</div>";
        
        // Try to find by partial email
        echo "<p>Trying to find by partial email match...</p>";
        $partialStmt = $db->prepare("
            SELECT ar.email, u.id, u.fullname, u.status
            FROM admin_registrations ar
            LEFT JOIN users u ON ar.email = u.email
            WHERE LOWER(ar.email) LIKE '%khayel%'
               OR LOWER(u.email) LIKE '%khayel%'
        ");
        $partialStmt->execute();
        $partialAdmins = $partialStmt->fetchAll();
        
        if (!empty($partialAdmins)) {
            echo "<p>Found similar accounts:</p>";
            echo "<table><tr><th>Email</th><th>User ID</th><th>Fullname</th><th>Status</th></tr>";
            foreach ($partialAdmins as $a) {
                echo "<tr><td>{$a['email']}</td><td>{$a['id']}</td><td>{$a['fullname']}</td><td>{$a['status']}</td></tr>";
            }
            echo "</table>";
        }
        exit;
    }
    
    echo "<div class='success'>✅ Found admin account:</div>";
    echo "<table>";
    echo "<tr><th>Field</th><th>Value</th></tr>";
    echo "<tr><td>Email</td><td><strong>{$admin['email']}</strong></td></tr>";
    echo "<tr><td>Business Name</td><td>{$admin['business_name']}</td></tr>";
    echo "<tr><td>User ID</td><td>{$admin['user_id']}</td></tr>";
    echo "<tr><td>Fullname</td><td>{$admin['fullname']}</td></tr>";
    echo "<tr><td>Current Status</td><td><strong style='color:" . ($admin['user_status'] === 'inactive' ? 'red' : 'green') . ";'>{$admin['user_status']}</strong></td></tr>";
    echo "<tr><td>Registration Status</td><td>{$admin['registration_status']}</td></tr>";
    echo "</table>";
    
    // Step 2: Check if kyle store is banned
    echo "<h3>Step 2: Checking if kyle store is banned...</h3>";
    $storeStmt = $db->prepare("
        SELECT id, store_name, status, banned_at 
        FROM store_locations 
        WHERE LOWER(store_name) LIKE '%kyle%'
        LIMIT 1
    ");
    $storeStmt->execute();
    $store = $storeStmt->fetch(PDO::FETCH_ASSOC);
    
    if ($store) {
        $storeIsBanned = ($store['status'] === 'inactive' || !empty($store['banned_at']));
        echo "<div class='info'>Store: <strong>{$store['store_name']}</strong> - Status: <strong>{$store['status']}</strong> - Banned: " . ($storeIsBanned ? 'YES' : 'NO') . "</div>";
    } else {
        echo "<div class='warning'>⚠️ Kyle store not found, but proceeding to ban admin anyway...</div>";
    }
    
    // Step 3: Ban the admin account
    echo "<h3>Step 3: Banning admin account...</h3>";
    
    $db->beginTransaction();
    
    // Get super admin ID for banned_by
    $superAdminStmt = $db->query("SELECT id FROM control_panel_admins WHERE role = 'super_admin' LIMIT 1");
    $superAdmin = $superAdminStmt->fetch();
    $superAdminId = $superAdmin ? $superAdmin['id'] : 1;
    
    $banReason = 'Store (kyle store) has been banned';
    
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
            echo "<div class='success'>✅ Banned user account (with ban tracking)</div>";
        } else {
            $updateUserStmt = $db->prepare("
                UPDATE users 
                SET status = 'inactive',
                    updated_at = NOW()
                WHERE id = ?
            ");
            $updateUserStmt->execute([$admin['user_id']]);
            echo "<div class='success'>✅ Banned user account (status only)</div>";
        }
    } else {
        echo "<div class='error'>❌ No user_id found. Admin might not have a users table entry.</div>";
    }
    
    // Ban admin_registrations
    if ($hasAdminRegBanColumns) {
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
        $updateAdminRegStmt->execute([$banReason, $superAdminId, $email]);
        echo "<div class='success'>✅ Banned admin_registrations (with ban tracking)</div>";
    } else {
        $updateAdminRegStmt = $db->prepare("
            UPDATE admin_registrations 
            SET registration_status = 'banned',
                updated_at = NOW()
            WHERE email = ?
        ");
        $updateAdminRegStmt->execute([$email]);
        echo "<div class='success'>✅ Banned admin_registrations (status only)</div>";
    }
    
    $db->commit();
    
    // Step 4: Verify the ban
    echo "<h3>Step 4: Verifying the ban...</h3>";
    $verifyStmt = $db->prepare("
        SELECT u.id, u.email, u.fullname, u.status, u.banned_at, u.ban_reason,
               ar.email as reg_email, ar.registration_status, ar.banned_at as reg_banned_at
        FROM users u
        LEFT JOIN admin_registrations ar ON u.email = ar.email
        WHERE LOWER(u.email) = LOWER(?)
           OR LOWER(ar.email) = LOWER(?)
    ");
    $verifyStmt->execute([$email, $email]);
    $verified = $verifyStmt->fetch();
    
    if ($verified) {
        echo "<table>";
        echo "<tr><th>Field</th><th>Value</th></tr>";
        echo "<tr><td>Email</td><td><strong>{$verified['email']}</strong></td></tr>";
        echo "<tr><td>Fullname</td><td>{$verified['fullname']}</td></tr>";
        echo "<tr><td>User Status</td><td><strong style='color:red;font-size:18px;'>{$verified['status']}</strong></td></tr>";
        echo "<tr><td>Banned At</td><td>" . ($verified['banned_at'] ?? '<span style="color:red;">NULL</span>') . "</td></tr>";
        echo "<tr><td>Ban Reason</td><td>" . ($verified['ban_reason'] ?? 'N/A') . "</td></tr>";
        echo "<tr><td>Registration Status</td><td><strong style='color:red;'>{$verified['registration_status']}</strong></td></tr>";
        echo "<tr><td>Reg Banned At</td><td>" . ($verified['reg_banned_at'] ?? '<span style="color:red;">NULL</span>') . "</td></tr>";
        echo "</table>";
        
        if ($verified['status'] === 'inactive') {
            echo "<div class='success' style='font-size:20px;font-weight:bold;margin-top:20px;padding:20px;background:#d4edda;border:3px solid #28a745;'>";
            echo "✅ SUCCESS! Admin account '{$verified['email']}' has been BANNED!<br>";
            echo "The admin CANNOT log in to the system anymore.";
            echo "</div>";
        } else {
            echo "<div class='error' style='font-size:18px;font-weight:bold;margin-top:20px;'>";
            echo "❌ ERROR! Admin account status is still '{$verified['status']}'. The ban may not have worked.";
            echo "</div>";
        }
    } else {
        echo "<div class='error'>❌ Could not verify the ban. Admin account might not exist.</div>";
    }
    
    // Step 5: Test login check
    echo "<h3>Step 5: Testing login check...</h3>";
    $loginCheckStmt = $db->prepare("
        SELECT COUNT(*) as count
        FROM store_locations sl
        INNER JOIN admin_registrations ar ON (
            ar.email = ?
            AND (
                (LOWER(TRIM(sl.store_name)) = LOWER(TRIM(ar.business_name)) 
                 AND LOWER(TRIM(sl.address)) = LOWER(TRIM(ar.business_address)))
                OR (LOWER(TRIM(sl.store_name)) LIKE LOWER(CONCAT('%', TRIM(ar.business_name), '%'))
                    AND ar.business_name IS NOT NULL 
                    AND TRIM(ar.business_name) != '')
            )
        )
        WHERE sl.status = 'inactive'
    ");
    $loginCheckStmt->execute([$email]);
    $bannedStoresCount = $loginCheckStmt->fetch()['count'];
    
    echo "<div class='info'>Found <strong>{$bannedStoresCount}</strong> banned store(s) for this admin.</div>";
    echo "<p><strong>When this admin tries to log in, they will see:</strong></p>";
    echo "<div style='padding:15px;background:#fff3cd;border:2px solid #ffc107;border-radius:5px;margin:10px 0;'>";
    echo "<strong style='color:red;'>Error Message:</strong><br>";
    echo "'Your account has been banned because your store(s) have been banned. You cannot log in. Please contact the administrator.'";
    echo "</div>";
    
} catch (Exception $e) {
    $db->rollBack();
    echo "<div class='error'>";
    echo "❌ Fatal Error: " . $e->getMessage();
    echo "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<p style='margin-top:30px;'>";
echo "<a href='../control-panel/bannedStores' style='padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;display:inline-block;margin-right:10px;font-weight:bold;'>Go to Banned Stores List</a>";
echo "<a href='ban_khayel_admin_now.php' style='padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;display:inline-block;'>Refresh This Page</a>";
echo "</p>";

echo "</body></html>";

?>

