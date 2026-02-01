<?php
/**
 * Run Repair Workflow Migration
 * Adds repair_days, repair_start_date columns and new statuses
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = Database::getInstance()->getConnection();
    
    echo "Starting Repair Workflow Migration...\n\n";
    
    // Read and execute SQL file
    $sqlFile = __DIR__ . '/add_repair_workflow_columns.sql';
    
    if (!file_exists($sqlFile)) {
        die("Error: SQL file not found: {$sqlFile}\n");
    }
    
    $sql = file_get_contents($sqlFile);
    
    // Remove USE statement and comments, split by semicolons
    $sql = preg_replace('/USE\s+[^;]+;/i', '', $sql);
    $sql = preg_replace('/--.*$/m', '', $sql);
    
    // Split into individual statements
    $statements = array_filter(array_map('trim', explode(';', $sql)));
    
    foreach ($statements as $statement) {
        if (empty($statement)) {
            continue;
        }
        
        try {
            echo "Executing: " . substr($statement, 0, 60) . "...\n";
            $db->exec($statement);
            echo "✓ Success\n\n";
        } catch (PDOException $e) {
            // Check if error is about column/status already existing
            if (strpos($e->getMessage(), 'Duplicate column') !== false || 
                strpos($e->getMessage(), 'already exists') !== false ||
                strpos($e->getMessage(), 'Duplicate entry') !== false) {
                echo "⚠ Warning (column/status may already exist): " . $e->getMessage() . "\n\n";
            } else {
                echo "✗ Error: " . $e->getMessage() . "\n\n";
            }
        }
    }
    
    echo "Migration completed!\n";
    echo "\nVerifying changes...\n";
    
    // Check if columns exist
    $stmt = $db->query("SHOW COLUMNS FROM bookings WHERE Field IN ('repair_days', 'repair_start_date')");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "\nColumns added:\n";
    foreach ($columns as $col) {
        echo "  ✓ {$col['Field']} - {$col['Type']}\n";
    }
    
    // Check status ENUM
    $stmt = $db->query("SHOW COLUMNS FROM bookings WHERE Field = 'status'");
    $statusCol = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo "\nStatus ENUM includes:\n";
    if (strpos($statusCol['Type'], 'start_repair') !== false) {
        echo "  ✓ start_repair\n";
    } else {
        echo "  ✗ start_repair (missing)\n";
    }
    
    if (strpos($statusCol['Type'], 'repair_completed') !== false) {
        echo "  ✓ repair_completed\n";
    } else {
        echo "  ✗ repair_completed (missing)\n";
    }
    
    if (strpos($statusCol['Type'], 'repair_completed_ready_to_deliver') !== false) {
        echo "  ✓ repair_completed_ready_to_deliver\n";
    } else {
        echo "  ✗ repair_completed_ready_to_deliver (missing)\n";
    }
    
    echo "\n✓ Migration verification complete!\n";
    
} catch (Exception $e) {
    echo "Fatal error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
    exit(1);
}

