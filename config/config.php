<?php
/**
 * UpholCare Configuration File
 */

// Base URL - adjust this to your local setup
define('BASE_URL', 'http://localhost/UphoCare/');

// Application Settings
define('APP_NAME', 'UpholCare');
define('APP_DESC', 'Repair and Restoration Management System');

// Database Settings
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_upholcare');

// Session Settings
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_CUSTOMER', 'customer');
define('ROLE_CONTROL_PANEL_ADMIN', 'control_panel_admin');

// Admin Verification Key (Required for admin registration)
define('ADMIN_REGISTRATION_KEY', 'UpholCare2024Admin!Secure@Key#987');

// Timezone
date_default_timezone_set('Asia/Manila');

// Map Configuration
// NOTE: The store locations page now uses Leaflet + OpenStreetMap (free, no API key required)
// If you want to use Google Maps instead, uncomment the line below and add your API key
// define('GOOGLE_MAPS_API_KEY', ''); // Google Maps API key (not currently used)

// Development Mode (set to false in production)
// When true, shows detailed error messages and warnings
define('DEBUG_MODE', true);
