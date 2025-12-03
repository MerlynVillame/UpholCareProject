<?php
/**
 * Update payment_status ENUM in db_upholcare database
 * This adds the new payment status values: paid_full_cash, paid_on_delivery_cod, refunded, failed, cancelled
 * And removes 'partial' as requested
 */

require_once __DIR__ . '/../config/database.php';

echo "Updating payment_status ENUM in db_upholcare...\n\n";

try {
    $db = Database::getInstance()->getConnection();
    
    // Check current database
    $stmt = $db->query("SELECT DATABASE() as db_name");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "Connected to database: " . ($result['db_name'] ?? 'NULL') . "\n\n";
    
    // Check current ENUM values
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $currentEnum = '';
    foreach ($columns as $col) {
        if ($col['Field'] === 'payment_status') {
            $currentEnum = $col['Type'];
            echo "Current payment_status ENUM: " . $currentEnum . "\n";
            break;
        }
    }
    
    // Update existing 'partial' payments to 'unpaid' before altering ENUM
    echo "\nUpdating existing 'partial' payments to 'unpaid'...\n";
    $updateStmt = $db->prepare("UPDATE bookings SET payment_status = 'unpaid' WHERE payment_status = 'partial'");
    $updateResult = $updateStmt->execute();
    $affectedRows = $updateStmt->rowCount();
    echo "Updated $affectedRows records from 'partial' to 'unpaid'\n";
    
    // Update existing 'paid' statuses to 'paid_full_cash' for consistency
    echo "\nUpdating existing 'paid' statuses to 'paid_full_cash'...\n";
    $updateStmt2 = $db->prepare("UPDATE bookings SET payment_status = 'paid_full_cash' WHERE payment_status = 'paid'");
    $updateResult2 = $updateStmt2->execute();
    $affectedRows2 = $updateStmt2->rowCount();
    echo "Updated $affectedRows2 records from 'paid' to 'paid_full_cash'\n";
    
    // Alter the payment_status ENUM to include new values and remove 'partial'
    echo "\nAltering payment_status ENUM...\n";
    $alterSql = "ALTER TABLE bookings MODIFY COLUMN payment_status ENUM('unpaid', 'paid', 'paid_full_cash', 'paid_on_delivery_cod', 'refunded', 'failed', 'cancelled') DEFAULT 'unpaid'";
    $db->exec($alterSql);
    echo "✓ payment_status ENUM updated successfully!\n";
    
    // Verify the change
    echo "\nVerifying the change...\n";
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($columns as $col) {
        if ($col['Field'] === 'payment_status') {
            echo "New payment_status ENUM: " . $col['Type'] . "\n";
            break;
        }
    }
    
    // Show current payment status distribution
    echo "\nCurrent payment status distribution:\n";
    $stmt = $db->query("SELECT payment_status, COUNT(*) as count FROM bookings GROUP BY payment_status ORDER BY count DESC");
    $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($statuses as $status) {
        echo "  " . ($status['payment_status'] ?? 'NULL') . ": " . $status['count'] . " bookings\n";
    }
    
    echo "\n✓ Database update completed successfully!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

