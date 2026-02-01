<?php
/**
 * Auto Update Booking Status - API Endpoint
 * 
 * This endpoint can be called via HTTP request (e.g., from a cron job using curl or wget)
 * 
 * Usage:
 * - Via curl: curl http://localhost/UphoCare/api/auto_update_status.php
 * - Via wget: wget -q -O - http://localhost/UphoCare/api/auto_update_status.php
 * - Via cron: 0 0 * * * curl -s http://localhost/UphoCare/api/auto_update_status.php > /dev/null
 * 
 * Security: You may want to add authentication (API key) for production use
 */

// Set execution time limit
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

// Set JSON response header
header('Content-Type: application/json');

// Optional: Add API key authentication (uncomment and set your API key)
/*
$apiKey = $_GET['api_key'] ?? $_POST['api_key'] ?? '';
$validApiKey = 'your-secret-api-key-here'; // Change this to a secure key

if ($apiKey !== $validApiKey) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized. Invalid API key.'
    ]);
    exit;
}
*/

try {
    // Run the automatic status update service
    $stats = AutoStatusUpdateService::run();
    
    // Return JSON response
    echo json_encode([
        'success' => true,
        'message' => 'Status update completed',
        'stats' => $stats,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Error updating statuses: ' . $e->getMessage(),
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_PRETTY_PRINT);
}

