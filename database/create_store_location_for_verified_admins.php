<?php
/**
 * Utility Script: Create Store Locations for Verified Admins
 * 
 * This script checks all verified admin registrations and ensures their store locations
 * are created in the store_locations table. It will:
 * 1. Find all approved admin registrations with business information
 * 2. Geocode their addresses if coordinates are missing
 * 3. Create or update store locations in store_locations table
 * 
 * Usage: Run this script in your browser: http://localhost/UphoCare/database/create_store_location_for_verified_admins.php
 */

// Set error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Define root path
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
if (!defined('ROOT')) {
    define('ROOT', dirname(dirname(__FILE__)));
}

// Load configuration first (defines DB constants)
require_once ROOT . DS . 'config' . DS . 'config.php';

// Load database class (Database class is in config/database.php)
require_once ROOT . DS . 'config' . DS . 'database.php';

// Load geocoding service
require_once ROOT . DS . 'core' . DS . 'GeocodingService.php';

// Start session if not started (required for some functions)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get database connection
$db = Database::getInstance()->getConnection();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Store Locations for Verified Admins</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .status-badge {
            padding: 5px 10px;
            border-radius: 4px;
            font-size: 0.875rem;
            font-weight: 600;
        }
        .status-success { background-color: #d4edda; color: #155724; }
        .status-warning { background-color: #fff3cd; color: #856404; }
        .status-error { background-color: #f8d7da; color: #721c24; }
        .status-info { background-color: #d1ecf1; color: #0c5460; }
        table {
            margin-top: 20px;
        }
        .action-btn {
            margin: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="mb-4">Create Store Locations for Verified Admins</h1>
        
        <?php
        $action = $_GET['action'] ?? 'list';
        $adminId = $_GET['admin_id'] ?? null;
        
        if ($action === 'process' && $adminId) {
            // Process a specific admin
            processAdminStoreLocation($db, $adminId);
        } elseif ($action === 'process_all') {
            // Process all verified admins
            processAllVerifiedAdmins($db);
        } else {
            // List all verified admins
            listVerifiedAdmins($db);
        }
        
        /**
         * List all verified admins with business information
         */
        function listVerifiedAdmins($db) {
            try {
                // Get all approved admin registrations with business information
                $stmt = $db->query("
                    SELECT ar.*, 
                           u.status as user_status,
                           (SELECT COUNT(*) FROM store_locations WHERE email = ar.email) as store_exists
                    FROM admin_registrations ar
                    LEFT JOIN users u ON u.email = ar.email AND u.role = 'admin'
                    WHERE ar.registration_status = 'approved'
                      AND ar.business_name IS NOT NULL
                      AND ar.business_name != ''
                      AND ar.business_address IS NOT NULL
                      AND ar.business_address != ''
                    ORDER BY ar.created_at DESC
                ");
                $admins = $stmt->fetchAll();
                
                if (empty($admins)) {
                    echo '<div class="alert alert-info">No verified admins with business information found.</div>';
                    return;
                }
                
                echo '<div class="alert alert-info">';
                echo '<strong>Found ' . count($admins) . ' verified admin(s) with business information.</strong><br>';
                echo 'Click "Process All" to create store locations for all admins, or process them individually.';
                echo '</div>';
                
                echo '<div class="mb-3">';
                echo '<a href="?action=process_all" class="btn btn-primary action-btn" onclick="return confirm(\'This will process all verified admins. Continue?\')">';
                echo 'Process All Admins</a>';
                echo '</div>';
                
                echo '<table class="table table-striped table-bordered">';
                echo '<thead class="table-dark">';
                echo '<tr>';
                echo '<th>ID</th>';
                echo '<th>Business Name</th>';
                echo '<th>Email</th>';
                echo '<th>Address</th>';
                echo '<th>City/Province</th>';
                echo '<th>Coordinates</th>';
                echo '<th>User Status</th>';
                echo '<th>Store Exists</th>';
                echo '<th>Actions</th>';
                echo '</tr>';
                echo '</thead>';
                echo '<tbody>';
                
                foreach ($admins as $admin) {
                    $hasCoordinates = !empty($admin['business_latitude']) && !empty($admin['business_longitude']);
                    $storeExists = $admin['store_exists'] > 0;
                    $userStatus = $admin['user_status'] ?? 'unknown';
                    
                    echo '<tr>';
                    echo '<td>' . htmlspecialchars($admin['id']) . '</td>';
                    echo '<td><strong>' . htmlspecialchars($admin['business_name']) . '</strong></td>';
                    echo '<td>' . htmlspecialchars($admin['email']) . '</td>';
                    echo '<td>' . htmlspecialchars($admin['business_address']) . '</td>';
                    echo '<td>' . htmlspecialchars($admin['business_city'] ?? 'Bohol') . ', ' . htmlspecialchars($admin['business_province'] ?? 'Bohol') . '</td>';
                    echo '<td>';
                    if ($hasCoordinates) {
                        echo '<span class="status-badge status-success">';
                        echo htmlspecialchars($admin['business_latitude']) . ', ' . htmlspecialchars($admin['business_longitude']);
                        echo '</span>';
                    } else {
                        echo '<span class="status-badge status-warning">Not Geocoded</span>';
                    }
                    echo '</td>';
                    echo '<td>';
                    if ($userStatus === 'active') {
                        echo '<span class="status-badge status-success">Active</span>';
                    } else {
                        echo '<span class="status-badge status-warning">' . htmlspecialchars($userStatus) . '</span>';
                    }
                    echo '</td>';
                    echo '<td>';
                    if ($storeExists) {
                        echo '<span class="status-badge status-success">Yes</span>';
                    } else {
                        echo '<span class="status-badge status-error">No</span>';
                    }
                    echo '</td>';
                    echo '<td>';
                    echo '<a href="?action=process&admin_id=' . $admin['id'] . '" class="btn btn-sm btn-primary action-btn">';
                    echo 'Create/Update Store</a>';
                    echo '</td>';
                    echo '</tr>';
                }
                
                echo '</tbody>';
                echo '</table>';
                
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">';
                echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
                echo '</div>';
            }
        }
        
        /**
         * Process a specific admin registration
         */
        function processAdminStoreLocation($db, $adminId) {
            try {
                // Get admin registration
                $stmt = $db->prepare("
                    SELECT * FROM admin_registrations 
                    WHERE id = ? AND registration_status = 'approved'
                ");
                $stmt->execute([$adminId]);
                $admin = $stmt->fetch();
                
                if (!$admin) {
                    echo '<div class="alert alert-danger">Admin registration not found or not approved.</div>';
                    echo '<a href="?" class="btn btn-secondary">Back to List</a>';
                    return;
                }
                
                if (empty($admin['business_name']) || empty($admin['business_address'])) {
                    echo '<div class="alert alert-warning">Admin does not have business name or address.</div>';
                    echo '<a href="?" class="btn btn-secondary">Back to List</a>';
                    return;
                }
                
                echo '<div class="alert alert-info">';
                echo '<h4>Processing: ' . htmlspecialchars($admin['business_name']) . '</h4>';
                echo '<p><strong>Email:</strong> ' . htmlspecialchars($admin['email']) . '</p>';
                echo '<p><strong>Address:</strong> ' . htmlspecialchars($admin['business_address']) . '</p>';
                echo '</div>';
                
                $results = [];
                
                // Step 1: Geocode address if coordinates are missing
                $latitude = $admin['business_latitude'] ?? null;
                $longitude = $admin['business_longitude'] ?? null;
                
                if (empty($latitude) || empty($longitude)) {
                    $results[] = '<strong>Step 1:</strong> Geocoding address...';
                    try {
                        $coordinates = GeocodingService::geocodeAddressWithRetry(
                            $admin['business_address'],
                            $admin['business_city'] ?? 'Bohol',
                            $admin['business_province'] ?? 'Bohol'
                        );
                        
                        if ($coordinates !== null) {
                            $latitude = $coordinates['lat'];
                            $longitude = $coordinates['lng'];
                            
                            // Update admin_registrations with coordinates
                            $updateStmt = $db->prepare("
                                UPDATE admin_registrations 
                                SET business_latitude = ?, business_longitude = ?, updated_at = NOW()
                                WHERE id = ?
                            ");
                            $updateStmt->execute([$latitude, $longitude, $adminId]);
                            
                            $results[] = '<span class="status-badge status-success">✓ Geocoded successfully: ' . $latitude . ', ' . $longitude . '</span>';
                        } else {
                            // Use default coordinates
                            $defaultCoords = GeocodingService::getDefaultBoholCoordinates();
                            $latitude = $defaultCoords['lat'];
                            $longitude = $defaultCoords['lng'];
                            $results[] = '<span class="status-badge status-warning">⚠ Geocoding failed, using default Bohol coordinates: ' . $latitude . ', ' . $longitude . '</span>';
                        }
                    } catch (Exception $e) {
                        $defaultCoords = GeocodingService::getDefaultBoholCoordinates();
                        $latitude = $defaultCoords['lat'];
                        $longitude = $defaultCoords['lng'];
                        $results[] = '<span class="status-badge status-error">✗ Geocoding error: ' . htmlspecialchars($e->getMessage()) . '. Using default coordinates.</span>';
                    }
                } else {
                    $results[] = '<strong>Step 1:</strong> Coordinates already exist: ' . $latitude . ', ' . $longitude;
                }
                
                // Step 2: Verify coordinates are valid
                if (empty($latitude) || empty($longitude) || 
                    $latitude < 9.0 || $latitude > 10.5 || 
                    $longitude < 123.0 || $longitude > 125.0) {
                    $results[] = '<span class="status-badge status-error">✗ Invalid coordinates. Store location not created.</span>';
                    echo '<div class="alert alert-danger">';
                    echo '<h5>Processing Results:</h5>';
                    echo '<ul>';
                    foreach ($results as $result) {
                        echo '<li>' . $result . '</li>';
                    }
                    echo '</ul>';
                    echo '</div>';
                    echo '<a href="?" class="btn btn-secondary">Back to List</a>';
                    return;
                }
                
                // Step 3: Check if store location already exists
                $checkStmt = $db->prepare("
                    SELECT id FROM store_locations 
                    WHERE email = ? OR (store_name = ? AND address = ?)
                ");
                $checkStmt->execute([
                    $admin['email'],
                    $admin['business_name'],
                    $admin['business_address']
                ]);
                $existingStore = $checkStmt->fetch();
                
                // Step 4: Create or update store location
                $results[] = '<strong>Step 2:</strong> Creating/updating store location...';
                
                if (!$existingStore) {
                    // Create new store location
                    $insertStmt = $db->prepare("
                        INSERT INTO store_locations 
                        (store_name, address, city, province, latitude, longitude, 
                         phone, email, status, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())
                    ");
                    $insertStmt->execute([
                        $admin['business_name'],
                        $admin['business_address'],
                        $admin['business_city'] ?? 'Bohol',
                        $admin['business_province'] ?? 'Bohol',
                        $latitude,
                        $longitude,
                        $admin['phone'] ?? '',
                        $admin['email']
                    ]);
                    $results[] = '<span class="status-badge status-success">✓ Store location created successfully!</span>';
                } else {
                    // Update existing store location
                    $updateStmt = $db->prepare("
                        UPDATE store_locations 
                        SET store_name = ?, address = ?, city = ?, province = ?, 
                            latitude = ?, longitude = ?, phone = ?, status = 'active', 
                            updated_at = NOW()
                        WHERE id = ?
                    ");
                    $updateStmt->execute([
                        $admin['business_name'],
                        $admin['business_address'],
                        $admin['business_city'] ?? 'Bohol',
                        $admin['business_province'] ?? 'Bohol',
                        $latitude,
                        $longitude,
                        $admin['phone'] ?? '',
                        $existingStore['id']
                    ]);
                    $results[] = '<span class="status-badge status-success">✓ Store location updated successfully!</span>';
                }
                
                echo '<div class="alert alert-success">';
                echo '<h5>Processing Results:</h5>';
                echo '<ul>';
                foreach ($results as $result) {
                    echo '<li>' . $result . '</li>';
                }
                echo '</ul>';
                echo '</div>';
                
                echo '<a href="?" class="btn btn-primary">Back to List</a>';
                
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">';
                echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
                echo '</div>';
                echo '<a href="?" class="btn btn-secondary">Back to List</a>';
            }
        }
        
        /**
         * Process all verified admins
         */
        function processAllVerifiedAdmins($db) {
            try {
                // Get all approved admin registrations with business information
                $stmt = $db->query("
                    SELECT id FROM admin_registrations 
                    WHERE registration_status = 'approved'
                      AND business_name IS NOT NULL
                      AND business_name != ''
                      AND business_address IS NOT NULL
                      AND business_address != ''
                    ORDER BY created_at DESC
                ");
                $admins = $stmt->fetchAll();
                
                if (empty($admins)) {
                    echo '<div class="alert alert-info">No verified admins with business information found.</div>';
                    echo '<a href="?" class="btn btn-secondary">Back to List</a>';
                    return;
                }
                
                echo '<div class="alert alert-info">';
                echo '<h4>Processing ' . count($admins) . ' admin(s)...</h4>';
                echo '</div>';
                
                $successCount = 0;
                $errorCount = 0;
                $results = [];
                
                foreach ($admins as $admin) {
                    try {
                        // Process each admin (reuse the processAdminStoreLocation logic)
                        $adminId = $admin['id'];
                        
                        // Get admin registration
                        $getStmt = $db->prepare("SELECT * FROM admin_registrations WHERE id = ?");
                        $getStmt->execute([$adminId]);
                        $adminData = $getStmt->fetch();
                        
                        if (!$adminData) continue;
                        
                        // Geocode if needed
                        $latitude = $adminData['business_latitude'] ?? null;
                        $longitude = $adminData['business_longitude'] ?? null;
                        
                        if (empty($latitude) || empty($longitude)) {
                            $coordinates = GeocodingService::geocodeAddressWithRetry(
                                $adminData['business_address'],
                                $adminData['business_city'] ?? 'Bohol',
                                $adminData['business_province'] ?? 'Bohol'
                            );
                            
                            if ($coordinates !== null) {
                                $latitude = $coordinates['lat'];
                                $longitude = $coordinates['lng'];
                                
                                $updateStmt = $db->prepare("
                                    UPDATE admin_registrations 
                                    SET business_latitude = ?, business_longitude = ?, updated_at = NOW()
                                    WHERE id = ?
                                ");
                                $updateStmt->execute([$latitude, $longitude, $adminId]);
                            } else {
                                $defaultCoords = GeocodingService::getDefaultBoholCoordinates();
                                $latitude = $defaultCoords['lat'];
                                $longitude = $defaultCoords['lng'];
                            }
                        }
                        
                        // Verify coordinates
                        if (empty($latitude) || empty($longitude) || 
                            $latitude < 9.0 || $latitude > 10.5 || 
                            $longitude < 123.0 || $longitude > 125.0) {
                            $results[] = '<span class="status-badge status-error">✗ ' . htmlspecialchars($adminData['business_name']) . ': Invalid coordinates</span>';
                            $errorCount++;
                            continue;
                        }
                        
                        // Check if store exists
                        $checkStmt = $db->prepare("
                            SELECT id FROM store_locations 
                            WHERE email = ? OR (store_name = ? AND address = ?)
                        ");
                        $checkStmt->execute([
                            $adminData['email'],
                            $adminData['business_name'],
                            $adminData['business_address']
                        ]);
                        $existingStore = $checkStmt->fetch();
                        
                        // Create or update store
                        if (!$existingStore) {
                            $insertStmt = $db->prepare("
                                INSERT INTO store_locations 
                                (store_name, address, city, province, latitude, longitude, 
                                 phone, email, status, created_at, updated_at) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())
                            ");
                            $insertStmt->execute([
                                $adminData['business_name'],
                                $adminData['business_address'],
                                $adminData['business_city'] ?? 'Bohol',
                                $adminData['business_province'] ?? 'Bohol',
                                $latitude,
                                $longitude,
                                $adminData['phone'] ?? '',
                                $adminData['email']
                            ]);
                            $results[] = '<span class="status-badge status-success">✓ ' . htmlspecialchars($adminData['business_name']) . ': Created</span>';
                        } else {
                            $updateStmt = $db->prepare("
                                UPDATE store_locations 
                                SET store_name = ?, address = ?, city = ?, province = ?, 
                                    latitude = ?, longitude = ?, phone = ?, status = 'active', 
                                    updated_at = NOW()
                                WHERE id = ?
                            ");
                            $updateStmt->execute([
                                $adminData['business_name'],
                                $adminData['business_address'],
                                $adminData['business_city'] ?? 'Bohol',
                                $adminData['business_province'] ?? 'Bohol',
                                $latitude,
                                $longitude,
                                $adminData['phone'] ?? '',
                                $existingStore['id']
                            ]);
                            $results[] = '<span class="status-badge status-success">✓ ' . htmlspecialchars($adminData['business_name']) . ': Updated</span>';
                        }
                        
                        $successCount++;
                        
                        // Small delay to avoid rate limiting
                        sleep(1);
                        
                    } catch (Exception $e) {
                        $results[] = '<span class="status-badge status-error">✗ ' . htmlspecialchars($adminData['business_name'] ?? 'Unknown') . ': ' . htmlspecialchars($e->getMessage()) . '</span>';
                        $errorCount++;
                    }
                }
                
                echo '<div class="alert alert-success">';
                echo '<h5>Processing Complete!</h5>';
                echo '<p><strong>Success:</strong> ' . $successCount . ' store(s) created/updated</p>';
                echo '<p><strong>Errors:</strong> ' . $errorCount . '</p>';
                echo '</div>';
                
                echo '<div class="alert alert-info">';
                echo '<h5>Results:</h5>';
                echo '<ul>';
                foreach ($results as $result) {
                    echo '<li>' . $result . '</li>';
                }
                echo '</ul>';
                echo '</div>';
                
                echo '<a href="?" class="btn btn-primary">Back to List</a>';
                
            } catch (Exception $e) {
                echo '<div class="alert alert-danger">';
                echo '<strong>Error:</strong> ' . htmlspecialchars($e->getMessage());
                echo '</div>';
                echo '<a href="?" class="btn btn-secondary">Back to List</a>';
            }
        }
        ?>
    </div>
</body>
</html>

