<?php
/**
 * Check bookings table primary key
 */

require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Checking bookings table primary key...\n";
    
    $stmt = $db->query("SHOW KEYS FROM bookings WHERE Key_name = 'PRIMARY'");
    $keys = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($keys)) {
        echo "No primary key found!\n";
    } else {
        foreach ($keys as $key) {
            echo "Primary key: " . $key['Column_name'] . "\n";
        }
    }
    
    // Also check if there's an 'id' column
    $stmt = $db->query("DESCRIBE bookings");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $hasId = false;
    $hasBookingId = false;
    
    foreach ($columns as $col) {
        if ($col['Field'] === 'id') {
            $hasId = true;
            echo "Found 'id' column\n";
        }
        if ($col['Field'] === 'booking_id') {
            $hasBookingId = true;
            echo "Found 'booking_id' column\n";
        }
    }
    
    // Show a sample booking to see the structure
    echo "\nSample booking record:\n";
    $stmt = $db->query("SELECT * FROM bookings LIMIT 1");
    $sample = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($sample) {
        foreach ($sample as $key => $value) {
            echo "$key: " . (is_null($value) ? 'NULL' : $value) . "\n";
        }
    } else {
        echo "No bookings found in table\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}

