<?php
/**
 * Auto Update Booking Status - Cron Job Script
 * 
 * This script automatically updates booking statuses based on pickup_date
 * 
 * Usage:
 * - Add to crontab to run daily: 0 0 * * * php /path/to/UphoCare/cron/auto_update_booking_status.php
 * - Or run hourly: 0 * * * * php /path/to/UphoCare/cron/auto_update_booking_status.php
 * 
 * For Windows Task Scheduler:
 * - Create a scheduled task that runs: php.exe "C:\xampp\htdocs\UphoCare\cron\auto_update_booking_status.php"
 */

// Set execution time limit (5 minutes should be enough)
set_time_limit(300);

// Set memory limit
ini_set('memory_limit', '128M');

// Define root directory
define('ROOT', dirname(__DIR__));
define('DS', DIRECTORY_SEPARATOR);

// Include configuration
require_once ROOT . DS . 'config' . DS . 'config.php';
require_once ROOT . DS . 'config' . DS . 'database.php';
require_once ROOT . DS . 'core' . DS . 'AutoStatusUpdateService.php';

// Set timezone
date_default_timezone_set('Asia/Manila');

// Log file path
$logFile = ROOT . DS . 'logs' . DS . 'auto_status_update.log';

// Create logs directory if it doesn't exist
$logsDir = dirname($logFile);
if (!is_dir($logsDir)) {
    mkdir($logsDir, 0755, true);
}

/**
 * Log message to file and console
 */
function logMessage($message, $logFile) {
    $timestamp = date('Y-m-d H:i:s');
    $logEntry = "[{$timestamp}] {$message}\n";
    
    // Write to log file
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    // Also output to console if running from command line
    if (php_sapi_name() === 'cli') {
        echo $logEntry;
    }
}

// Start execution
logMessage("=== Auto Status Update Cron Job Started ===", $logFile);

try {
    // Run the automatic status update service
    $stats = AutoStatusUpdateService::run();
    
    // Log results
    logMessage("Status Update Completed:", $logFile);
    logMessage("  - Bookings checked: {$stats['checked']}", $logFile);
    logMessage("  - Bookings updated: {$stats['updated']}", $logFile);
    logMessage("  - Errors: {$stats['errors']}", $logFile);
    
    // Log details of updated bookings
    if (!empty($stats['details'])) {
        logMessage("Updated Bookings:", $logFile);
        foreach ($stats['details'] as $detail) {
            logMessage("  - Booking #{$detail['booking_id']}: {$detail['old_status']} → {$detail['new_status']} (pickup_date: {$detail['pickup_date']})", $logFile);
        }
    }
    
    logMessage("=== Auto Status Update Cron Job Completed Successfully ===", $logFile);
    
} catch (Exception $e) {
    logMessage("ERROR: " . $e->getMessage(), $logFile);
    logMessage("Stack trace: " . $e->getTraceAsString(), $logFile);
    logMessage("=== Auto Status Update Cron Job Failed ===", $logFile);
    exit(1);
}

exit(0);

