<?php
/**
 * Remove Test Data Script
 * 
 * This script removes all test booking records created by seed_yearly_test_data.php
 * 
 * It deletes records where notes = 'TEST_DATA_DO_NOT_DELETE'
 * 
 * To run: Navigate to http://localhost/UphoCare/database/remove_test_data.php
 */

require_once __DIR__ . '/../config/database.php';

// Check if user confirmed deletion
$confirmed = isset($_GET['confirm']) && $_GET['confirm'] === 'yes';

if (!$confirmed) {
    // Show confirmation page
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Remove Test Data - UphoCare</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                max-width: 800px;
                margin: 0 auto;
                padding: 20px;
                background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .container {
                background: white;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.3);
                text-align: center;
            }
            h1 {
                color: #dc3545;
                margin-top: 0;
                font-size: 2.5rem;
            }
            .warning-icon {
                font-size: 5rem;
                color: #ffc107;
                margin: 20px 0;
            }
            p {
                color: #2c3e50;
                font-size: 1.1rem;
                line-height: 1.6;
                margin: 20px 0;
            }
            .info-box {
                background: #fff3cd;
                border: 2px solid #ffc107;
                padding: 20px;
                border-radius: 10px;
                margin: 20px 0;
            }
            .info-box strong {
                color: #856404;
            }
            .button-group {
                margin-top: 30px;
            }
            .btn {
                text-decoration: none;
                padding: 15px 30px;
                border-radius: 8px;
                display: inline-block;
                margin: 10px;
                font-weight: bold;
                font-size: 1.1rem;
                transition: all 0.3s ease;
            }
            .btn-danger {
                background: #dc3545;
                color: white;
            }
            .btn-danger:hover {
                background: #c82333;
                transform: scale(1.05);
            }
            .btn-secondary {
                background: #6c757d;
                color: white;
            }
            .btn-secondary:hover {
                background: #5a6268;
                transform: scale(1.05);
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="warning-icon">⚠️</div>
            <h1>Confirm Data Removal</h1>
            
            <p>
                You are about to <strong style="color: #dc3545;">permanently delete</strong> all test booking records from the database.
            </p>
            
            <div class="info-box">
                <p style="margin: 0;">
                    <strong>This will remove:</strong><br>
                    • All bookings with notes = 'TEST_DATA_DO_NOT_DELETE'<br>
                    • All test bookings created by the seeder script<br>
                    • Test records across all years (2021-2025)
                </p>
            </div>
            
            <p style="color: #dc3545; font-weight: bold;">
                This action cannot be undone!
            </p>
            
            <div class="button-group">
                <a href="?confirm=yes" class="btn btn-danger">
                    🗑️ Yes, Delete Test Data
                </a>
                <a href="../admin/reports" class="btn btn-secondary">
                    ← Cancel & Go Back
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// User confirmed - proceed with deletion
$db = Database::getInstance()->getConnection();

try {
    $db->beginTransaction();
    
    // First, get count of records to be deleted
    $countStmt = $db->prepare("SELECT COUNT(*) as count FROM bookings WHERE notes = 'TEST_DATA_DO_NOT_DELETE'");
    $countStmt->execute();
    $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
    $recordCount = $countResult['count'];
    
    if ($recordCount == 0) {
        echo "<div style='background: #fff3cd; border: 2px solid #ffc107; padding: 20px; border-radius: 10px; margin: 20px; text-align: center;'>";
        echo "<h2 style='color: #856404;'>ℹ️ No Test Data Found</h2>";
        echo "<p style='color: #856404;'>There are no test records to delete.</p>";
        echo "<a href='../admin/reports' style='color: #007bff; font-weight: bold; text-decoration: none;'>← Back to Reports</a>";
        echo "</div>";
        exit;
    }
    
    // Get some sample records before deletion (for confirmation)
    $sampleStmt = $db->prepare("SELECT booking_number, created_at FROM bookings WHERE notes = 'TEST_DATA_DO_NOT_DELETE' LIMIT 5");
    $sampleStmt->execute();
    $sampleRecords = $sampleStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Delete the records
    $deleteStmt = $db->prepare("DELETE FROM bookings WHERE notes = 'TEST_DATA_DO_NOT_DELETE'");
    $deleteStmt->execute();
    $deletedCount = $deleteStmt->rowCount();
    
    // Optional: Delete test customer if no other bookings
    $testCustomerEmail = 'test_customer@uphocare.test';
    $customerCheckStmt = $db->prepare("SELECT COUNT(*) as count FROM bookings WHERE customer_id = (SELECT id FROM users WHERE email = ?)");
    $customerCheckStmt->execute([$testCustomerEmail]);
    $customerCheck = $customerCheckStmt->fetch(PDO::FETCH_ASSOC);
    
    $customerDeleted = false;
    if ($customerCheck['count'] == 0) {
        // No more bookings for test customer, safe to delete
        $deleteCustomerStmt = $db->prepare("DELETE FROM users WHERE email = ?");
        $deleteCustomerStmt->execute([$testCustomerEmail]);
        $customerDeleted = true;
    }
    
    $db->commit();
    
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Test Data Removed - UphoCare</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                max-width: 900px;
                margin: 0 auto;
                padding: 20px;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
            }
            .container {
                background: white;
                padding: 40px;
                border-radius: 15px;
                box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            }
            h1 {
                color: #28a745;
                border-bottom: 3px solid #28a745;
                padding-bottom: 10px;
                margin-top: 0;
            }
            .success-box {
                background: #d4edda;
                border: 2px solid #28a745;
                padding: 20px;
                border-radius: 10px;
                margin: 20px 0;
            }
            .info-box {
                background: #d1ecf1;
                border: 2px solid #17a2b8;
                padding: 20px;
                border-radius: 10px;
                margin: 20px 0;
            }
            .btn {
                text-decoration: none;
                padding: 12px 25px;
                background: #667eea;
                color: white;
                border-radius: 8px;
                display: inline-block;
                margin: 10px 5px;
                font-weight: bold;
            }
            .btn:hover {
                background: #764ba2;
            }
            .btn-success {
                background: #28a745;
            }
            .btn-success:hover {
                background: #218838;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 15px 0;
            }
            th, td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }
            th {
                background: #f8f9fa;
                font-weight: bold;
            }
            tr:hover {
                background: #f8f9fa;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>✓ Test Data Successfully Removed</h1>
            
            <div class="success-box">
                <h2 style="color: #155724; margin: 0 0 15px 0;">Deletion Complete!</h2>
                <p style="color: #155724; font-size: 1.1rem; margin: 0;">
                    <strong><?php echo number_format($deletedCount); ?> test booking records</strong> have been permanently removed from the database.
                </p>
                <?php if ($customerDeleted): ?>
                <p style="color: #155724; margin: 10px 0 0 0;">
                    Test customer account also removed.
                </p>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($sampleRecords)): ?>
            <div class="info-box">
                <h3 style="color: #0c5460; margin: 0 0 15px 0;">Sample of Deleted Records:</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Booking Number</th>
                            <th>Date Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sampleRecords as $record): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['booking_number']); ?></td>
                            <td><?php echo date('F d, Y', strtotime($record['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php if ($deletedCount > 5): ?>
                <p style="color: #0c5460; margin: 10px 0 0 0; text-align: center;">
                    <em>... and <?php echo number_format($deletedCount - 5); ?> more records</em>
                </p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="../admin/reports" class="btn btn-success">📊 View Reports Dashboard</a>
                <a href="seed_yearly_test_data.php" class="btn">🌱 Seed New Test Data</a>
                <a href="../admin/dashboard" class="btn">🏠 Admin Dashboard</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    
} catch (Exception $e) {
    $db->rollBack();
    
    echo "<div style='background: #f8d7da; border: 2px solid #dc3545; padding: 20px; border-radius: 10px; margin: 20px; text-align: center;'>";
    echo "<h2 style='color: #721c24;'>✗ Error!</h2>";
    echo "<p style='color: #721c24;'>Failed to remove test data: " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<a href='../admin/reports' style='color: #007bff; font-weight: bold; text-decoration: none;'>← Back to Reports</a>";
    echo "</div>";
}
?>

