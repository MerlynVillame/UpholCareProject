<?php
/**
 * Database Connection Test
 * 
 * This file tests the connection to the upholcare_customers database
 * and displays basic information about the existing tables.
 */

require_once 'config/Database.php';

echo "<h2>UpholCare Database Connection Test</h2>\n";

try {
    // Test database connection
    $db = Database::getInstance();
    echo "<p style='color: green;'>✓ Successfully connected to upholcare_customers database</p>\n";
    
    // Get list of tables
    $tables = $db->fetchAll("SHOW TABLES");
    echo "<h3>Existing Tables:</h3>\n";
    echo "<ul>\n";
    foreach ($tables as $table) {
        $tableName = array_values($table)[0];
        echo "<li>{$tableName}</li>\n";
    }
    echo "</ul>\n";
    
    // Test each table for data
    echo "<h3>Table Data Summary:</h3>\n";
    foreach ($tables as $table) {
        $tableName = array_values($table)[0];
        $count = $db->fetchOne("SELECT COUNT(*) as count FROM {$tableName}");
        echo "<p><strong>{$tableName}:</strong> {$count['count']} records</p>\n";
    }
    
    // Test specific queries
    echo "<h3>Sample Data:</h3>\n";
    
    // Test customers table
    if ($db->tableExists('customers')) {
        $customers = $db->fetchAll("SELECT * FROM customers LIMIT 3");
        echo "<h4>Customers:</h4>\n";
        echo "<pre>" . print_r($customers, true) . "</pre>\n";
    }
    
    // Test services table
    if ($db->tableExists('services')) {
        $services = $db->fetchAll("SELECT * FROM services LIMIT 3");
        echo "<h4>Services:</h4>\n";
        echo "<pre>" . print_r($services, true) . "</pre>\n";
    }
    
    // Test bookings table
    if ($db->tableExists('bookings')) {
        $bookings = $db->fetchAll("SELECT * FROM bookings LIMIT 3");
        echo "<h4>Bookings:</h4>\n";
        echo "<pre>" . print_r($bookings, true) . "</pre>\n";
    }
    
    echo "<h3 style='color: green;'>🎉 Database connection test completed successfully!</h3>\n";
    echo "<p><a href='index.php'>← Back to UpholCare</a></p>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>✗ Database connection test failed: " . $e->getMessage() . "</p>\n";
    echo "<p>Please check your database configuration in config/database.php</p>\n";
}

?>
