<?php
session_start();

echo "<h1>Session Debug Information</h1>";
echo "<hr>";

echo "<h2>Session Status:</h2>";
echo "<p><strong>Session ID:</strong> " . session_id() . "</p>";
echo "<p><strong>Session Status:</strong> " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Not Active') . "</p>";

echo "<hr>";
echo "<h2>Session Variables:</h2>";

if (!empty($_SESSION)) {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>Key</th><th>Value</th></tr>";
    foreach ($_SESSION as $key => $value) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($key) . "</strong></td>";
        echo "<td>" . htmlspecialchars(print_r($value, true)) . "</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p style='color: red;'><strong>⚠️ NO SESSION VARIABLES FOUND - You are NOT logged in!</strong></p>";
}

echo "<hr>";
echo "<h2>What This Means:</h2>";

if (isset($_SESSION['user_id'])) {
    echo "<p style='color: green;'>✅ You ARE logged in</p>";
    echo "<p><strong>User ID:</strong> " . $_SESSION['user_id'] . "</p>";
    
    if (isset($_SESSION['role'])) {
        echo "<p><strong>Role:</strong> " . $_SESSION['role'] . "</p>";
        
        if ($_SESSION['role'] === 'customer') {
            echo "<p style='color: green;'>✅ You have CUSTOMER role - should have access</p>";
        } elseif ($_SESSION['role'] === 'admin') {
            echo "<p style='color: orange;'>⚠️ You have ADMIN role - cannot access customer pages</p>";
        } else {
            echo "<p style='color: red;'>❌ Unknown role: " . $_SESSION['role'] . "</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ Role is NOT set in session</p>";
    }
} else {
    echo "<p style='color: red;'>❌ You are NOT logged in</p>";
    echo "<p><strong>Solution:</strong> Go to <a href='http://localhost/UphoCare/auth/login'>Login Page</a></p>";
}

echo "<hr>";
echo "<h2>Actions:</h2>";
echo "<p><a href='http://localhost/UphoCare/auth/login'>Go to Login</a></p>";
echo "<p><a href='http://localhost/UphoCare/customer/dashboard'>Try Customer Dashboard</a></p>";
echo "<p><a href='http://localhost/UphoCare/'>Go to Home</a></p>";

// Check database connection
echo "<hr>";
echo "<h2>Database Check:</h2>";
require_once 'config/config.php';
require_once 'config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    echo "<p style='color: green;'>✅ Database connected successfully</p>";
    
    // Check if users table exists and has data
    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
    $result = $stmt->fetch();
    echo "<p><strong>Customer accounts in database:</strong> " . $result['count'] . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
}
?>

