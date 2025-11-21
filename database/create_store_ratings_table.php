<?php
/**
 * Create Store Ratings Table
 * 
 * This script creates the store_ratings table and triggers for the rating system.
 * Run this in your browser: http://localhost/UphoCare/database/create_store_ratings_table.php
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
    <title>Create Store Ratings Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status-success { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
        .status-info { color: #17a2b8; }
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
        <h1 class="mb-4">Create Store Ratings Table</h1>
        
        <?php
        $action = $_GET['action'] ?? 'check';
        
        if ($action === 'create') {
            createStoreRatingsTable($db);
        } else {
            checkTableStatus($db);
        }
        
        /**
         * Check if table exists
         */
        function checkTableStatus($db) {
            try {
                // Check if table exists
                $stmt = $db->query("
                    SELECT COUNT(*) as table_exists 
                    FROM information_schema.tables 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'store_ratings'
                ");
                $result = $stmt->fetch();
                $tableExists = ($result && $result['table_exists'] > 0);
                
                if ($tableExists) {
                    echo '<div class="alert alert-success">';
                    echo '<h4><i class="fas fa-check-circle"></i> Table Already Exists</h4>';
                    echo '<p>The <strong>store_ratings</strong> table already exists in the database.</p>';
                    echo '</div>';
                    
                    // Show table structure
                    echo '<h5>Table Structure:</h5>';
                    $stmt = $db->query("DESCRIBE store_ratings");
                    $columns = $stmt->fetchAll();
                    
                    echo '<table class="table table-bordered">';
                    echo '<thead><tr><th>Field</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th></tr></thead>';
                    echo '<tbody>';
                    foreach ($columns as $column) {
                        echo '<tr>';
                        echo '<td>' . htmlspecialchars($column['Field']) . '</td>';
                        echo '<td>' . htmlspecialchars($column['Type']) . '</td>';
                        echo '<td>' . htmlspecialchars($column['Null']) . '</td>';
                        echo '<td>' . htmlspecialchars($column['Key']) . '</td>';
                        echo '<td>' . htmlspecialchars($column['Default'] ?? 'NULL') . '</td>';
                        echo '</tr>';
                    }
                    echo '</tbody>';
                    echo '</table>';
                    
                    // Check triggers
                    echo '<h5 class="mt-4">Triggers:</h5>';
                    try {
                        $stmt = $db->query("SHOW TRIGGERS LIKE 'update_store_rating%'");
                        $triggers = $stmt->fetchAll();
                        
                        if (count($triggers) > 0) {
                            echo '<div class="alert alert-success">';
                            echo '<p><strong>Triggers found:</strong></p><ul>';
                            foreach ($triggers as $trigger) {
                                echo '<li>' . htmlspecialchars($trigger['Trigger']) . ' - ' . htmlspecialchars($trigger['Event']) . '</li>';
                            }
                            echo '</ul></div>';
                        } else {
                            echo '<div class="alert alert-warning">';
                            echo '<p>No triggers found. Triggers are optional but recommended for automatic rating updates.</p>';
                            echo '</div>';
                        }
                    } catch (Exception $e) {
                        echo '<div class="alert alert-warning">';
                        echo '<p>Could not check triggers: ' . htmlspecialchars($e->getMessage()) . '</p>';
                        echo '</div>';
                    }
                    
                    // Show record count
                    try {
                        $stmt = $db->query("SELECT COUNT(*) as count FROM store_ratings");
                        $count = $stmt->fetch()['count'];
                        echo '<div class="alert alert-info">';
                        echo '<p><strong>Total Ratings:</strong> ' . $count . '</p>';
                        echo '</div>';
                    } catch (Exception $e) {
                        // Ignore
                    }
                    
                } else {
                    echo '<div class="alert alert-warning">';
                    echo '<h4><i class="fas fa-exclamation-triangle"></i> Table Does Not Exist</h4>';
                    echo '<p>The <strong>store_ratings</strong> table does not exist. Click the button below to create it.</p>';
                    echo '</div>';
                    
                    echo '<div class="mt-4">';
                    echo '<a href="?action=create" class="btn btn-primary btn-lg" onclick="return confirm(\'This will create the store_ratings table. Continue?\')">';
                    echo 'Create Store Ratings Table</a>';
                    echo '</div>';
                }
                
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">';
                echo '<h4>Error</h4>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '</div>';
            }
        }
        
        /**
         * Create store ratings table
         */
        function createStoreRatingsTable($db) {
            echo '<h4>Creating Store Ratings Table...</h4>';
            echo '<div class="progress mb-3"><div class="progress-bar" role="progressbar" style="width: 0%"></div></div>';
            
            $errors = [];
            $success = [];
            
            try {
                $db->beginTransaction();
                
                // Step 1: Create table
                echo '<p><strong>Step 1:</strong> Creating store_ratings table...</p>';
                try {
                    // Create table without foreign keys first (to avoid errors if tables don't match)
                    $createTableSql = "
                        CREATE TABLE IF NOT EXISTS store_ratings (
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
                    
                    $db->exec($createTableSql);
                    $success[] = "Table 'store_ratings' created successfully";
                    echo '<p class="text-success">✓ Table created successfully</p>';
                    
                    // Try to add foreign keys separately (optional)
                    echo '<p><strong>Step 1b:</strong> Adding foreign keys (optional)...</p>';
                    try {
                        // Check if store_locations table exists
                        $checkStoreTable = $db->query("SHOW TABLES LIKE 'store_locations'");
                        if ($checkStoreTable->fetch()) {
                            try {
                                $db->exec("
                                    ALTER TABLE store_ratings 
                                    ADD CONSTRAINT fk_store_ratings_store_id 
                                    FOREIGN KEY (store_id) REFERENCES store_locations(id) ON DELETE CASCADE
                                ");
                                $success[] = "Foreign key for store_id added";
                                echo '<p class="text-success">✓ Foreign key for store_id added</p>';
                            } catch (Exception $e) {
                                echo '<p class="text-warning">⚠ Could not add foreign key for store_id: ' . htmlspecialchars($e->getMessage()) . ' (non-critical)</p>';
                            }
                        }
                        
                        // Check if users table exists
                        $checkUsersTable = $db->query("SHOW TABLES LIKE 'users'");
                        if ($checkUsersTable->fetch()) {
                            try {
                                $db->exec("
                                    ALTER TABLE store_ratings 
                                    ADD CONSTRAINT fk_store_ratings_user_id 
                                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                                ");
                                $success[] = "Foreign key for user_id added";
                                echo '<p class="text-success">✓ Foreign key for user_id added</p>';
                            } catch (Exception $e) {
                                echo '<p class="text-warning">⚠ Could not add foreign key for user_id: ' . htmlspecialchars($e->getMessage()) . ' (non-critical)</p>';
                            }
                        }
                    } catch (Exception $e) {
                        echo '<p class="text-warning">⚠ Could not add foreign keys: ' . htmlspecialchars($e->getMessage()) . ' (non-critical - table will still work)</p>';
                    }
                    
                } catch (Exception $e) {
                    $errorMsg = "Error creating table: " . $e->getMessage();
                    $errors[] = $errorMsg;
                    echo '<p class="text-danger">✗ ' . htmlspecialchars($errorMsg) . '</p>';
                    
                    // Check if table was created anyway
                    $checkStmt = $db->query("SHOW TABLES LIKE 'store_ratings'");
                    if ($checkStmt->fetch()) {
                        echo '<p class="text-warning">⚠ Table exists but may have errors. Checking structure...</p>';
                    } else {
                        $db->rollBack();
                        echo '<div class="alert alert-danger">';
                        echo '<h5>Creation Failed</h5>';
                        echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                        echo '<p><strong>Possible causes:</strong></p>';
                        echo '<ul>';
                        echo '<li>Database permissions issue</li>';
                        echo '<li>MySQL version incompatible</li>';
                        echo '<li>Table name conflict</li>';
                        echo '</ul>';
                        echo '</div>';
                        echo '<a href="?" class="btn btn-secondary">Go Back</a>';
                        return;
                    }
                }
                
                // Step 2: Create triggers (optional, but recommended)
                // Note: Triggers are optional - the rating system will work without them
                // The controller already updates ratings manually when ratings are submitted
                echo '<p><strong>Step 2:</strong> Creating triggers for automatic rating updates (optional)...</p>';
                echo '<p class="text-info"><small>Note: Triggers are optional. The system will update ratings manually if triggers cannot be created.</small></p>';
                
                // Drop existing triggers if they exist
                try {
                    $db->exec("DROP TRIGGER IF EXISTS update_store_rating_on_insert");
                    $db->exec("DROP TRIGGER IF EXISTS update_store_rating_on_update");
                    $db->exec("DROP TRIGGER IF EXISTS update_store_rating_on_delete");
                } catch (Exception $e) {
                    // Ignore errors when dropping
                }
                
                // Try to create triggers - they may fail due to MySQL version or permissions
                // This is OK - the system will work without them
                $triggersCreated = 0;
                
                // Create trigger for INSERT
                try {
                    $triggerInsertSql = "CREATE TRIGGER update_store_rating_on_insert
                        AFTER INSERT ON store_ratings
                        FOR EACH ROW
                        BEGIN
                            UPDATE store_locations
                            SET rating = COALESCE((
                                SELECT AVG(rating)
                                FROM store_ratings
                                WHERE store_id = NEW.store_id AND status = 'active'
                            ), 0.00),
                            updated_at = NOW()
                            WHERE id = NEW.store_id;
                        END";
                    
                    // For PDO, we need to execute this carefully
                    // Some MySQL versions require DELIMITER which PDO doesn't support well
                    // So we'll try a simpler approach first
                    try {
                        // Try simple trigger without BEGIN/END first
                        $simpleTrigger = "CREATE TRIGGER update_store_rating_on_insert
                            AFTER INSERT ON store_ratings
                            FOR EACH ROW
                            UPDATE store_locations
                            SET rating = COALESCE((
                                SELECT AVG(rating) FROM store_ratings 
                                WHERE store_id = NEW.store_id AND status = 'active'
                            ), 0.00), updated_at = NOW()
                            WHERE id = NEW.store_id";
                        $db->exec($simpleTrigger);
                        $triggersCreated++;
                        $success[] = "Trigger 'update_store_rating_on_insert' created";
                        echo '<p class="text-success">✓ Insert trigger created</p>';
                    } catch (Exception $e2) {
                        throw $e2;
                    }
                } catch (Exception $e) {
                    echo '<p class="text-warning">⚠ Could not create insert trigger: ' . htmlspecialchars($e->getMessage()) . ' (optional - system will update ratings manually)</p>';
                }
                
                // Create trigger for UPDATE
                try {
                    $triggerUpdateSql = "CREATE TRIGGER update_store_rating_on_update
                        AFTER UPDATE ON store_ratings
                        FOR EACH ROW
                        UPDATE store_locations
                        SET rating = COALESCE((
                            SELECT AVG(rating) FROM store_ratings 
                            WHERE store_id = NEW.store_id AND status = 'active'
                        ), 0.00), updated_at = NOW()
                        WHERE id = NEW.store_id";
                    
                    $db->exec($triggerUpdateSql);
                    $triggersCreated++;
                    $success[] = "Trigger 'update_store_rating_on_update' created";
                    echo '<p class="text-success">✓ Update trigger created</p>';
                } catch (Exception $e) {
                    echo '<p class="text-warning">⚠ Could not create update trigger: ' . htmlspecialchars($e->getMessage()) . ' (optional)</p>';
                }
                
                // Create trigger for DELETE
                try {
                    $triggerDeleteSql = "CREATE TRIGGER update_store_rating_on_delete
                        AFTER DELETE ON store_ratings
                        FOR EACH ROW
                        UPDATE store_locations
                        SET rating = COALESCE((
                            SELECT AVG(rating) FROM store_ratings 
                            WHERE store_id = OLD.store_id AND status = 'active'
                        ), 0.00), updated_at = NOW()
                        WHERE id = OLD.store_id";
                    
                    $db->exec($triggerDeleteSql);
                    $triggersCreated++;
                    $success[] = "Trigger 'update_store_rating_on_delete' created";
                    echo '<p class="text-success">✓ Delete trigger created</p>';
                } catch (Exception $e) {
                    echo '<p class="text-warning">⚠ Could not create delete trigger: ' . htmlspecialchars($e->getMessage()) . ' (optional)</p>';
                }
                
                if ($triggersCreated === 0) {
                    echo '<div class="alert alert-info mt-3">';
                    echo '<p><strong>Note:</strong> Triggers could not be created. This is OK - the rating system will still work.</p>';
                    echo '<p>The system will update store ratings manually when customers submit ratings.</p>';
                    echo '</div>';
                } else if ($triggersCreated < 3) {
                    echo '<div class="alert alert-info mt-3">';
                    echo '<p><strong>Note:</strong> Some triggers could not be created. The system will update ratings manually for those operations.</p>';
                    echo '</div>';
                }
                
                $db->commit();
                
                echo '<div class="alert alert-success mt-4">';
                echo '<h4><i class="fas fa-check-circle"></i> Setup Complete!</h4>';
                echo '<p>The store ratings table has been created successfully.</p>';
                
                if (count($success) > 0) {
                    echo '<p><strong>Successfully created:</strong></p><ul>';
                    foreach ($success as $msg) {
                        echo '<li>' . htmlspecialchars($msg) . '</li>';
                    }
                    echo '</ul>';
                }
                
                if (count($errors) > 0) {
                    echo '<p class="text-warning"><strong>Warnings (non-critical):</strong></p><ul>';
                    foreach ($errors as $msg) {
                        echo '<li>' . htmlspecialchars($msg) . '</li>';
                    }
                    echo '</ul>';
                }
                
                echo '</div>';
                
                echo '<div class="mt-3">';
                echo '<a href="?" class="btn btn-primary">View Table Status</a>';
                echo '<a href="../customer/storeLocations" class="btn btn-success">Go to Store Locations</a>';
                echo '</div>';
                
            } catch (Exception $e) {
                $db->rollBack();
                echo '<div class="alert alert-danger">';
                echo '<h4>Error</h4>';
                echo '<p>' . htmlspecialchars($e->getMessage()) . '</p>';
                echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
                echo '</div>';
                echo '<a href="?" class="btn btn-secondary">Go Back</a>';
            }
        }
        ?>
    </div>
</body>
</html>

