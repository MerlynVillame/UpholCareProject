<?php
/**
 * UphoCare Database Setup Script
 * Run this file once to set up the database
 */

// Database configuration
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'uphocare_db';

echo "<h2>UphoCare Database Setup</h2>";
echo "<hr>";

try {
    // Step 1: Connect to MySQL without database
    echo "<p><strong>Step 1:</strong> Connecting to MySQL server...</p>";
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ Connected to MySQL server successfully!</p>";
    
    // Step 2: Create database if not exists
    echo "<p><strong>Step 2:</strong> Creating database '$dbname'...</p>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $dbname");
    echo "<p style='color: green;'>✓ Database '$dbname' created/verified successfully!</p>";
    
    // Step 3: Select the database
    $pdo->exec("USE $dbname");
    
    // Step 4: Read and execute SQL file
    echo "<p><strong>Step 3:</strong> Importing database schema and data...</p>";
    $sql = file_get_contents(__DIR__ . '/uphocare_db.sql');
    
    // Split SQL commands by semicolon
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    $successCount = 0;
    foreach ($statements as $statement) {
        if (!empty($statement) && substr($statement, 0, 2) !== '--') {
            try {
                $pdo->exec($statement);
                $successCount++;
            } catch (PDOException $e) {
                // Skip errors for CREATE DATABASE and USE statements
                if (strpos($statement, 'CREATE DATABASE') === false && 
                    strpos($statement, 'USE ') === false) {
                    echo "<p style='color: orange;'>Warning: " . $e->getMessage() . "</p>";
                }
            }
        }
    }
    
    echo "<p style='color: green;'>✓ Executed $successCount SQL statements successfully!</p>";
    
    // Step 5: Verify tables
    echo "<p><strong>Step 4:</strong> Verifying tables...</p>";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li style='color: green;'>✓ Table: $table</li>";
    }
    echo "</ul>";
    
    echo "<h3 style='color: green;'>✓ Database setup completed successfully!</h3>";
    
    // Display default credentials
    echo "<hr>";
    echo "<h3>Default Login Credentials:</h3>";
    echo "<div style='background: #f0f0f0; padding: 15px; border-left: 4px solid #4e73df;'>";
    echo "<p><strong>Admin Account:</strong></p>";
    echo "<ul>";
    echo "<li>Username: <strong>admin</strong></li>";
    echo "<li>Password: <strong>admin123</strong></li>";
    echo "<li>Email: admin@uphocare.com</li>";
    echo "</ul>";
    echo "<p><strong>Customer Demo Account:</strong></p>";
    echo "<ul>";
    echo "<li>Username: <strong>customer</strong></li>";
    echo "<li>Password: <strong>customer123</strong></li>";
    echo "<li>Email: customer@example.com</li>";
    echo "</ul>";
    echo "</div>";
    
    echo "<hr>";
    echo "<p><strong>Next Steps:</strong></p>";
    echo "<ol>";
    echo "<li>Go to <a href='../'>http://localhost/UphoCare/</a></li>";
    echo "<li>Login with the credentials above</li>";
    echo "<li>Delete this setup.php file for security</li>";
    echo "</ol>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>✗ Error: " . $e->getMessage() . "</p>";
    echo "<p><strong>Troubleshooting:</strong></p>";
    echo "<ul>";
    echo "<li>Make sure XAMPP MySQL service is running</li>";
    echo "<li>Check your database credentials in config/config.php</li>";
    echo "<li>Verify MySQL port is 3306 (default)</li>";
    echo "</ul>";
}

?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 800px;
        margin: 50px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h2 {
        color: #4e73df;
    }
    a {
        color: #4e73df;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
</style>

