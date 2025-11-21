<?php
/**
 * Test script to verify database connections and admin functionality
 */

require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
require_once dirname(__DIR__) . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'database.php';

echo "Testing UphoCare Admin Database Connections...\n\n";

try {
    // Test database connection
    $db = Database::getInstance()->getConnection();
    echo "✅ Database connection successful\n";
    
    // Test if admin tables exist
    $tables = [
        'booking_numbers',
        'email_logs', 
        'admin_settings',
        'admin_activity_log',
        'reservation_queue',
        'customer_booking_numbers',
        'repair_items',
        'repair_quotations'
    ];
    
    echo "\nChecking admin tables:\n";
    foreach ($tables as $table) {
        $stmt = $db->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$table]);
        if ($stmt->fetch()) {
            echo "✅ Table '{$table}' exists\n";
        } else {
            echo "❌ Table '{$table}' missing\n";
        }
    }
    
    // Test if views exist
    $views = [
        'admin_dashboard_stats',
        'admin_booking_details',
        'repair_workflow_view',
        'admin_repair_stats'
    ];
    
    echo "\nChecking admin views:\n";
    foreach ($views as $view) {
        $stmt = $db->prepare("SHOW TABLES LIKE ?");
        $stmt->execute([$view]);
        if ($stmt->fetch()) {
            echo "✅ View '{$view}' exists\n";
        } else {
            echo "❌ View '{$view}' missing\n";
        }
    }
    
    // Test booking numbers
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM booking_numbers");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "\n📊 Booking numbers available: " . $result['count'] . "\n";
    
    // Test admin settings
    $stmt = $db->prepare("SELECT COUNT(*) as count FROM admin_settings");
    $stmt->execute();
    $result = $stmt->fetch();
    echo "📊 Admin settings configured: " . $result['count'] . "\n";
    
    // Test if bookings table has new columns
    echo "\nChecking bookings table enhancements:\n";
    $columns = ['booking_number_id', 'total_amount', 'payment_status', 'updated_at'];
    foreach ($columns as $column) {
        $stmt = $db->prepare("SHOW COLUMNS FROM bookings LIKE ?");
        $stmt->execute([$column]);
        if ($stmt->fetch()) {
            echo "✅ Column '{$column}' exists in bookings table\n";
        } else {
            echo "❌ Column '{$column}' missing from bookings table\n";
        }
    }
    
    echo "\n🎉 Database setup verification complete!\n";
    echo "\nNext steps:\n";
    echo "1. Run the admin tables SQL script if any tables are missing\n";
    echo "2. Test the admin interface at: " . BASE_URL . "admin/dashboard\n";
    echo "3. Check repair items management at: " . BASE_URL . "admin/repairItems\n";
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    echo "\nPlease check your database configuration in config/database.php\n";
}
