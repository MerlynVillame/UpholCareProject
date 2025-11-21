<?php
/**
 * UphoCare Database Connection Test
 * Test your database connection
 */

require_once 'config/config.php';
require_once 'config/database.php';

echo "<h2>UphoCare Database Connection Test</h2>";
echo "<hr>";

try {
    // Get database instance
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo "<p style='color: green; font-size: 18px;'>✓ Database connection successful!</p>";
    
    echo "<h3>Connection Details:</h3>";
    echo "<ul>";
    echo "<li><strong>Host:</strong> " . DB_HOST . "</li>";
    echo "<li><strong>Database:</strong> " . DB_NAME . "</li>";
    echo "<li><strong>User:</strong> " . DB_USER . "</li>";
    echo "<li><strong>Status:</strong> <span style='color: green;'>Connected</span></li>";
    echo "</ul>";
    
    // Test query - count tables
    echo "<h3>Database Information:</h3>";
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p><strong>Total Tables:</strong> " . count($tables) . "</p>";
    echo "<ul>";
    foreach ($tables as $table) {
        // Count rows in each table
        $countStmt = $conn->query("SELECT COUNT(*) as count FROM $table");
        $count = $countStmt->fetch()['count'];
        echo "<li>$table <span style='color: #666;'>($count records)</span></li>";
    }
    echo "</ul>";
    
    // Test users table
    echo "<h3>User Accounts:</h3>";
    $stmt = $conn->query("SELECT id, username, email, fullname, role, status FROM users");
    $users = $stmt->fetchAll();
    
    if (count($users) > 0) {
        echo "<table border='1' cellpadding='10' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background: #4e73df; color: white;'>";
        echo "<th>ID</th><th>Username</th><th>Email</th><th>Name</th><th>Role</th><th>Status</th>";
        echo "</tr>";
        foreach ($users as $user) {
            echo "<tr>";
            echo "<td>{$user['id']}</td>";
            echo "<td>{$user['username']}</td>";
            echo "<td>{$user['email']}</td>";
            echo "<td>{$user['fullname']}</td>";
            echo "<td><span style='background: #1cc88a; color: white; padding: 2px 8px; border-radius: 3px;'>{$user['role']}</span></td>";
            echo "<td><span style='background: #36b9cc; color: white; padding: 2px 8px; border-radius: 3px;'>{$user['status']}</span></td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p style='color: orange;'>No users found. Database is empty - no privileges needed!</p>";
        echo "<p><strong>Next steps:</strong></p>";
        echo "<ul>";
        echo "<li>Go to <a href='auth/register'>Register Page</a> to create your first account</li>";
        echo "<li>Or run <a href='database/setup_empty_database.sql'>database/setup_empty_database.sql</a> to create tables</li>";
        echo "</ul>";
    }
    
    // Test services
    echo "<h3>Available Services:</h3>";
    $stmt = $conn->query("SELECT COUNT(*) as count FROM services WHERE status = 'active'");
    $serviceCount = $stmt->fetch()['count'];
    echo "<p>Active Services: <strong>$serviceCount</strong></p>";
    
    echo "<hr>";
    echo "<h3 style='color: green;'>✓ All tests passed!</h3>";
    echo "<p><strong>Your database is ready to use.</strong></p>";
    echo "<p>Go to <a href='index.php'>UphoCare Home</a> to login.</p>";
    
} catch (PDOException $e) {
    echo "<div style='background: #f8d7da; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px;'>";
    echo "<h3 style='color: #721c24;'>✗ Connection Failed!</h3>";
    echo "<p style='color: #721c24;'><strong>Error:</strong> " . $e->getMessage() . "</p>";
    echo "</div>";
    
    echo "<h3>Troubleshooting Steps:</h3>";
    echo "<ol>";
    echo "<li><strong>Check XAMPP:</strong> Make sure MySQL service is running in XAMPP Control Panel</li>";
    echo "<li><strong>Database Setup:</strong> Run <a href='database/setup_empty_database.sql'>database/setup_empty_database.sql</a> to create the database</li>";
    echo "<li><strong>Verify Credentials:</strong> Check config/config.php for correct database settings</li>";
    echo "<li><strong>MySQL Port:</strong> Ensure MySQL is running on port 3306</li>";
    echo "</ol>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error: " . $e->getMessage() . "</p>";
}

?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 900px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h2 {
        color: #4e73df;
    }
    table {
        background: white;
        margin: 10px 0;
    }
    th {
        text-align: left;
    }
    a {
        color: #4e73df;
        text-decoration: none;
        font-weight: bold;
    }
    a:hover {
        text-decoration: underline;
    }
</style>
