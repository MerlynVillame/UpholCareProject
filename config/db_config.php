<?php
/**
 * Database Configuration for UpholCare
 * 
 * This file contains database connection settings for the existing upholcare_customers database.
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'upholcare_customers');
define('DB_USER', 'upholcare');
define('DB_PASS', 'uc');
define('DB_CHARSET', 'utf8mb4');

// Database connection options
define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
]);

// Application settings
define('APP_NAME', 'UpholCare');
define('APP_VERSION', '1.0.0');
define('APP_DEBUG', true); // Set to false in production

// Timezone
date_default_timezone_set('Asia/Manila'); // Adjust as needed

?>
