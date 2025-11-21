<?php
/**
 * Database Migration Script
 * Run this script to add missing fields to the bookings table
 */

// Include database configuration
require_once '../config/database.php';

try {
    echo "Starting database migration...\n";
    
    // Read the migration SQL file
    $migrationSQL = file_get_contents('migrate_booking_fields.sql');
    
    if (!$migrationSQL) {
        throw new Exception("Could not read migration file");
    }
    
    // Split SQL into individual statements
    $statements = array_filter(array_map('trim', explode(';', $migrationSQL)));
    
    $successCount = 0;
    $errorCount = 0;
    
    foreach ($statements as $statement) {
        if (empty($statement) || strpos($statement, '--') === 0) {
            continue; // Skip empty statements and comments
        }
        
        try {
            $pdo->exec($statement);
            $successCount++;
            echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
        } catch (PDOException $e) {
            $errorCount++;
            echo "✗ Error: " . $e->getMessage() . "\n";
            echo "Statement: " . substr($statement, 0, 100) . "...\n";
        }
    }
    
    echo "\nMigration completed!\n";
    echo "Successful statements: $successCount\n";
    echo "Failed statements: $errorCount\n";
    
    if ($errorCount === 0) {
        echo "\n✅ Database migration successful! The email notification system is now ready to use.\n";
        echo "\nNext steps:\n";
        echo "1. Configure email settings in config/email.php\n";
        echo "2. Test email configuration in admin panel\n";
        echo "3. Start accepting/rejecting reservations\n";
    } else {
        echo "\n⚠️ Some statements failed. Please check the errors above.\n";
    }
    
} catch (Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
