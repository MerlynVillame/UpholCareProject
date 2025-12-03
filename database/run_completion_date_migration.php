<?php
/**
 * Migration Script: Add completion_date to bookings table
 * Run this file once to update your database
 * 
 * URL: http://localhost/UphoCare/database/run_completion_date_migration.php
 */

// Include configuration
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

// Set content type
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Database Migration - Add Completion Date</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .success {
            padding: 15px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            border-radius: 5px;
            margin: 10px 0;
        }
        .error {
            padding: 15px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            border-radius: 5px;
            margin: 10px 0;
        }
        .info {
            padding: 15px;
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            border-radius: 5px;
            margin: 10px 0;
        }
        pre {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            overflow-x: auto;
        }
        h1 {
            color: #333;
            border-bottom: 3px solid #4e73df;
            padding-bottom: 10px;
        }
        .step {
            margin: 20px 0;
            padding: 15px;
            border-left: 4px solid #4e73df;
            background: #f8f9fc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔧 Database Migration: Add Completion Date Tracking</h1>
        
        <div class="info">
            <strong>Purpose:</strong> Add <code>completion_date</code> column to track when bookings are completed.
        </div>

<?php
try {
    $db = Database::getInstance()->getConnection();
    
    echo '<div class="step">';
    echo '<h3>Step 1: Check if column already exists</h3>';
    
    // Check if completion_date column exists
    $checkColumn = $db->query("SHOW COLUMNS FROM `bookings` LIKE 'completion_date'");
    $columnExists = $checkColumn->rowCount() > 0;
    
    if ($columnExists) {
        echo '<div class="info">✓ Column <code>completion_date</code> already exists. Skipping column creation.</div>';
    } else {
        echo '<div class="info">Column <code>completion_date</code> does not exist. Will create it.</div>';
    }
    echo '</div>';
    
    // Step 2: Add column if it doesn't exist
    if (!$columnExists) {
        echo '<div class="step">';
        echo '<h3>Step 2: Adding completion_date column</h3>';
        
        $sql = "ALTER TABLE `bookings` 
                ADD COLUMN `completion_date` DATETIME NULL DEFAULT NULL 
                COMMENT 'Date and time when booking was marked as completed' 
                AFTER `updated_at`";
        
        $db->exec($sql);
        echo '<div class="success">✓ Successfully added <code>completion_date</code> column!</div>';
        echo '</div>';
    }
    
    // Step 3: Create indexes
    echo '<div class="step">';
    echo '<h3>Step 3: Creating indexes for better performance</h3>';
    
    // Check and create idx_completion_date
    try {
        $db->exec("CREATE INDEX `idx_completion_date` ON `bookings` (`completion_date`)");
        echo '<div class="success">✓ Created index: <code>idx_completion_date</code></div>';
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo '<div class="info">✓ Index <code>idx_completion_date</code> already exists</div>';
        } else {
            throw $e;
        }
    }
    
    // Check and create idx_status_payment_completion
    try {
        $db->exec("CREATE INDEX `idx_status_payment_completion` 
                   ON `bookings` (`status`, `payment_status`, `completion_date`)");
        echo '<div class="success">✓ Created index: <code>idx_status_payment_completion</code></div>';
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate key name') !== false) {
            echo '<div class="info">✓ Index <code>idx_status_payment_completion</code> already exists</div>';
        } else {
            throw $e;
        }
    }
    echo '</div>';
    
    // Step 4: Update existing completed bookings
    echo '<div class="step">';
    echo '<h3>Step 4: Updating existing completed bookings</h3>';
    
    $updateSql = "UPDATE `bookings` 
                  SET `completion_date` = COALESCE(`updated_at`, `created_at`)
                  WHERE `status` = 'completed' 
                  AND `payment_status` IN ('paid', 'paid_full_cash', 'paid_on_delivery_cod')
                  AND `completion_date` IS NULL";
    
    $stmt = $db->prepare($updateSql);
    $stmt->execute();
    $updatedRows = $stmt->rowCount();
    
    echo '<div class="success">✓ Updated <strong>' . $updatedRows . '</strong> existing completed bookings with completion dates</div>';
    echo '</div>';
    
    // Step 5: Verification
    echo '<div class="step">';
    echo '<h3>Step 5: Verification & Statistics</h3>';
    
    $verifySql = "SELECT 
                    COUNT(*) as total_completed_bookings,
                    COUNT(completion_date) as bookings_with_completion_date,
                    COUNT(*) - COUNT(completion_date) as bookings_without_completion_date
                  FROM `bookings`
                  WHERE `status` = 'completed'";
    
    $verifyStmt = $db->query($verifySql);
    $stats = $verifyStmt->fetch(PDO::FETCH_ASSOC);
    
    echo '<pre>';
    echo '<strong>Completed Bookings Statistics:</strong>' . "\n";
    echo 'Total Completed Bookings: ' . $stats['total_completed_bookings'] . "\n";
    echo 'With Completion Date: ' . $stats['bookings_with_completion_date'] . "\n";
    echo 'Without Completion Date: ' . $stats['bookings_without_completion_date'] . "\n";
    echo '</pre>';
    
    if ($stats['bookings_without_completion_date'] == 0) {
        echo '<div class="success">✓ All completed bookings now have completion dates!</div>';
    } else {
        echo '<div class="info">Some completed bookings still don\'t have completion dates. This is OK - they may have different payment statuses.</div>';
    }
    echo '</div>';
    
    // Success summary
    echo '<div class="step" style="border-left-color: #28a745; background: #d4edda;">';
    echo '<h2 style="color: #28a745;">✅ Migration Completed Successfully!</h2>';
    echo '<p><strong>What was done:</strong></p>';
    echo '<ul>';
    echo '<li>✓ Added <code>completion_date</code> column to bookings table</li>';
    echo '<li>✓ Created performance indexes</li>';
    echo '<li>✓ Updated ' . $updatedRows . ' existing completed bookings</li>';
    echo '<li>✓ Verified data integrity</li>';
    echo '</ul>';
    echo '<p><strong>Next Steps:</strong></p>';
    echo '<ul>';
    echo '<li>Go to <a href="' . BASE_URL . 'admin/reports" target="_blank">Reports Page</a> to see the updated data</li>';
    echo '<li>Mark any booking as "completed" to see automatic completion_date tracking</li>';
    echo '<li>Click the info icon (ℹ️) next to months in reports to see detailed booking list</li>';
    echo '</ul>';
    echo '</div>';
    
} catch (Exception $e) {
    echo '<div class="error">';
    echo '<h3>❌ Error during migration:</h3>';
    echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
    echo '<p><strong>Stack Trace:</strong></p>';
    echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    echo '</div>';
}
?>

        <div style="margin-top: 30px; padding: 15px; background: #e7f3ff; border-radius: 5px;">
            <strong>📚 Documentation:</strong> See <code>REPORTS_COMPLETED_BOOKINGS_UPDATE.md</code> for full details.
        </div>
        
        <div style="margin-top: 20px; text-align: center;">
            <a href="<?php echo BASE_URL; ?>admin/reports" 
               style="display: inline-block; padding: 12px 30px; background: #4e73df; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;">
                View Reports →
            </a>
        </div>
    </div>
</body>
</html>

