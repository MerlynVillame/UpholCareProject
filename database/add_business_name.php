<?php
/**
 * Add Business Name Field to admin_registrations Table
 * Quick fix script - Run this in your browser
 * 
 * URL: http://localhost/UphoCare/database/add_business_name.php
 */

// Database configuration
require_once __DIR__ . '/../config/config.php';

try {
    // Connect to database
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );
    
    echo "<!DOCTYPE html>";
    echo "<html><head><title>Add Business Name Field</title>";
    echo "<style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .success { color: green; background: #d4edda; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .error { color: red; background: #f8d7da; padding: 10px; border-radius: 5px; margin: 10px 0; }
        .info { color: blue; background: #d1ecf1; padding: 10px; border-radius: 5px; margin: 10px 0; }
        pre { background: #f5f5f5; padding: 15px; border-radius: 5px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 10px; text-align: left; border: 1px solid #ddd; }
        th { background: #007bff; color: white; }
    </style>";
    echo "</head><body>";
    
    echo "<h2>Adding Business Name Field to admin_registrations Table</h2>";
    
    // Check if column already exists
    $checkColumn = $db->query("
        SELECT COUNT(*) as exists_count 
        FROM INFORMATION_SCHEMA.COLUMNS 
        WHERE TABLE_SCHEMA = DATABASE() 
        AND TABLE_NAME = 'admin_registrations' 
        AND COLUMN_NAME = 'business_name'
    ")->fetch();
    
    if ($checkColumn['exists_count'] > 0) {
        echo "<div class='info'>";
        echo "✅ The <strong>business_name</strong> column already exists in the admin_registrations table.";
        echo "</div>";
    } else {
        // Add the column
        try {
            $sql = "ALTER TABLE `admin_registrations` ADD COLUMN `business_name` VARCHAR(255) NULL AFTER `phone`";
            $db->exec($sql);
            echo "<div class='success'>";
            echo "✅ Successfully added <strong>business_name</strong> column to admin_registrations table!";
            echo "</div>";
        } catch (PDOException $e) {
            echo "<div class='error'>";
            echo "❌ Error adding column: " . htmlspecialchars($e->getMessage());
            echo "</div>";
            throw $e;
        }
    }
    
    // Show current table structure
    echo "<h3>Current admin_registrations Table Structure</h3>";
    echo "<table>";
    echo "<tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr>";
    
    $columns = $db->query("DESCRIBE admin_registrations")->fetchAll();
    foreach ($columns as $column) {
        echo "<tr>";
        echo "<td><strong>" . htmlspecialchars($column['Field']) . "</strong></td>";
        echo "<td>" . htmlspecialchars($column['Type']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Null']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Key']) . "</td>";
        echo "<td>" . htmlspecialchars($column['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
    
    // Check if business_name exists
    $businessNameExists = false;
    foreach ($columns as $column) {
        if ($column['Field'] === 'business_name') {
            $businessNameExists = true;
            break;
        }
    }
    
    if ($businessNameExists) {
        echo "<div class='success'>";
        echo "<h3>✅ Success!</h3>";
        echo "<p>The <strong>business_name</strong> field has been successfully added to the database.</p>";
        echo "<p>You can now:</p>";
        echo "<ul>";
        echo "<li><a href='" . BASE_URL . "auth/registerAdmin'>Go to Admin Registration</a> and try registering again</li>";
        echo "<li>The registration form will now save the business name to the database</li>";
        echo "</ul>";
        echo "</div>";
    }
    
    echo "</body></html>";
    
} catch (PDOException $e) {
    echo "<!DOCTYPE html>";
    echo "<html><head><title>Error</title></head><body>";
    echo "<h2>Database Error</h2>";
    echo "<div class='error'>";
    echo "Error: " . htmlspecialchars($e->getMessage());
    echo "</div>";
    echo "<p>Please check:</p>";
    echo "<ul>";
    echo "<li>Database credentials in <code>config/config.php</code></li>";
    echo "<li>That the database <code>db_upholcare</code> exists</li>";
    echo "<li>That the table <code>admin_registrations</code> exists</li>";
    echo "</ul>";
    echo "</body></html>";
} catch (Exception $e) {
    echo "<!DOCTYPE html>";
    echo "<html><head><title>Error</title></head><body>";
    echo "<h2>Error</h2>";
    echo "<div class='error'>";
    echo "Error: " . htmlspecialchars($e->getMessage());
    echo "</div>";
    echo "</body></html>";
}

