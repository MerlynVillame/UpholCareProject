<?php
/**
 * Database Configuration for UpholCare
 * 
 * This file contains database connection settings for the existing upholcare_customers database.
 */

// Database configuration - only define if not already defined
if (!defined('DB_HOST')) {
    define('DB_HOST', 'localhost');
}
if (!defined('DB_NAME')) {
    define('DB_NAME', 'upholcare_customers');
}
if (!defined('DB_USER')) {
    define('DB_USER', 'upholcare');
}
if (!defined('DB_PASS')) {
    define('DB_PASS', 'uc');
}
if (!defined('DB_CHARSET')) {
    define('DB_CHARSET', 'utf8mb4');
}

// Database connection options - only define if not already defined
if (!defined('DB_OPTIONS')) {
    define('DB_OPTIONS', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
    ]);
}

// Application settings - only define if not already defined
if (!defined('APP_NAME')) {
    define('APP_NAME', 'UpholCare');
}
if (!defined('APP_VERSION')) {
    define('APP_VERSION', '1.0.0');
}
if (!defined('APP_DEBUG')) {
    define('APP_DEBUG', true); // Set to false in production
}

// Timezone
date_default_timezone_set('Asia/Manila'); // Adjust as needed

?>
