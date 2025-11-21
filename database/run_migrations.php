<?php
/**
 * Database Migration Runner
 * Run this file via command line or web browser to execute migration scripts
 * 
 * Usage:
 *   php database/run_migrations.php
 *   OR visit: http://localhost/UphoCare/database/run_migrations.php
 */

// Database configuration - Load from config if available
if (file_exists(__DIR__ . '/../config/config.php')) {
    require_once __DIR__ . '/../config/config.php';
    $host = defined('DB_HOST') ? DB_HOST : 'localhost';
    $username = defined('DB_USER') ? DB_USER : 'root';
    $password = defined('DB_PASS') ? DB_PASS : '';
    $database = defined('DB_NAME') ? DB_NAME : 'db_upholcare';
} else {
    // Default XAMPP configuration
    $host = 'localhost';
    $username = 'root';
    $password = ''; // XAMPP default is empty
    $database = 'db_upholcare';
}

echo "========================================\n";
echo "Database Migration Runner\n";
echo "========================================\n\n";

try {
    // Connect to MySQL with buffered queries
    $pdo = new PDO("mysql:host=$host", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true
    ]);
    
    echo "✓ Connected to MySQL server\n";
    
    // Select database
    $pdo->exec("USE $database");
    echo "✓ Using database: $database\n\n";
    
    // Migration 1: Add verification code columns to admin_registrations
    echo "========================================\n";
    echo "Migration 1: Add verification code columns\n";
    echo "========================================\n";
    
    $migration1 = file_get_contents(__DIR__ . '/add_verification_code_to_admin_registrations.sql');
    
    // Remove USE statement (we're already using the database)
    $migration1 = preg_replace('/USE\s+[^;]+;/i', '', $migration1);
    
    // Split by semicolon and execute each statement
    $statements = array_filter(array_map('trim', explode(';', $migration1)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^(SELECT|SHOW|DESCRIBE|DESC)/i', $statement)) {
            try {
                $pdo->exec($statement);
                echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
            } catch (PDOException $e) {
                // Check if it's a "duplicate column" error (already exists)
                if (strpos($e->getMessage(), 'Duplicate column name') !== false) {
                    echo "⚠ Column already exists (skipped)\n";
                } elseif (strpos($e->getMessage(), 'already exists') !== false) {
                    echo "⚠ Already exists (skipped)\n";
                } else {
                    echo "✗ Error: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    // Execute SELECT statements to show results
    $selectStatements = array_filter($statements, function($stmt) {
        return preg_match('/^(SELECT)/i', trim($stmt));
    });
    
    foreach ($selectStatements as $statement) {
        try {
            $result = $pdo->query($statement);
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                echo "  " . implode(' | ', $row) . "\n";
            }
        } catch (PDOException $e) {
            // Ignore SELECT errors
        }
    }
    
    echo "\n";
    
    // Migration 2: Create and populate verification codes table
    echo "========================================\n";
    echo "Migration 2: Create verification codes table\n";
    echo "========================================\n";
    
    $migration2 = file_get_contents(__DIR__ . '/setup_verification_codes_complete.sql');
    
    // Remove USE statement
    $migration2 = preg_replace('/USE\s+[^;]+;/i', '', $migration2);
    
    // Handle prepared statements and stored procedures
    // Split by delimiter for stored procedures
    $migration2 = preg_replace('/DELIMITER\s+\$\$.*?DELIMITER\s*;/is', '', $migration2);
    
    // Split by semicolon
    $statements = array_filter(array_map('trim', explode(';', $migration2)));
    
    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^(SELECT|SHOW|DESCRIBE|DESC)/i', $statement)) {
            // Skip SET and PREPARE statements for now
            if (preg_match('/^(SET|PREPARE|EXECUTE|DEALLOCATE)/i', $statement)) {
                continue;
            }
            
            try {
                $pdo->exec($statement);
                if (preg_match('/CREATE TABLE/i', $statement)) {
                    echo "✓ Created table: admin_verification_codes\n";
                } elseif (preg_match('/INSERT/i', $statement)) {
                    echo "✓ Populating verification codes...\n";
                } elseif (preg_match('/ALTER TABLE/i', $statement)) {
                    echo "✓ Altered table structure\n";
                } else {
                    echo "✓ Executed statement\n";
                }
            } catch (PDOException $e) {
                // Check for common errors
                if (strpos($e->getMessage(), 'Duplicate column name') !== false ||
                    strpos($e->getMessage(), 'already exists') !== false ||
                    strpos($e->getMessage(), 'Duplicate key name') !== false) {
                    echo "⚠ Already exists (skipped)\n";
                } else {
                    echo "✗ Error: " . $e->getMessage() . "\n";
                }
            }
        }
    }
    
    // Execute SELECT statements to show results
    $selectStatements = array_filter($statements, function($stmt) {
        return preg_match('/^(SELECT)/i', trim($stmt));
    });
    
    foreach ($selectStatements as $statement) {
        try {
            $result = $pdo->query($statement);
            $rows = $result->fetchAll(PDO::FETCH_ASSOC);
            if (!empty($rows)) {
                echo "\nResults:\n";
                foreach ($rows as $row) {
                    foreach ($row as $key => $value) {
                        echo "  $key: $value\n";
                    }
                }
            }
        } catch (PDOException $e) {
            // Ignore SELECT errors
        }
    }
    
    // Verify the setup
    echo "\n========================================\n";
    echo "Verification\n";
    echo "========================================\n";
    
    // Check admin_registrations table
    try {
        $stmt = $pdo->query("SHOW COLUMNS FROM admin_registrations LIKE 'verification_code'");
        $column = $stmt->fetch();
        if ($column) {
            echo "✓ admin_registrations.verification_code column exists\n";
        } else {
            echo "✗ admin_registrations.verification_code column NOT found\n";
        }
    } catch (PDOException $e) {
        echo "✗ Error checking admin_registrations: " . $e->getMessage() . "\n";
    }
    
    // Check admin_verification_codes table
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_verification_codes");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $count = $result['count'] ?? 0;
        
        if ($count > 0) {
            echo "✓ admin_verification_codes table exists with $count codes\n";
            
            // Check available codes
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM admin_verification_codes WHERE status = 'available'");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $available = $result['count'] ?? 0;
            echo "✓ Available codes: $available\n";
        } else {
            echo "⚠ admin_verification_codes table exists but is empty\n";
        }
    } catch (PDOException $e) {
        echo "✗ admin_verification_codes table NOT found: " . $e->getMessage() . "\n";
    }
    
    echo "\n========================================\n";
    echo "Migration Complete!\n";
    echo "========================================\n";
    
} catch (PDOException $e) {
    echo "✗ Database Error: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
    exit(1);
}

