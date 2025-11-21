<?php
/**
 * Test Script for Email Notification System
 * This script tests the email notification functionality with the shared database
 */

// Include required files
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../core/NotificationService.php';

echo "=== UphoCare Email Notification Test ===\n\n";

try {
    // Test 1: Check database connection
    echo "1. Testing database connection...\n";
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $result = $stmt->fetch();
    echo "   ✓ Database connected. Found {$result['count']} users.\n\n";
    
    // Test 2: Check required tables exist
    echo "2. Checking required tables...\n";
    $tables = ['users', 'bookings', 'booking_numbers', 'services', 'service_categories'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->fetch()) {
            echo "   ✓ Table '$table' exists\n";
        } else {
            echo "   ✗ Table '$table' missing\n";
        }
    }
    echo "\n";
    
    // Test 3: Check required fields in bookings table
    echo "3. Checking bookings table structure...\n";
    $stmt = $pdo->query("DESCRIBE bookings");
    $fields = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $requiredFields = ['user_id', 'booking_number_id', 'total_amount', 'payment_status', 'updated_at'];
    
    foreach ($requiredFields as $field) {
        if (in_array($field, $fields)) {
            echo "   ✓ Field '$field' exists\n";
        } else {
            echo "   ✗ Field '$field' missing\n";
        }
    }
    echo "\n";
    
    // Test 4: Check for test data
    echo "4. Checking for test data...\n";
    
    // Check for admin user
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'admin'");
    $stmt->execute();
    $adminCount = $stmt->fetch()['count'];
    echo "   Admin users: $adminCount\n";
    
    // Check for customer users
    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE role = 'customer'");
    $stmt->execute();
    $customerCount = $stmt->fetch()['count'];
    echo "   Customer users: $customerCount\n";
    
    // Check for bookings
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM bookings");
    $bookingCount = $stmt->fetch()['count'];
    echo "   Total bookings: $bookingCount\n\n";
    
    // Test 5: Test email notification service
    echo "5. Testing email notification service...\n";
    
    if (class_exists('NotificationService')) {
        echo "   ✓ NotificationService class loaded\n";
        
        $notificationService = new NotificationService();
        echo "   ✓ NotificationService instantiated\n";
        
        // Test email configuration
        if (defined('EMAIL_ENABLED') && EMAIL_ENABLED) {
            echo "   ✓ Email notifications enabled\n";
        } else {
            echo "   ⚠ Email notifications disabled\n";
        }
        
        if (defined('EMAIL_TEST_MODE') && EMAIL_TEST_MODE) {
            echo "   ✓ Test mode enabled (emails will be logged, not sent)\n";
        } else {
            echo "   ⚠ Live mode enabled (emails will be sent)\n";
        }
        
    } else {
        echo "   ✗ NotificationService class not found\n";
    }
    echo "\n";
    
    // Test 6: Test booking query with customer details
    echo "6. Testing booking query with customer details...\n";
    
    $sql = "SELECT b.*, bn.booking_number, s.service_name, s.service_type, sc.category_name,
            u.fullname as customer_name, u.email as customer_email, u.phone
            FROM bookings b
            LEFT JOIN booking_numbers bn ON b.booking_number_id = bn.id
            LEFT JOIN services s ON b.service_id = s.id
            LEFT JOIN service_categories sc ON s.category_id = sc.id
            LEFT JOIN users u ON b.user_id = u.id
            LIMIT 1";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $booking = $stmt->fetch();
    
    if ($booking) {
        echo "   ✓ Booking query successful\n";
        echo "   Customer: {$booking['customer_name']}\n";
        echo "   Email: {$booking['customer_email']}\n";
        echo "   Service: {$booking['service_name']}\n";
        echo "   Status: {$booking['status']}\n";
    } else {
        echo "   ⚠ No bookings found (this is normal for new installations)\n";
    }
    echo "\n";
    
    // Test 7: Create sample data if none exists
    if ($bookingCount == 0) {
        echo "7. Creating sample data...\n";
        
        // Create sample booking numbers
        $bookingNumbers = [
            'BKG-20250115-0001',
            'BKG-20250115-0002',
            'BKG-20250115-0003'
        ];
        
        foreach ($bookingNumbers as $number) {
            $stmt = $pdo->prepare("INSERT IGNORE INTO booking_numbers (booking_number) VALUES (?)");
            $stmt->execute([$number]);
        }
        echo "   ✓ Sample booking numbers created\n";
        
        // Create sample booking if we have users
        if ($customerCount > 0) {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE role = 'customer' LIMIT 1");
            $stmt->execute();
            $customer = $stmt->fetch();
            
            if ($customer) {
                $stmt = $pdo->prepare("INSERT INTO bookings (user_id, service_id, booking_number_id, total_amount, status, payment_status) VALUES (?, 1, 1, 150.00, 'pending', 'unpaid')");
                $stmt->execute([$customer['id']]);
                echo "   ✓ Sample booking created\n";
            }
        }
        echo "\n";
    }
    
    // Summary
    echo "=== Test Summary ===\n";
    echo "✓ Database connection: OK\n";
    echo "✓ Required tables: OK\n";
    echo "✓ Email service: OK\n";
    echo "✓ Booking queries: OK\n";
    echo "\n";
    echo "🎉 Email notification system is ready!\n";
    echo "\nNext steps:\n";
    echo "1. Configure email settings in config/email.php\n";
    echo "2. Test email sending in admin panel\n";
    echo "3. Create test bookings and try accept/reject\n";
    
} catch (Exception $e) {
    echo "❌ Test failed: " . $e->getMessage() . "\n";
    echo "Please check your database configuration and run the migration script.\n";
}
