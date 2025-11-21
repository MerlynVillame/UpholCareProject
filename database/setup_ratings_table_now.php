<?php
/**
 * Quick Setup: Create Store Ratings Table
 * 
 * This script immediately creates the store_ratings table.
 * Run this in your browser: http://localhost/UphoCare/database/setup_ratings_table_now.php
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define root path
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('ROOT')) {
    define('ROOT', dirname(dirname(__FILE__)));
}

// Load configuration
require_once ROOT . DS . 'config' . DS . 'config.php';
require_once ROOT . DS . 'config' . DS . 'database.php';

// Get database connection
$db = Database::getInstance()->getConnection();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Store Ratings Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success { color: #28a745; }
        .error { color: #dc3545; }
        .warning { color: #ffc107; }
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Setup Store Ratings Table</h1>
        
        <?php
        try {
            // Check if table exists
            $checkStmt = $db->query("SHOW TABLES LIKE 'store_ratings'");
            $tableExists = $checkStmt->fetch() !== false;
            
            if ($tableExists) {
                echo '<div class="alert alert-success">';
                echo '<h4><i class="fas fa-check-circle"></i> Table Already Exists</h4>';
                echo '<p>The <strong>store_ratings</strong> table already exists. You can now use the rating system!</p>';
                echo '</div>';
                
                // Show table info
                $stmt = $db->query("SELECT COUNT(*) as count FROM store_ratings");
                $count = $stmt->fetch()['count'];
                echo '<div class="alert alert-info">';
                echo '<p><strong>Total Ratings:</strong> ' . $count . '</p>';
                echo '</div>';
                
                echo '<div class="mt-3">';
                echo '<a href="../customer/storeLocations" class="btn btn-primary">Go to Store Locations</a>';
                echo '</div>';
            } else {
                echo '<div class="alert alert-warning">';
                echo '<h4><i class="fas fa-exclamation-triangle"></i> Table Does Not Exist</h4>';
                echo '<p>Creating the <strong>store_ratings</strong> table now...</p>';
                echo '</div>';
                
                // Create table
                $createTableSql = "
                    CREATE TABLE store_ratings (
                        id INT AUTO_INCREMENT PRIMARY KEY,
                        store_id INT NOT NULL,
                        user_id INT NOT NULL,
                        rating DECIMAL(2, 1) NOT NULL COMMENT 'Rating from 1.0 to 5.0',
                        review_text TEXT NULL COMMENT 'Optional review text',
                        status ENUM('active', 'hidden') DEFAULT 'active',
                        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                        UNIQUE KEY unique_store_user_rating (store_id, user_id),
                        INDEX idx_store_id (store_id),
                        INDEX idx_user_id (user_id),
                        INDEX idx_rating (rating),
                        INDEX idx_created_at (created_at)
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
                ";
                
                try {
                    $db->exec($createTableSql);
                    
                    echo '<div class="alert alert-success">';
                    echo '<h4><i class="fas fa-check-circle"></i> Table Created Successfully!</h4>';
                    echo '<p>The <strong>store_ratings</strong> table has been created successfully.</p>';
                    echo '</div>';
                    
                    // Try to add foreign keys (optional)
                    echo '<p><strong>Adding foreign keys (optional)...</strong></p>';
                    
                    try {
                        // Check if store_locations exists
                        $checkStore = $db->query("SHOW TABLES LIKE 'store_locations'");
                        if ($checkStore->fetch()) {
                            try {
                                $db->exec("
                                    ALTER TABLE store_ratings 
                                    ADD CONSTRAINT fk_store_ratings_store_id 
                                    FOREIGN KEY (store_id) REFERENCES store_locations(id) ON DELETE CASCADE
                                ");
                                echo '<p class="success">✓ Foreign key for store_id added</p>';
                            } catch (Exception $e) {
                                echo '<p class="warning">⚠ Could not add foreign key for store_id (non-critical)</p>';
                            }
                        }
                        
                        // Check if users exists
                        $checkUsers = $db->query("SHOW TABLES LIKE 'users'");
                        if ($checkUsers->fetch()) {
                            try {
                                $db->exec("
                                    ALTER TABLE store_ratings 
                                    ADD CONSTRAINT fk_store_ratings_user_id 
                                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                                ");
                                echo '<p class="success">✓ Foreign key for user_id added</p>';
                            } catch (Exception $e) {
                                echo '<p class="warning">⚠ Could not add foreign key for user_id (non-critical)</p>';
                            }
                        }
                    } catch (Exception $e) {
                        echo '<p class="warning">⚠ Could not add foreign keys (non-critical - table will still work)</p>';
                    }
                    
                    echo '<div class="alert alert-success mt-4">';
                    echo '<h5>Setup Complete!</h5>';
                    echo '<p>The rating system is now ready to use. Customers can now rate stores.</p>';
                    echo '</div>';
                    
                    echo '<div class="mt-3">';
                    echo '<a href="../customer/storeLocations" class="btn btn-success btn-lg">Go to Store Locations & Test Rating</a>';
                    echo '</div>';
                    
                } catch (Exception $e) {
                    echo '<div class="alert alert-danger">';
                    echo '<h4>Error Creating Table</h4>';
                    echo '<p><strong>Error:</strong> ' . htmlspecialchars($e->getMessage()) . '</p>';
                    echo '<p><strong>Possible causes:</strong></p>';
                    echo '<ul>';
                    echo '<li>Database permissions issue</li>';
                    echo '<li>MySQL version incompatible</li>';
                    echo '<li>Table name conflict</li>';
                    echo '</ul>';
                    echo '</div>';
                    
                    echo '<div class="alert alert-info">';
                    echo '<h5>Manual Setup</h5>';
                    echo '<p>You can manually create the table by running this SQL in phpMyAdmin:</p>';
                    echo '<pre>' . htmlspecialchars($createTableSql) . '</pre>';
                    echo '</div>';
                }
            }
            
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">';
            echo '<h4>Error</h4>';
            echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>

