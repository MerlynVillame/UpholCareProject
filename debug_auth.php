<?php
/**
 * Debug Authentication Issues
 */

session_start();
require_once 'config/config.php';

echo "<h2>🔍 Authentication Debug</h2>";
echo "<hr>";

echo "<h3>Session Status:</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

echo "<h3>Login Check:</h3>";
$isLoggedIn = isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
echo "Is Logged In: " . ($isLoggedIn ? "✅ YES" : "❌ NO") . "<br>";

if ($isLoggedIn) {
    echo "User ID: " . $_SESSION['user_id'] . "<br>";
    echo "Username: " . ($_SESSION['username'] ?? 'Not set') . "<br>";
    echo "Role: " . ($_SESSION['role'] ?? 'Not set') . "<br>";
    echo "Name: " . ($_SESSION['name'] ?? 'Not set') . "<br>";
    echo "User Name (session): " . ($_SESSION['user_name'] ?? 'Not set') . "<br>";
    echo "User Email (session): " . ($_SESSION['user_email'] ?? 'Not set') . "<br>";
}

echo "<h3>Role Check:</h3>";
$hasCustomerRole = isset($_SESSION['role']) && $_SESSION['role'] === 'customer';
echo "Has Customer Role: " . ($hasCustomerRole ? "✅ YES" : "❌ NO") . "<br>";

echo "<h3>Database Users:</h3>";
try {
    require_once 'config/database.php';
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    $stmt = $conn->query("SELECT id, username, email, fullname, role, status FROM users");
    $users = $stmt->fetchAll();
    
    if (count($users) > 0) {
        echo "<table border='1' cellpadding='10'>";
        echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Name</th><th>Role</th><th>Status</th></tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['fullname']}</td>";
            echo "<td>{$user['role']}</td>";
            echo "<td>{$user['status']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>No users found in database.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>Database Error: " . $e->getMessage() . "</p>";
}

echo "<h3>Quick Actions:</h3>";
echo "<a href='auth/register'>📝 Register</a> | ";
echo "<a href='auth/login'>🔑 Login</a> | ";
echo "<a href='customer/dashboard'>📊 Customer Dashboard</a> | ";
echo "<a href='test_connection.php'>🔍 Test Connection</a>";

?>

<style>
body { font-family: Arial, sans-serif; max-width: 1000px; margin: 50px auto; padding: 20px; }
table { border-collapse: collapse; width: 100%; margin: 10px 0; }
th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
th { background-color: #f2f2f2; }
a { color: #007bff; text-decoration: none; margin: 5px; }
a:hover { text-decoration: underline; }
</style>
