<?php
/**
 * Database Verification Script
 * Check if admin tables exist in db_upholcare database
 */

// Include database configuration
require_once '../config/database.php';

echo "=== UphoCare Database Verification ===\n\n";

try {
    // Check current database name
    $stmt = $pdo->query("SELECT DATABASE() as current_db");
    $currentDb = $stmt->fetch()['current_db'];
    echo "Current database: $currentDb\n\n";
    
    // List all tables in the database
    echo "=== Current Tables in Database ===\n";
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($tables as $table) {
        echo "✓ $table\n";
    }
    echo "\nTotal tables: " . count($tables) . "\n\n";
    
    // Check for admin-specific tables
    echo "=== Admin Tables Check ===\n";
    $adminTables = [
        'booking_numbers' => 'Admin-managed booking numbers',
        'email_logs' => 'Email notification tracking',
        'admin_settings' => 'Admin configuration settings',
        'admin_activity_log' => 'Admin action audit trail',
        'reservation_queue' => 'Reservation management queue'
    ];
    
    $existingAdminTables = [];
    $missingAdminTables = [];
    
    foreach ($adminTables as $table => $description) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->fetch()) {
            echo "✓ $table - $description\n";
            $existingAdminTables[] = $table;
        } else {
            echo "✗ $table - $description (MISSING)\n";
            $missingAdminTables[] = $table;
        }
    }
    
    // Check for views
    echo "\n=== Admin Views Check ===\n";
    $adminViews = [
        'admin_dashboard_stats' => 'Dashboard statistics view',
        'admin_booking_details' => 'Complete booking details view'
    ];
    
    foreach ($adminViews as $view => $description) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$view'");
        if ($stmt->fetch()) {
            echo "✓ $view - $description\n";
        } else {
            echo "✗ $view - $description (MISSING)\n";
        }
    }
    
    // Check bookings table structure
    echo "\n=== Bookings Table Structure ===\n";
    $stmt = $pdo->query("DESCRIBE bookings");
    $fields = $stmt->fetchAll();
    
    $requiredFields = ['booking_number_id', 'total_amount', 'payment_status', 'updated_at'];
    $existingFields = array_column($fields, 'Field');
    
    foreach ($requiredFields as $field) {
        if (in_array($field, $existingFields)) {
            echo "✓ $field\n";
        } else {
            echo "✗ $field (MISSING)\n";
        }
    }
    
    // Check services table structure
    echo "\n=== Services Table Structure ===\n";
    $stmt = $pdo->query("DESCRIBE services");
    $serviceFields = $stmt->fetchAll();
    $existingServiceFields = array_column($serviceFields, 'Field');
    
    if (in_array('category_id', $existingServiceFields)) {
        echo "✓ category_id\n";
    } else {
        echo "✗ category_id (MISSING)\n";
    }
    
    // Summary
    echo "\n=== Summary ===\n";
    echo "Existing admin tables: " . count($existingAdminTables) . "/" . count($adminTables) . "\n";
    echo "Missing admin tables: " . count($missingAdminTables) . "\n";
    
    if (count($missingAdminTables) > 0) {
        echo "\n⚠️ Missing tables detected. You need to run the admin tables setup.\n";
        echo "Run: php setup_admin_tables.php\n";
    } else {
        echo "\n✅ All admin tables are present!\n";
    }
    
    // Check sample data
    echo "\n=== Sample Data Check ===\n";
    
    // Check booking numbers
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM booking_numbers");
    $bookingNumbersCount = $stmt->fetch()['count'];
    echo "Booking numbers: $bookingNumbersCount\n";
    
    // Check bookings
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings");
    $bookingsCount = $stmt->fetch()['count'];
    echo "Bookings: $bookingsCount\n";
    
    // Check users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $usersCount = $stmt->fetch()['count'];
    echo "Users: $usersCount\n";
    
    // Check admin users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
    $adminCount = $stmt->fetch()['count'];
    echo "Admin users: $adminCount\n";
    
    // Check customer users
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
    $customerCount = $stmt->fetch()['count'];
    echo "Customer users: $customerCount\n";
    
    echo "\n=== Next Steps ===\n";
    if (count($missingAdminTables) > 0) {
        echo "1. Run: php setup_admin_tables.php\n";
        echo "2. Configure email settings in config/email.php\n";
        echo "3. Test email notifications in admin panel\n";
    } else {
        echo "1. Configure email settings in config/email.php\n";
        echo "2. Test email notifications in admin panel\n";
        echo "3. Start managing reservations\n";
    }
    
} catch (Exception $e) {
    echo "❌ Verification failed: " . $e->getMessage() . "\n";
    echo "Please check your database connection.\n";
}
