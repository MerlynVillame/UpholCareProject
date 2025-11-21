<?php
/**
 * Create Store Compliance Reports Table
 * Run this script to create the store_compliance_reports table
 */

// Set up path constants
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('ROOT')) {
    define('ROOT', dirname(__DIR__));
}

// Include database configuration
require_once ROOT . DS . 'config' . DS . 'config.php';
require_once ROOT . DS . 'config' . DS . 'database.php';

// Read SQL file
$sqlFile = __DIR__ . DS . 'create_store_compliance_reports_table.sql';
$sql = file_get_contents($sqlFile);

if (!$sql) {
    die("Error: Could not read SQL file: $sqlFile");
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Execute SQL statements
    $statements = explode(';', $sql);
    
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement) && !preg_match('/^(USE|SELECT)/i', $statement)) {
            $db->exec($statement);
        }
    }
    
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Store Compliance Reports Table - Created</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
            .success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 15px; border-radius: 5px; margin: 20px 0; }
            .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <h1>Store Compliance Reports Table</h1>
        <div class='success'>
            <strong>Success!</strong> The store_compliance_reports table has been created successfully.
        </div>
        <p><a href='" . BASE_URL . "control-panel/complianceReports'>Go to Compliance Reports</a></p>
    </body>
    </html>";
    
} catch (Exception $e) {
    echo "<!DOCTYPE html>
    <html>
    <head>
        <title>Store Compliance Reports Table - Error</title>
        <style>
            body { font-family: Arial, sans-serif; padding: 20px; background: #f5f5f5; }
            .error { background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 15px; border-radius: 5px; margin: 20px 0; }
        </style>
    </head>
    <body>
        <h1>Store Compliance Reports Table</h1>
        <div class='error'>
            <strong>Error!</strong> " . htmlspecialchars($e->getMessage()) . "
        </div>
    </body>
    </html>";
}

