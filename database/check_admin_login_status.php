<?php
/**
 * Check Admin Login Status
 * This script checks why an admin cannot log in even after being unbanned
 * Run this in your browser: http://localhost/UphoCare/database/check_admin_login_status.php
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

echo "<!DOCTYPE html><html><head><title>Check Admin Login Status</title>";
echo "<style>body{font-family:Arial,sans-serif;padding:20px;background:#f5f5f5;}";
echo ".success{color:green;padding:10px;background:#d4edda;border:1px solid #c3e6cb;border-radius:5px;margin:10px 0;}";
echo ".error{color:red;padding:10px;background:#f8d7da;border:1px solid #f5c6cb;border-radius:5px;margin:10px 0;}";
echo ".info{color:blue;padding:10px;background:#d1ecf1;border:1px solid #bee5eb;border-radius:5px;margin:10px 0;}";
echo ".warning{color:orange;padding:10px;background:#fff3cd;border:1px solid #ffc107;border-radius:5px;margin:10px 0;}";
echo "table{border-collapse:collapse;width:100%;margin:20px 0;}";
echo "th,td{border:1px solid #ddd;padding:8px;text-align:left;}";
echo "th{background-color:#4CAF50;color:white;}</style></head><body>";
echo "<h2>Check Admin Login Status</h2>";

try {
    // Step 1: List all admin accounts
    echo "<h3>Step 1: Listing all admin accounts...</h3>";
    $adminsStmt = $db->query("
        SELECT u.id, u.email, u.fullname, u.status as user_status, u.role,
               u.banned_at, u.ban_reason, u.banned_by,
               ar.email as reg_email, ar.registration_status, ar.banned_at as reg_banned_at
        FROM users u
        LEFT JOIN admin_registrations ar ON u.email = ar.email
        WHERE u.role = 'admin'
        ORDER BY u.email
    ");
    $admins = $adminsStmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($admins)) {
        echo "<div class='warning'>⚠️ No admin accounts found in the system.</div>";
        exit;
    }
    
    echo "<table>";
    echo "<tr><th>ID</th><th>Email</th><th>Fullname</th><th>User Status</th><th>Registration Status</th><th>Banned At (User)</th><th>Banned At (Reg)</th><th>Actions</th></tr>";
    foreach ($admins as $admin) {
        $statusColor = $admin['user_status'] === 'active' ? 'green' : 'red';
        $regStatusColor = ($admin['registration_status'] ?? '') === 'approved' ? 'green' : (($admin['registration_status'] ?? '') === 'banned' ? 'red' : 'orange');
        echo "<tr>";
        echo "<td>{$admin['id']}</td>";
        echo "<td><strong>{$admin['email']}</strong></td>";
        echo "<td>{$admin['fullname']}</td>";
        echo "<td style='color:{$statusColor};font-weight:bold;'>{$admin['user_status']}</td>";
        echo "<td style='color:{$regStatusColor};font-weight:bold;'>{$admin['registration_status']}</td>";
        echo "<td>" . ($admin['banned_at'] ?? '<span style="color:green;">NULL</span>') . "</td>";
        echo "<td>" . ($admin['reg_banned_at'] ?? '<span style="color:green;">NULL</span>') . "</td>";
        echo "<td>";
        if ($admin['user_status'] === 'inactive') {
            echo "<a href='?fix_admin=" . urlencode($admin['email']) . "' style='padding:5px 10px;background:#28a745;color:white;text-decoration:none;border-radius:3px;'>Fix (Set to Active)</a>";
        }
        echo "</td>";
        echo "</tr>";
    }
    echo "</table>";
    
    // Step 2: Check for banned stores linked to each admin
    echo "<h3>Step 2: Checking for banned stores linked to admins...</h3>";
    foreach ($admins as $admin) {
        $bannedStoresStmt = $db->prepare("
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
                    OR (LOWER(TRIM(ar.business_name)) LIKE LOWER(CONCAT('%', TRIM(sl.store_name), '%'))
                        AND sl.store_name IS NOT NULL 
                        AND TRIM(sl.store_name) != '')
                )
            )
            WHERE sl.status = 'inactive'
                AND (sl.banned_at IS NOT NULL OR sl.banned_at != '')
        ");
        $bannedStoresStmt->execute([$admin['email']]);
        $bannedStoresCount = $bannedStoresStmt->fetch()['count'];
        
        if ($bannedStoresCount > 0) {
            echo "<div class='warning'>⚠️ Admin <strong>{$admin['email']}</strong> has <strong>{$bannedStoresCount}</strong> banned store(s).</div>";
        } else {
            echo "<div class='info'>✅ Admin <strong>{$admin['email']}</strong> has no banned stores.</div>";
        }
    }
    
    // Step 3: Fix admin if requested
    if (isset($_GET['fix_admin']) && !empty($_GET['fix_admin'])) {
        $adminEmail = urldecode($_GET['fix_admin']);
        echo "<h3>Step 3: Fixing admin account: {$adminEmail}</h3>";
        
        try {
            $db->beginTransaction();
            
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
                // Ignore
            }
            
            // Unban user account
            if ($hasUserBanColumns) {
                $updateUserStmt = $db->prepare("
                    UPDATE users 
                    SET status = 'active',
                        banned_at = NULL,
                        banned_until = NULL,
                        ban_duration_days = NULL,
                        ban_reason = NULL,
                        banned_by = NULL,
                        updated_at = NOW()
                    WHERE email = ? AND role = 'admin'
                ");
                $updateUserStmt->execute([$adminEmail]);
                echo "<div class='success'>✅ Updated users table (with ban tracking columns)</div>";
            } else {
                $updateUserStmt = $db->prepare("
                    UPDATE users 
                    SET status = 'active',
                        updated_at = NOW()
                    WHERE email = ? AND role = 'admin'
                ");
                $updateUserStmt->execute([$adminEmail]);
                echo "<div class='success'>✅ Updated users table (status only)</div>";
            }
            
            // Unban admin_registrations
            if ($hasAdminRegBanColumns) {
                $updateAdminRegStmt = $db->prepare("
                    UPDATE admin_registrations 
                    SET registration_status = 'approved',
                        banned_at = NULL,
                        banned_until = NULL,
                        ban_duration_days = NULL,
                        ban_reason = NULL,
                        banned_by = NULL,
                        updated_at = NOW()
                    WHERE email = ?
                ");
                $updateAdminRegStmt->execute([$adminEmail]);
                echo "<div class='success'>✅ Updated admin_registrations table (with ban tracking columns)</div>";
            } else {
                $updateAdminRegStmt = $db->prepare("
                    UPDATE admin_registrations 
                    SET registration_status = 'approved',
                        updated_at = NOW()
                    WHERE email = ?
                ");
                $updateAdminRegStmt->execute([$adminEmail]);
                echo "<div class='success'>✅ Updated admin_registrations table (status only)</div>";
            }
            
            $db->commit();
            
            echo "<div class='success' style='font-size:18px;font-weight:bold;margin-top:20px;'>";
            echo "✅ SUCCESS! Admin account '{$adminEmail}' has been fixed and set to ACTIVE!";
            echo "</div>";
            echo "<p><strong>Please try logging in again.</strong></p>";
            
            // Refresh the page to show updated status
            echo "<script>setTimeout(function(){ window.location.href = 'check_admin_login_status.php'; }, 2000);</script>";
            
        } catch (Exception $e) {
            $db->rollBack();
            echo "<div class='error'>❌ Error fixing admin: " . $e->getMessage() . "</div>";
        }
    }
    
    // Step 4: Test login logic
    echo "<h3>Step 4: Testing login logic for each admin...</h3>";
    foreach ($admins as $admin) {
        echo "<div style='margin:10px 0;padding:10px;background:white;border:1px solid #ddd;border-radius:5px;'>";
        echo "<strong>Admin: {$admin['email']}</strong><br>";
        
        // Test 1: Check account status
        if ($admin['user_status'] === 'inactive') {
            echo "<span style='color:red;'>❌ BLOCKED: Account status is 'inactive'</span><br>";
            echo "→ Login will be blocked with message: 'Your account has been banned by the administrator.'<br>";
        } else {
            echo "<span style='color:green;'>✅ Account status is 'active'</span><br>";
        }
        
        // Test 2: Check banned stores
        $bannedStoresStmt = $db->prepare("
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
                AND (sl.banned_at IS NOT NULL OR sl.banned_at != '')
        ");
        $bannedStoresStmt->execute([$admin['email']]);
        $bannedStoresCount = $bannedStoresStmt->fetch()['count'];
        
        if ($bannedStoresCount > 0) {
            echo "<span style='color:red;'>❌ BLOCKED: Admin has {$bannedStoresCount} banned store(s)</span><br>";
            echo "→ Login will be blocked even if account status is 'active'<br>";
        } else {
            echo "<span style='color:green;'>✅ No banned stores found</span><br>";
        }
        
        // Test 3: Check registration status
        if (($admin['registration_status'] ?? '') === 'banned') {
            echo "<span style='color:orange;'>⚠️ WARNING: Registration status is 'banned'</span><br>";
        } else {
            echo "<span style='color:green;'>✅ Registration status is OK</span><br>";
        }
        
        // Final verdict
        if ($admin['user_status'] === 'inactive' || $bannedStoresCount > 0) {
            echo "<strong style='color:red;'>RESULT: ADMIN CANNOT LOG IN</strong><br>";
            if ($admin['user_status'] === 'inactive') {
                echo "<a href='?fix_admin=" . urlencode($admin['email']) . "' style='padding:5px 10px;background:#28a745;color:white;text-decoration:none;border-radius:3px;margin-top:5px;display:inline-block;'>Fix This Admin</a>";
            }
        } else {
            echo "<strong style='color:green;'>RESULT: ADMIN CAN LOG IN</strong><br>";
        }
        
        echo "</div>";
    }
    
} catch (Exception $e) {
    echo "<div class='error'>";
    echo "❌ Fatal Error: " . $e->getMessage();
    echo "</div>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<p style='margin-top:30px;'>";
echo "<a href='check_admin_login_status.php' style='padding:10px 20px;background:#007bff;color:white;text-decoration:none;border-radius:5px;display:inline-block;margin-right:10px;font-weight:bold;'>Refresh This Page</a>";
echo "<a href='../control-panel/bannedStores' style='padding:10px 20px;background:#28a745;color:white;text-decoration:none;border-radius:5px;display:inline-block;'>Go to Banned Stores List</a>";
echo "</p>";

echo "</body></html>";

?>

