<?php
/**
 * Admin Tables Setup Script
 * Creates only the admin-specific tables needed for the email notification system
 */

// Include database configuration
require_once '../config/database.php';

echo "=== UphoCare Admin Tables Setup ===\n\n";

try {
    echo "Creating admin-specific tables for email notification system...\n\n";
    
    // Read the admin tables SQL file
    $adminTablesSQL = file_get_contents('create_admin_tables.sql');
    
    if (!$adminTablesSQL) {
        throw new Exception("Could not read create_admin_tables.sql file");
    }
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $adminTablesSQL)));
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue; // Skip empty statements and comments
        }
        
        try {
            $pdo->exec($statement);
            $successCount++;
            
            // Extract table/action name for logging
            if (preg_match('/CREATE TABLE.*`(\w+)`/', $statement, $matches)) {
                echo "✓ Created table: {$matches[1]}\n";
            } elseif (preg_match('/ALTER TABLE.*`(\w+)`/', $statement, $matches)) {
                echo "✓ Modified table: {$matches[1]}\n";
            } elseif (preg_match('/CREATE.*VIEW.*`(\w+)`/', $statement, $matches)) {
                echo "✓ Created view: {$matches[1]}\n";
            } elseif (preg_match('/INSERT INTO.*`(\w+)`/', $statement, $matches)) {
                echo "✓ Inserted data into: {$matches[1]}\n";
            } else {
                echo "✓ Executed SQL statement\n";
            }
            
        } catch (PDOException $e) {
            $errorCount++;
            echo "✗ Error: " . $e->getMessage() . "\n";
            echo "Statement: " . substr($statement, 0, 100) . "...\n";
        }
    }
    
    echo "\n=== Setup Summary ===\n";
    echo "Successful operations: $successCount\n";
    echo "Failed operations: $errorCount\n\n";
    
    if ($errorCount === 0) {
        echo "🎉 Admin tables setup completed successfully!\n\n";
        
        // Verify the setup
        echo "Verifying setup...\n";
        
        $tables = ['booking_numbers', 'email_logs', 'admin_settings', 'admin_activity_log', 'reservation_queue'];
        foreach ($tables as $table) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
            if ($stmt->fetch()) {
                echo "✓ Table '$table' exists\n";
            } else {
                echo "✗ Table '$table' missing\n";
            }
        }
        
        // Check views
        $views = ['admin_dashboard_stats', 'admin_booking_details'];
        foreach ($views as $view) {
            $stmt = $pdo->query("SHOW TABLES LIKE '$view'");
            if ($stmt->fetch()) {
                echo "✓ View '$view' exists\n";
            } else {
                echo "✗ View '$view' missing\n";
            }
        }
        
        echo "\n✅ Admin email notification system is ready!\n\n";
        echo "What was created:\n";
        echo "• booking_numbers - Admin-managed booking numbers\n";
        echo "• email_logs - Track all email notifications\n";
        echo "• admin_settings - Email configuration settings\n";
        echo "• admin_activity_log - Admin action tracking\n";
        echo "• reservation_queue - Manage pending reservations\n";
        echo "• admin_dashboard_stats - Dashboard statistics view\n";
        echo "• admin_booking_details - Complete booking information view\n";
        echo "• Enhanced bookings table with admin fields\n";
        echo "• Enhanced services table with categories\n\n";
        
        echo "Next steps:\n";
        echo "1. Configure email settings in config/email.php\n";
        echo "2. Login as admin and test email notifications\n";
        echo "3. Use the admin panel to manage reservations\n";
        
    } else {
        echo "⚠️ Some operations failed. Please check the errors above.\n";
        echo "You may need to run individual SQL statements manually.\n";
    }
    
} catch (Exception $e) {
    echo "❌ Setup failed: " . $e->getMessage() . "\n";
    echo "Please check your database configuration and permissions.\n";
    exit(1);
}
