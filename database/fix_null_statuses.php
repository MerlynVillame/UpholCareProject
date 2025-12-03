<?php
/**
 * Fix NULL statuses in bookings table
 * Sets NULL statuses to 'pending' as default
 */

$host = 'localhost';
$dbname = 'db_upholcare';
$username = 'root';
$password = '';

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "Fixing NULL statuses in bookings table...\n";
    
    // Count NULL statuses
    $stmt = $db->query("SELECT COUNT(*) as count FROM bookings WHERE status IS NULL OR status = ''");
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $nullCount = $result['count'];
    
    echo "Found $nullCount bookings with NULL or empty status.\n";
    
    if ($nullCount > 0) {
        // Update NULL statuses to 'pending'
        $updateStmt = $db->prepare("UPDATE bookings SET status = 'pending' WHERE status IS NULL OR status = ''");
        $updateStmt->execute();
        
        $affected = $updateStmt->rowCount();
        echo "✓ Updated $affected bookings to 'pending' status.\n\n";
    } else {
        echo "✓ No NULL statuses found. All bookings have valid statuses.\n\n";
    }
    
    // Show final status distribution
    echo "Final bookings status distribution:\n";
    $stmt = $db->query("SELECT status, COUNT(*) as count FROM bookings GROUP BY status ORDER BY count DESC");
    $statuses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($statuses as $status) {
        echo "  " . ($status['status'] ?? 'NULL') . ": " . $status['count'] . "\n";
    }
    
    echo "\n✓ Fix completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

