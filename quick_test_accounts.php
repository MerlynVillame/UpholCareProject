<?php
/**
 * Quick Test Accounts Setup
 * This will create test accounts directly in your database
 */

require_once 'config/config.php';
require_once 'config/database.php';

echo "<h2>🔑 Quick Test Accounts Setup</h2>";
echo "<hr>";

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo "<h3>Creating Test Accounts...</h3>";
    
    // Test accounts data
    $testAccounts = [
        [
            'username' => 'customer',
            'email' => 'customer@uphocare.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'fullname' => 'Test Customer',
            'phone' => '09123456789',
            'role' => 'customer',
            'status' => 'active'
        ],
        [
            'username' => 'admin',
            'email' => 'admin@uphocare.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'fullname' => 'Test Admin',
            'phone' => '09123456789',
            'role' => 'admin',
            'status' => 'active'
        ],
        [
            'username' => 'testuser',
            'email' => 'test@uphocare.com',
            'password' => password_hash('password', PASSWORD_DEFAULT),
            'fullname' => 'Test User',
            'phone' => '09123456789',
            'role' => 'customer',
            'status' => 'active'
        ]
    ];
    
    $created = 0;
    $skipped = 0;
    
    foreach ($testAccounts as $account) {
        // Check if username already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
        $stmt->execute([$account['username']]);
        
        if ($stmt->fetch()) {
            echo "<p>⚠️ Account '{$account['username']}' already exists - skipped</p>";
            $skipped++;
        } else {
            // Insert new account
            $stmt = $conn->prepare("
                INSERT INTO users (username, email, password, fullname, phone, role, status, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
            ");
            
            if ($stmt->execute([
                $account['username'],
                $account['email'],
                $account['password'],
                $account['fullname'],
                $account['phone'],
                $account['role'],
                $account['status']
            ])) {
                echo "<p>✅ Account '{$account['username']}' created successfully</p>";
                $created++;
            } else {
                echo "<p>❌ Failed to create account '{$account['username']}'</p>";
            }
        }
    }
    
    echo "<hr>";
    echo "<h3>Summary:</h3>";
    echo "<p>✅ Created: $created accounts</p>";
    echo "<p>⚠️ Skipped: $skipped accounts</p>";
    
    // Show all users
    echo "<h3>All Users in Database:</h3>";
    $stmt = $conn->query("SELECT id, username, email, fullname, role, status, created_at FROM users ORDER BY id");
    $users = $stmt->fetchAll();
    
    if (count($users) > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #4e73df; color: white;'>";
        echo "<th>ID</th><th>Username</th><th>Email</th><th>Name</th><th>Role</th><th>Status</th><th>Created</th>";
        echo "</tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td><strong>{$user['username']}</strong></td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['fullname']}</td>";
            echo "<td><span style='background: " . ($user['role'] == 'admin' ? '#e74a3b' : '#1cc88a') . "; color: white; padding: 2px 8px; border-radius: 3px;'>{$user['role']}</span></td>";
            echo "<td><span style='background: #36b9cc; color: white; padding: 2px 8px; border-radius: 3px;'>{$user['status']}</span></td>";
            echo "<td>{$user['created_at']}</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: red;'>No users found in database.</p>";
    }
    
    echo "<hr>";
    echo "<h3>🔑 Test Account Credentials:</h3>";
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 5px solid #007bff;'>";
    echo "<h4>Customer Account:</h4>";
    echo "<p><strong>Username:</strong> customer</p>";
    echo "<p><strong>Password:</strong> password</p>";
    echo "<p><strong>Role:</strong> Customer</p>";
    echo "<p><strong>Access:</strong> <a href='auth/login'>Login as Customer</a></p>";
    echo "</div>";
    
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 5px solid #e74a3b; margin-top: 10px;'>";
    echo "<h4>Admin Account:</h4>";
    echo "<p><strong>Username:</strong> admin</p>";
    echo "<p><strong>Password:</strong> password</p>";
    echo "<p><strong>Role:</strong> Admin</p>";
    echo "<p><strong>Access:</strong> <a href='auth/login'>Login as Admin</a></p>";
    echo "</div>";
    
    echo "<div style='background: #f8f9fa; padding: 20px; border-radius: 10px; border-left: 5px solid #1cc88a; margin-top: 10px;'>";
    echo "<h4>Test User Account:</h4>";
    echo "<p><strong>Username:</strong> testuser</p>";
    echo "<p><strong>Password:</strong> password</p>";
    echo "<p><strong>Role:</strong> Customer</p>";
    echo "<p><strong>Access:</strong> <a href='auth/login'>Login as Test User</a></p>";
    echo "</div>";
    
    echo "<hr>";
    echo "<h3>🚀 Next Steps:</h3>";
    echo "<ol>";
    echo "<li><a href='auth/login'>🔑 Go to Login Page</a></li>";
    echo "<li><a href='debug_auth.php'>🔍 Check Authentication Status</a></li>";
    echo "<li><a href='test_connection.php'>📊 Test Database Connection</a></li>";
    echo "</ol>";
    
} catch (Exception $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px;'>";
    echo "<h3 style='color: #721c24;'>❌ Error!</h3>";
    echo "<p style='color: #721c24;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
}

?>

<style>
body { 
    font-family: Arial, sans-serif; 
    max-width: 1000px; 
    margin: 50px auto; 
    padding: 20px; 
    background: #f5f5f5;
}
table { 
    background: white;
    border-collapse: collapse; 
    width: 100%; 
    margin: 10px 0; 
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}
th, td { 
    border: 1px solid #ddd; 
    padding: 12px; 
    text-align: left; 
}
th { 
    background-color: #4e73df; 
    color: white; 
}
a { 
    color: #007bff; 
    text-decoration: none; 
    font-weight: bold;
}
a:hover { 
    text-decoration: underline; 
}
</style>
