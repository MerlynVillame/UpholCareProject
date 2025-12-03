<?php
/**
 * Admin Controller
 */

require_once ROOT . DS . 'core' . DS . 'Controller.php';
require_once ROOT . DS . 'helpers' . DS . 'FeeCalculator.php';

class AdminController extends Controller {
    
    private $bookingModel;
    
    public function __construct() {
        // SECURITY: Require admin role and verify role integrity for all admin pages
        $this->requireAdmin();
        $this->verifyRoleIntegrity();
        $this->bookingModel = $this->model('Booking');
    }
    
    /**
     * Admin Dashboard
     */
    public function dashboard() {
        // Get booking statistics
        $totalBookings = $this->bookingModel->getTotalBookings();
        $pendingBookings = $this->bookingModel->getBookingCountByStatus(null, 'pending');
        $completedBookings = $this->bookingModel->getBookingCountByStatus(null, 'completed');
        $totalRevenue = $this->bookingModel->getTotalRevenue();
        
        // Get recent bookings
        $recentBookings = $this->bookingModel->getRecentBookings(null, 10);
        
        $data = [
            'title' => 'Admin Dashboard - ' . APP_NAME,
            'user' => $this->currentUser(),
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings,
            'completedBookings' => $completedBookings,
            'totalRevenue' => $totalRevenue,
            'recentBookings' => $recentBookings
        ];
        
        $this->view('admin/dashboard', $data);
    }
    
    /**
     * Manage Orders
     */
    public function orders() {
        $data = [
            'title' => 'Manage Orders - ' . APP_NAME,
            'user' => $this->currentUser()
        ];
        
        $this->view('admin/orders', $data);
    }
    
    /**
     * Reports
     */
    public function reports($year = null) {
        $db = Database::getInstance()->getConnection();
        
        // Get year from parameter or use current year
        $selectedYear = $year ? (int)$year : (int)date('Y');
        $currentYear = (int)date('Y');
        
        // Get monthly sales data from database
        $monthlySalesData = [];
        $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                      'July', 'August', 'September', 'October', 'November', 'December'];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = sprintf('%04d-%02d-01', $selectedYear, $month);
            $monthEnd = sprintf('%04d-%02d-%d', $selectedYear, $month, date('t', strtotime($monthStart)));
            
            // Get completed bookings for this month (status = completed AND payment_status = paid)
            // Use updated_at for completion date, fallback to created_at
            // Join with users and services tables to get customer name and service details
            $sql = "SELECT 
                        COUNT(*) as orders,
                        COALESCE(SUM(b.total_amount), 0) as revenue,
                        GROUP_CONCAT(
                            CONCAT(
                                'ID:', b.id, 
                                '|Amount:', b.total_amount, 
                                '|Date:', COALESCE(b.updated_at, b.created_at),
                                '|Customer:', COALESCE(u.fullname, 'Unknown'),
                                '|Service:', COALESCE(s.service_name, 'N/A'),
                                '|Item:', COALESCE(b.item_description, 'N/A'),
                                '|ItemType:', COALESCE(b.item_type, 'N/A'),
                                '|ServiceType:', COALESCE(b.service_type, 'N/A'),
                                '|Notes:', COALESCE(b.notes, 'N/A')
                            ) SEPARATOR ';;'
                        ) as booking_details
                    FROM bookings b
                    LEFT JOIN users u ON b.user_id = u.id
                    LEFT JOIN services s ON b.service_id = s.id
                    WHERE b.status = 'completed' 
                    AND b.payment_status IN ('paid', 'paid_full_cash', 'paid_on_delivery_cod')
                    AND (
                        (b.updated_at IS NOT NULL AND DATE(b.updated_at) BETWEEN ? AND ?)
                        OR (b.updated_at IS NULL AND DATE(b.created_at) BETWEEN ? AND ?)
                    )";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$monthStart, $monthEnd, $monthStart, $monthEnd]);
            $result = $stmt->fetch();
            
            $orders = (int)($result['orders'] ?? 0);
            $revenue = (float)($result['revenue'] ?? 0);
            $bookingDetails = $result['booking_details'] ?? '';
            
            // Calculate expenses (30% of revenue as estimated operating costs)
            $expenses = $revenue * 0.30;
            $profit = $revenue - $expenses;
            
            $monthlySalesData[] = [
                'month' => $monthNames[$month - 1],
                'orders' => $orders,
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $profit,
                'booking_details' => $bookingDetails // Store details for drill-down
            ];
        }
        
        // Calculate totals and highest income
        $totalRevenue = 0;
        $totalOrders = 0;
        $totalExpenses = 0;
        $totalProfit = 0;
        $highestIncome = 0;
        $highestIncomeMonth = '';
        
        foreach ($monthlySalesData as $data) {
            $totalRevenue += $data['revenue'];
            $totalOrders += $data['orders'];
            $totalExpenses += $data['expenses'];
            $totalProfit += $data['profit'];
            
            if ($data['revenue'] > $highestIncome) {
                $highestIncome = $data['revenue'];
                $highestIncomeMonth = $data['month'];
            }
        }
        
        $averageRevenue = count($monthlySalesData) > 0 ? $totalRevenue / count($monthlySalesData) : 0;
        $averageProfit = count($monthlySalesData) > 0 ? $totalProfit / count($monthlySalesData) : 0;
        
        // Get current month data
        $currentMonth = (int)date('n');
        $currentMonthData = $monthlySalesData[$currentMonth - 1] ?? ['revenue' => 0, 'orders' => 0];
        
        // Get previous month for growth calculation
        $previousMonth = $currentMonth > 1 ? $currentMonth - 2 : 11;
        $previousMonthData = $monthlySalesData[$previousMonth] ?? ['revenue' => 0];
        
        $currentMonthRevenue = $currentMonthData['revenue'];
        $previousMonthRevenue = $previousMonthData['revenue'];
        $growthPercentage = $previousMonthRevenue > 0 
            ? (($currentMonthRevenue - $previousMonthRevenue) / $previousMonthRevenue) * 100 
            : 0;
        
        // Prepare data for chart
        $months = array_map(function($d) { return $d['month']; }, $monthlySalesData);
        $revenues = array_map(function($d) { return $d['revenue']; }, $monthlySalesData);
        $profits = array_map(function($d) { return $d['profit']; }, $monthlySalesData);
        $expenses = array_map(function($d) { return $d['expenses']; }, $monthlySalesData);
        
        // Get available years from database (completed bookings only)
        $yearsSql = "SELECT DISTINCT YEAR(COALESCE(updated_at, created_at)) as year 
                     FROM bookings 
                     WHERE status = 'completed' 
                     AND payment_status IN ('paid', 'paid_full_cash', 'paid_on_delivery_cod')
                     ORDER BY year DESC";
        $yearsStmt = $db->query($yearsSql);
        $availableYears = $yearsStmt->fetchAll(PDO::FETCH_COLUMN);
        
        // Ensure current year is in the list
        if (!in_array($currentYear, $availableYears)) {
            $availableYears[] = $currentYear;
            rsort($availableYears);
        }
        
        $data = [
            'title' => 'Reports - ' . APP_NAME,
            'user' => $this->currentUser(),
            'monthlySalesData' => $monthlySalesData,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'totalExpenses' => $totalExpenses,
            'totalProfit' => $totalProfit,
            'highestIncome' => $highestIncome,
            'highestIncomeMonth' => $highestIncomeMonth,
            'averageRevenue' => $averageRevenue,
            'averageProfit' => $averageProfit,
            'currentMonthRevenue' => $currentMonthRevenue,
            'currentMonthOrders' => $currentMonthData['orders'],
            'growthPercentage' => $growthPercentage,
            'chartMonths' => json_encode($months),
            'chartRevenues' => json_encode($revenues),
            'chartProfits' => json_encode($profits),
            'chartExpenses' => json_encode($expenses),
            'selectedYear' => $selectedYear,
            'availableYears' => $availableYears
        ];
        
        $this->view('admin/reports', $data);
    }
    
    /**
     * Get admin's store location ID
     * Returns the store location ID associated with the current admin user
     */
    private function getAdminStoreLocationId() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
            return null;
        }
        
        $db = Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'];
        $userEmail = $_SESSION['email'];
        
        try {
            // Method 1: Try linking via admin_registrations using user_id
            $stmt = $db->prepare("
                SELECT sl.id as store_location_id
                FROM store_locations sl
                INNER JOIN admin_registrations ar ON (
                    (LOWER(TRIM(sl.store_name)) = LOWER(TRIM(ar.business_name)) 
                     AND LOWER(TRIM(sl.address)) = LOWER(TRIM(ar.business_address)))
                    OR (sl.store_name LIKE CONCAT('%', ar.business_name, '%')
                        AND ar.business_name IS NOT NULL 
                        AND ar.business_name != '')
                )
                INNER JOIN users u ON ar.user_id = u.id AND u.role = 'admin'
                WHERE u.id = ? AND u.email = ? AND sl.status = 'active'
                LIMIT 1
            ");
            $stmt->execute([$userId, $userEmail]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($result && $result['store_location_id']) {
                return intval($result['store_location_id']);
            }
            
            // Method 2: Try linking via admin_registrations using email
            $stmt2 = $db->prepare("
                SELECT sl.id as store_location_id
                FROM store_locations sl
                INNER JOIN admin_registrations ar ON (
                    (LOWER(TRIM(sl.store_name)) = LOWER(TRIM(ar.business_name)) 
                     AND LOWER(TRIM(sl.address)) = LOWER(TRIM(ar.business_address)))
                    OR (sl.store_name LIKE CONCAT('%', ar.business_name, '%')
                        AND ar.business_name IS NOT NULL 
                        AND ar.business_name != '')
                )
                INNER JOIN users u ON ar.email = u.email AND u.role = 'admin'
                WHERE u.id = ? AND u.email = ? AND sl.status = 'active'
                LIMIT 1
            ");
            $stmt2->execute([$userId, $userEmail]);
            $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
            
            if ($result2 && $result2['store_location_id']) {
                return intval($result2['store_location_id']);
            }
            
            // Method 3: Try direct email match in store_locations
            $stmt3 = $db->prepare("
                SELECT id as store_location_id
                FROM store_locations
                WHERE email = ? AND status = 'active'
                LIMIT 1
            ");
            $stmt3->execute([$userEmail]);
            $result3 = $stmt3->fetch(PDO::FETCH_ASSOC);
            
            if ($result3 && $result3['store_location_id']) {
                return intval($result3['store_location_id']);
            }
            
            // If no store found, log for debugging
            error_log("WARNING: No store location found for admin user_id: $userId, email: $userEmail");
            return null;
        } catch (Exception $e) {
            error_log("Error getting admin store location: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return null;
        }
    }
    
    /**
     * Create a default store location for admin if none exists
     */
    private function createDefaultStoreLocationForAdmin() {
        if (!isset($_SESSION['user_id']) || !isset($_SESSION['email'])) {
            return null;
        }
        
        $db = Database::getInstance()->getConnection();
        $userId = $_SESSION['user_id'];
        $userEmail = $_SESSION['email'];
        
        try {
            // Get admin registration info
            $stmt = $db->prepare("
                SELECT ar.business_name, ar.business_address, ar.business_city, ar.business_province,
                       ar.business_latitude, ar.business_longitude, ar.phone, ar.email
                FROM admin_registrations ar
                INNER JOIN users u ON ar.user_id = u.id
                WHERE u.id = ? AND u.email = ? AND u.role = 'admin'
                LIMIT 1
            ");
            $stmt->execute([$userId, $userEmail]);
            $adminReg = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($adminReg && !empty($adminReg['business_name'])) {
                // Create store location from admin registration
                $insertStmt = $db->prepare("
                    INSERT INTO store_locations 
                    (store_name, address, city, province, latitude, longitude, phone, email, status, created_at, updated_at)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())
                ");
                $insertStmt->execute([
                    $adminReg['business_name'],
                    $adminReg['business_address'] ?? '',
                    $adminReg['business_city'] ?? 'Bohol',
                    $adminReg['business_province'] ?? 'Bohol',
                    $adminReg['business_latitude'] ?? null,
                    $adminReg['business_longitude'] ?? null,
                    $adminReg['phone'] ?? '',
                    $adminReg['email'] ?? $userEmail
                ]);
                
                return intval($db->lastInsertId());
            }
            
            return null;
        } catch (Exception $e) {
            error_log("Error creating default store location: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Inventory Management
     */
    public function inventory() {
        $inventoryModel = $this->model('Inventory');
        
        // Get admin's store location ID to filter inventory
        $adminStoreLocationId = $this->getAdminStoreLocationId();
        $inventory = $inventoryModel->getAll($adminStoreLocationId);
        
        $data = [
            'title' => 'Inventory Management - ' . APP_NAME,
            'user' => $this->currentUser(),
            'inventory' => $inventory,
            'admin_store_location_id' => $adminStoreLocationId
        ];
        
        $this->view('admin/inventory', $data);
    }
    
    /**
     * Get Inventory (AJAX) - Returns inventory data
     */
    public function getInventory() {
        // Prevent any output before JSON
        if (ob_get_level()) {
            ob_clean();
        }
        ob_start();
        
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        try {
            $inventoryModel = $this->model('Inventory');
            $storeLocationId = $_GET['store_location_id'] ?? null;
            $inventory = $inventoryModel->getAll($storeLocationId);
            
            // Format for frontend
            $formatted = array_map(function($item) {
                $status = 'in-stock';
                $quantity = floatval($item['quantity'] ?? 0);
                if ($quantity === 0) {
                    $status = 'out-of-stock';
                } elseif ($quantity < 5) {
                    $status = 'low-stock';
                }
                
                // Get type from database - Check BOTH fabric_type and leather_type columns
                // The database might have either column name
                $dbType = $item['leather_type'] ?? $item['fabric_type'] ?? null;
                
                // Only default to 'standard' if it's actually null/empty/not set
                // If database has 'premium', preserve it as 'premium'
                if ($dbType === null || $dbType === '' || (!isset($item['leather_type']) && !isset($item['fabric_type']))) {
                    $normalizedType = 'standard';
                } else {
                    // Preserve the actual database value, just normalize case
                    $normalizedType = strtolower(trim($dbType));
                    // Ensure it's valid
                    if ($normalizedType !== 'standard' && $normalizedType !== 'premium') {
                        $normalizedType = 'standard'; // Fallback if invalid
                    }
                }
                
                // Debug logging - this will help identify the issue
                error_log("DEBUG getInventory - Item ID: " . ($item['id'] ?? 'N/A') . 
                         " | Code: " . ($item['color_code'] ?? 'N/A') . 
                         " | DB leather_type: " . var_export($item['leather_type'] ?? 'NOT SET', true) .
                         " | DB fabric_type: " . var_export($item['fabric_type'] ?? 'NOT SET', true) .
                         " | Selected DB type: " . var_export($dbType, true) . 
                         " | Normalized: " . $normalizedType);
                
                return [
                    'id' => $item['id'] ?? 0,
                    'code' => $item['color_code'] ?? '',
                    'name' => $item['color_name'] ?? '',
                    'color' => $item['color_hex'] ?? '#000000',
                    'color_hex' => $item['color_hex'] ?? '#000000',
                    // Return the normalized type in all fields for frontend compatibility
                    'fabric_type_raw' => $dbType, // Keep original for debugging
                    'type' => $normalizedType,
                    'fabric_type' => $normalizedType,
                    'leather_type' => $normalizedType,
                    'store_location_id' => $item['store_location_id'] ?? null,
                    'quantity' => $quantity,
                    'standard_price' => floatval($item['price_per_unit'] ?? $item['standard_price'] ?? 0),
                    'premium_price' => floatval($item['premium_price'] ?? 0),
                    'price_per_meter' => floatval($item['price_per_meter'] ?? 0),
                    'status' => $status,
                    'lastUpdated' => isset($item['updated_at']) && $item['updated_at'] ? date('M d, Y, h:i A', strtotime($item['updated_at'])) : (isset($item['created_at']) && $item['created_at'] ? date('M d, Y, h:i A', strtotime($item['created_at'])) : date('M d, Y, h:i A'))
                ];
            }, $inventory);
            
            ob_clean(); // Clear any unexpected output
            echo json_encode([
                'success' => true,
                'data' => $formatted,
                'count' => count($formatted)
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        } catch (Exception $e) {
            error_log("Error getting inventory: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            ob_clean(); // Clear any unexpected output
            echo json_encode([
                'success' => false, 
                'message' => 'Error loading inventory: ' . $e->getMessage(),
                'error' => (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : null,
                'data' => []
            ], JSON_UNESCAPED_UNICODE);
        } finally {
            if (ob_get_level()) {
                ob_end_flush();
            }
        }
        exit;
    }
    
    /**
     * Create/Add Inventory Item (AJAX)
     */
    public function createInventory() {
        // Prevent any output before JSON
        if (ob_get_level()) {
            ob_clean();
        }
        ob_start();
        
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Check request method - also check for POST data in case method is overridden
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $hasPostData = !empty($_POST);
        
        // If method is GET but we have POST data, try to parse from input stream
        if ($requestMethod === 'GET' && !$hasPostData) {
            $input = file_get_contents('php://input');
            if (!empty($input)) {
                // Try to parse as form data (multipart/form-data or application/x-www-form-urlencoded)
                parse_str($input, $parsed);
                if (!empty($parsed)) {
                    $_POST = array_merge($_POST, $parsed);
                    $hasPostData = true;
                }
            }
        }
        
        if ($requestMethod !== 'POST' && !$hasPostData) {
            http_response_code(405);
            ob_clean();
            echo json_encode([
                'success' => false, 
                'message' => 'Method not allowed. Expected POST, got ' . $requestMethod,
                'received_method' => $requestMethod,
                'has_post_data' => $hasPostData,
                'post_keys' => array_keys($_POST ?? []),
                'input_length' => strlen(file_get_contents('php://input') ?: '')
            ], JSON_UNESCAPED_UNICODE);
            ob_end_flush();
            exit;
        }
        
        try {
            $inventoryModel = $this->model('Inventory');
            
            // Debug: Log all incoming data
            error_log("DEBUG createInventory - REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
            error_log("DEBUG createInventory - Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'not set'));
            error_log("DEBUG createInventory - All POST data: " . print_r($_POST, true));
            error_log("DEBUG createInventory - All _REQUEST data: " . print_r($_REQUEST, true));
            
            // Get POST data
            $colorCode = $_POST['color_code'] ?? '';
            $colorName = $_POST['color_name'] ?? '';
            $colorHex = $_POST['color_hex'] ?? '#000000';
            
            // Get and normalize leather type to lowercase
            // Debug: Log what we're receiving
            $leatherTypeRaw = $_POST['leather_type'] ?? $_POST['fabric_type'] ?? '';
            error_log("DEBUG createInventory - Raw leather_type from POST: " . var_export($leatherTypeRaw, true));
            error_log("DEBUG createInventory - leather_type from POST directly: " . var_export($_POST['leather_type'] ?? 'NOT SET', true));
            error_log("DEBUG createInventory - fabric_type from POST directly: " . var_export($_POST['fabric_type'] ?? 'NOT SET', true));
            error_log("DEBUG createInventory - All POST keys: " . implode(', ', array_keys($_POST)));
            
            // If empty, default to standard, otherwise normalize
            if (empty($leatherTypeRaw)) {
                $leatherType = 'standard';
                error_log("DEBUG createInventory - leather_type was empty, defaulting to 'standard'");
            } else {
                $leatherType = strtolower(trim($leatherTypeRaw));
                error_log("DEBUG createInventory - Normalized leather_type: " . $leatherType);
            }
            
            // Ensure it's either 'standard' or 'premium'
            if ($leatherType !== 'standard' && $leatherType !== 'premium') {
                error_log("DEBUG createInventory - Invalid leather_type '{$leatherType}', defaulting to 'standard'");
                $leatherType = 'standard'; // Default to standard if invalid
            }
            
            error_log("DEBUG createInventory - Final leather_type to save: " . $leatherType);
            
            $pricePerMeter = floatval($_POST['price_per_meter'] ?? 0);
            $quantity = floatval($_POST['quantity'] ?? 0);
            
            // Automatically get admin's store location ID
            $storeLocationId = $this->getAdminStoreLocationId();
            
            // If no store location found, try to create one or use a default
            if (!$storeLocationId) {
                // Try to create a default store location for this admin
                $storeLocationId = $this->createDefaultStoreLocationForAdmin();
            }
            
            // If still no store location, allow null (optional for now)
            // This allows admins to add inventory even if store location setup is incomplete
            // Store location can be assigned later
            
            $data = [
                'color_code' => $colorCode,
                'color_name' => $colorName,
                'color_hex' => $colorHex,
                'fabric_type' => $leatherType, // Save as lowercase: 'standard' or 'premium'
                'leather_type' => $leatherType, // Save as lowercase: 'standard' or 'premium'
                'price_per_unit' => 0, // Set to 0 as standard/premium prices are removed
                'premium_price' => 0, // Set to 0 as standard/premium prices are removed
                'price_per_meter' => $pricePerMeter,
                'quantity' => $quantity,
                'store_location_id' => $storeLocationId
            ];
            
            // Validate required fields
            if (empty($data['color_code']) || empty($data['color_name'])) {
                http_response_code(400);
                ob_clean();
                echo json_encode([
                    'success' => false, 
                    'message' => 'Color code and name are required',
                    'received_data' => [
                        'color_code' => !empty($data['color_code']),
                        'color_name' => !empty($data['color_name'])
                    ]
                ], JSON_UNESCAPED_UNICODE);
                ob_end_flush();
                exit;
            }
            
            if (empty($leatherType)) {
                http_response_code(400);
                ob_clean();
                echo json_encode([
                    'success' => false, 
                    'message' => 'Leather type is required'
                ], JSON_UNESCAPED_UNICODE);
                ob_end_flush();
                exit;
            }
            
            // Validate price per meter
            if ($pricePerMeter <= 0) {
                http_response_code(400);
                ob_clean();
                echo json_encode([
                    'success' => false, 
                    'message' => 'Price per meter must be greater than 0'
                ], JSON_UNESCAPED_UNICODE);
                ob_end_flush();
                exit;
            }
            
            // Check if color code already exists
            $existing = $inventoryModel->getAll();
            foreach ($existing as $item) {
                if (strtolower($item['color_code']) === strtolower($data['color_code'])) {
                    http_response_code(400);
                    ob_clean();
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Color code already exists: ' . $data['color_code']
                    ], JSON_UNESCAPED_UNICODE);
                    ob_end_flush();
                    exit;
                }
            }
            
            $id = $inventoryModel->create($data);
            
            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Inventory item added successfully',
                'id' => $id,
                'data' => $data
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error creating inventory: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            ob_clean();
            echo json_encode([
                'success' => false, 
                'message' => 'Error creating inventory item: ' . $e->getMessage(),
                'error' => (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
        } finally {
            if (ob_get_level()) {
                ob_end_flush();
            }
        }
        exit;
    }
    
    /**
     * Update Inventory Item (AJAX)
     */
    public function updateInventory() {
        header('Content-Type: application/json');
        ob_start();
        
        // Check request method - allow POST or GET with POST data
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $hasPostData = !empty($_POST) || !empty(file_get_contents('php://input'));
        
        // Debug logging
        error_log("DEBUG updateInventory - REQUEST_METHOD: " . $requestMethod);
        error_log("DEBUG updateInventory - Has POST data: " . ($hasPostData ? 'yes' : 'no'));
        error_log("DEBUG updateInventory - POST keys: " . implode(', ', array_keys($_POST ?? [])));
        
        // Parse JSON input if present
        if ($requestMethod === 'POST' && empty($_POST) && !empty(file_get_contents('php://input'))) {
            $input = file_get_contents('php://input');
            $jsonData = json_decode($input, true);
            if ($jsonData) {
                $_POST = array_merge($_POST, $jsonData);
            }
        }
        
        if ($requestMethod !== 'POST' && !$hasPostData) {
            http_response_code(405);
            ob_clean();
            echo json_encode([
                'success' => false, 
                'message' => 'Method not allowed. Expected POST, got ' . $requestMethod,
                'received_method' => $requestMethod,
                'has_post_data' => $hasPostData
            ], JSON_UNESCAPED_UNICODE);
            ob_end_flush();
            exit;
        }
        
        try {
            $inventoryModel = $this->model('Inventory');
            $id = intval($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid inventory ID']);
                exit;
            }
            
            // Automatically get admin's store location ID
            $storeLocationId = $this->getAdminStoreLocationId();
            
            // If no store location found, try to create one or use a default
            if (!$storeLocationId) {
                // Try to create a default store location for this admin
                $storeLocationId = $this->createDefaultStoreLocationForAdmin();
            }
            
            // If still no store location, allow null (optional for now)
            // This allows admins to add inventory even if store location setup is incomplete
            // Store location can be assigned later
            
            $pricePerMeter = floatval($_POST['price_per_meter'] ?? 0);
            
            // Validate price per meter
            if ($pricePerMeter <= 0) {
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Price per meter must be greater than 0'
                ]);
                exit;
            }
            
            // Get and normalize leather type to lowercase
            $leatherTypeRaw = $_POST['leather_type'] ?? $_POST['fabric_type'] ?? 'standard';
            $leatherType = strtolower(trim($leatherTypeRaw));
            
            // Ensure it's either 'standard' or 'premium'
            if ($leatherType !== 'standard' && $leatherType !== 'premium') {
                $leatherType = 'standard'; // Default to standard if invalid
            }
            
            $data = [
                'color_code' => $_POST['color_code'] ?? '',
                'color_name' => $_POST['color_name'] ?? '',
                'color_hex' => $_POST['color_hex'] ?? '#000000',
                'fabric_type' => $leatherType, // Save as lowercase: 'standard' or 'premium'
                'price_per_unit' => 0, // Set to 0 as standard/premium prices are removed
                'premium_price' => 0, // Set to 0 as standard/premium prices are removed
                'price_per_meter' => $pricePerMeter,
                'quantity' => floatval($_POST['quantity'] ?? 0),
                'store_location_id' => $storeLocationId // Can be null
            ];
            
            // Check if color code already exists (excluding current item)
            $existing = $inventoryModel->getAll();
            foreach ($existing as $item) {
                if ($item['id'] != $id && strtolower($item['color_code']) === strtolower($data['color_code'])) {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Color code already exists']);
                    exit;
                }
            }
            
            $inventoryModel->update($id, $data);
            
            ob_clean();
            echo json_encode([
                'success' => true,
                'message' => 'Inventory item updated successfully',
                'id' => $id,
                'data' => $data
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error updating inventory: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            ob_clean();
            echo json_encode([
                'success' => false, 
                'message' => 'Error updating inventory item: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        } finally {
            if (ob_get_level()) {
                ob_end_flush();
            }
        }
        exit;
    }
    
    /**
     * Delete Inventory Item (AJAX)
     */
    public function deleteInventory() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        try {
            $inventoryModel = $this->model('Inventory');
            $id = intval($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid inventory ID']);
                exit;
            }
            
            $inventoryModel->delete($id);
            
            echo json_encode([
                'success' => true,
                'message' => 'Inventory item deleted successfully'
            ]);
        } catch (Exception $e) {
            error_log("Error deleting inventory: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error deleting inventory item']);
        }
        exit;
    }
    
    /**
     * Get All Inventory (AJAX)
     */
    public function getAllInventory() {
        header('Content-Type: application/json');
        
        try {
            $inventoryModel = $this->model('Inventory');
            $inventory = $inventoryModel->getAll();
            
            // Format for frontend
            $formatted = array_map(function($item) {
                $status = 'in-stock';
                $quantity = floatval($item['quantity'] ?? 0);
                if ($quantity === 0) {
                    $status = 'out-of-stock';
                } elseif ($quantity < 5) {
                    $status = 'low-stock';
                }
                
                return [
                    'id' => $item['id'],
                    'code' => $item['color_code'],
                    'name' => $item['color_name'],
                    'color' => $item['color_hex'],
                    'color_hex' => $item['color_hex'],
                    'type' => $item['fabric_type'] ?? 'standard',
                    'quantity' => $quantity,
                    'standard_price' => floatval($item['price_per_unit'] ?? 0),
                    'premium_price' => floatval($item['premium_price'] ?? 0),
                    'price_per_meter' => floatval($item['price_per_meter'] ?? 0),
                    'status' => $status,
                    'lastUpdated' => isset($item['updated_at']) ? date('M d, Y, h:i A', strtotime($item['updated_at'])) : date('M d, Y, h:i A')
                ];
            }, $inventory);
            
            echo json_encode([
                'success' => true,
                'inventory' => $formatted
            ]);
        } catch (Exception $e) {
            error_log("Error getting inventory: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error loading inventory']);
        }
        exit;
    }
    
    /**
     * Manage Booking Numbers
     */
    public function bookingNumbers() {
        $bookingModel = $this->model('Booking');
        
        $availableNumbers = $bookingModel->getAvailableBookingNumbers();
        $usedNumbers = $bookingModel->getUsedBookingNumbers();
        
        $data = [
            'title' => 'Manage Booking Numbers - ' . APP_NAME,
            'user' => $this->currentUser(),
            'availableNumbers' => $availableNumbers,
            'usedNumbers' => $usedNumbers
        ];
        
        $this->view('admin/booking_numbers', $data);
    }
    
    /**
     * Add new booking numbers
     */
    public function addBookingNumbers() {
        // Always set JSON header to prevent HTML errors from breaking JSON
        header('Content-Type: application/json');
        
        // Only allow POST method for AJAX requests
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed. Use POST.']);
            exit;
        }
        
        $db = Database::getInstance()->getConnection();
        
        $prefix = $_POST['prefix'] ?? 'BKG-';
        $date = $_POST['date'] ?? date('Ymd');
        $startNumber = (int)($_POST['start_number'] ?? 1);
        $count = (int)($_POST['count'] ?? 10);
        
        $added = 0;
        
        for ($i = 0; $i < $count; $i++) {
            $number = $startNumber + $i;
            $bookingNumber = $prefix . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
            
            // Check if booking number already exists
            $stmt = $db->prepare("SELECT id FROM booking_numbers WHERE booking_number = ?");
            $stmt->execute([$bookingNumber]);
            
            if (!$stmt->fetch()) {
                $stmt = $db->prepare("INSERT INTO booking_numbers (booking_number) VALUES (?)");
                if ($stmt->execute([$bookingNumber])) {
                    $added++;
                }
            }
        }
        
        // Return JSON response
        echo json_encode([
            'success' => true,
            'message' => "Added {$added} new booking numbers.",
            'added' => $added
        ]);
        exit;
    }
    
    /**
     * View All Bookings
     */
    public function allBookings() {
        // Get database connection
        $db = Database::getInstance()->getConnection();
        
        // Get all bookings with customer and service details
        // Use COALESCE to ensure status is never NULL
        // Explicitly select payment_status to ensure it's included
        // Filter out rejected bookings
        $sql = "SELECT b.*, s.service_name, s.service_type, sc.category_name,
                u.fullname as customer_name, u.email, u.phone,
                COALESCE(b.status, 'pending') as status,
                COALESCE(b.payment_status, 'unpaid') as payment_status
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE LOWER(COALESCE(b.status, 'pending')) NOT IN ('rejected', 'declined')
                ORDER BY b.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $bookings = $stmt->fetchAll();
        
        // Preserve actual statuses and payment_status - only default if truly NULL or empty string
        // Don't override valid statuses like 'approved', 'in_queue', etc.
        foreach ($bookings as &$booking) {
            // Handle status
            $status = trim($booking['status'] ?? '');
            // Only default to 'pending' if status is truly empty/null
            // Preserve all valid statuses including 'approved', 'in_queue', etc.
            if ($status === '' || $status === null || strtolower($status) === 'null') {
                $booking['status'] = 'pending';
            } else {
                // Preserve the actual status from database
                $booking['status'] = $status;
            }
            
            // Handle payment_status - preserve actual values from database
            $paymentStatus = trim($booking['payment_status'] ?? '');
            // Only default to 'unpaid' if payment_status is truly empty/null
            // Preserve all valid payment statuses like 'paid_full_cash', 'paid_on_delivery_cod', etc.
            if ($paymentStatus === '' || $paymentStatus === null || strtolower($paymentStatus) === 'null') {
                $booking['payment_status'] = 'unpaid';
            } else {
                // Preserve the actual payment_status from database
                $booking['payment_status'] = $paymentStatus;
            }
        }
        
        $data = [
            'title' => 'All Bookings - ' . APP_NAME,
            'user' => $this->currentUser(),
            'bookings' => $bookings
        ];
        
        $this->view('admin/all_bookings', $data);
    }
    
    /**
     * Update Booking Status
     */
    public function updateBookingStatus() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => 'Method not allowed']);
                exit;
            }
            $this->redirect('admin/allBookings');
        }
        
        header('Content-Type: application/json');
        
        $bookingId = $_POST['booking_id'] ?? null;
        $newStatus = $_POST['status'] ?? null;
        $paymentStatus = $_POST['payment_status'] ?? null;
        $adminNotes = $_POST['admin_notes'] ?? '';
        // Technician assignment removed - no longer used
        $progressType = $_POST['progress_type'] ?? null;
        $progressNotes = $_POST['progress_notes'] ?? '';
        $notifyCustomer = isset($_POST['notify_customer']) && $_POST['notify_customer'] == '1';
        $notifyCustomerProgress = isset($_POST['notify_customer_progress']) && $_POST['notify_customer_progress'] == '1';
        // Pickup/Delivery management removed - now handled by customer's service option selection
        
        // Debug logging
        error_log("Update Booking Status - Booking ID: " . $bookingId . ", New Status: " . $newStatus);
        error_log("POST data: " . print_r($_POST, true));
        
        if (!$bookingId || !$newStatus) {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => 'Booking ID and status are required',
                'debug' => [
                    'booking_id' => $bookingId,
                    'status' => $newStatus,
                    'post_data' => $_POST
                ]
            ]);
            exit;
        }
        
        // Sanitize status value
        $newStatus = trim(strtolower($newStatus));
            $allowedStatuses = [
                'pending', 'for_pickup', 'picked_up', 'to_inspect', 'for_inspection', 
                'for_repair', 'approved', 'in_queue', 'in_progress', 'under_repair', 'for_quality_check', 
                'ready_for_pickup', 'out_for_delivery', 'completed', 'paid', 'closed', 
                'delivered_and_paid', 'cancelled'
            ];
        
        if (!in_array($newStatus, $allowedStatuses)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid status value: ' . $newStatus]);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $adminId = $this->currentUser()['id'];
            
            // Start transaction to ensure atomic update
            $db->beginTransaction();
            
            // Get current status for comparison
            // Use id as primary key for db_upholcare database - use backticks for safety
            $currentStmt = $db->prepare("SELECT `status`, `payment_status` FROM `bookings` WHERE `id` = ?");
            $currentStmt->execute([$bookingId]);
            $currentBooking = $currentStmt->fetch();
            $currentStatus = $currentBooking['status'] ?? 'pending';
            $currentPaymentStatus = $currentBooking['payment_status'] ?? 'unpaid';
            
            error_log("Current status: " . $currentStatus . ", New status: " . $newStatus);
            
            // Update booking status and payment
            $updateData = ['status' => $newStatus];
            
            // Completion date tracking will be added after migration
            // Uncomment after running: database/run_completion_date_migration.php
            /*
            if ($newStatus === 'completed' && $currentStatus !== 'completed') {
                $updateData['completion_date'] = date('Y-m-d H:i:s');
                error_log("Setting completion_date for booking ID " . $bookingId . " to " . $updateData['completion_date']);
            }
            */
            
            // Always update payment_status if provided in POST data (including 'unpaid')
            // Check both $paymentStatus variable and $_POST directly
            $finalPaymentStatus = null;
            if ($paymentStatus !== null && $paymentStatus !== '') {
                $finalPaymentStatus = $paymentStatus;
            } elseif (isset($_POST['payment_status'])) {
                // Include 'unpaid' as a valid value - don't skip it
                $finalPaymentStatus = $_POST['payment_status'];
            }
            
            // If payment_status was provided (including 'unpaid'), always include it in the update
            if ($finalPaymentStatus !== null && $finalPaymentStatus !== '') {
                $updateData['payment_status'] = $finalPaymentStatus;
                error_log("Payment status will be updated to: " . $finalPaymentStatus);
                
                // Auto-update status based on payment_status changes
                // Workflow:
                // 1. COD: status = "completed", payment_status = "unpaid" (until payment received)
                // 2. When payment_status is updated to "paid" or "paid_on_delivery_cod" and status is "completed", 
                //    automatically change status to "delivered_and_paid"
                // 3. Full Cash: status = "completed", payment_status = "paid_full_cash" (paid before repair)
                
                if (($finalPaymentStatus === 'paid' || $finalPaymentStatus === 'paid_on_delivery_cod') && 
                    ($currentStatus === 'completed' || $newStatus === 'completed')) {
                    // COD: If status is completed and payment is now paid, change to delivered_and_paid
                    $updateData['status'] = 'delivered_and_paid';
                    $newStatus = 'delivered_and_paid'; // Update the variable for consistency
                    error_log("Auto-updating status to 'delivered_and_paid' because payment_status is now paid (COD) and status was completed");
                } elseif ($finalPaymentStatus === 'paid_full_cash') {
                    // Full cash paid before repair - keep status as "completed" (already paid)
                    // Don't change to delivered_and_paid, just ensure it's completed
                    if ($newStatus !== 'completed' && $newStatus !== 'delivered_and_paid') {
                        $updateData['status'] = 'completed';
                        $newStatus = 'completed';
                    } elseif ($newStatus === 'delivered_and_paid' && $finalPaymentStatus === 'paid_full_cash') {
                        // If status is delivered_and_paid but payment is paid_full_cash, keep as completed
                        // (delivered_and_paid is only for COD)
                        $updateData['status'] = 'completed';
                        $newStatus = 'completed';
                    }
                    error_log("Payment is paid_full_cash - keeping status as 'completed' (not delivered_and_paid)");
                } elseif ($finalPaymentStatus === 'unpaid' && ($newStatus === 'delivered_and_paid' || $currentStatus === 'delivered_and_paid')) {
                    // If payment is unpaid but status is delivered_and_paid, revert to completed
                    $updateData['status'] = 'completed';
                    $newStatus = 'completed';
                    error_log("Payment is unpaid - reverting status from 'delivered_and_paid' to 'completed'");
                }
            } else {
                error_log("No payment_status provided in update request - keeping existing value");
            }
            
            // Add admin notes if provided
            if ($adminNotes) {
                $stmt = $db->prepare("SELECT `notes` FROM `bookings` WHERE `id` = ?");
                $stmt->execute([$bookingId]);
                $booking = $stmt->fetch();
                $existingNotes = $booking['notes'] ?? '';
                $updateData['notes'] = $existingNotes ? $existingNotes . "\n\n[Admin: " . date('Y-m-d H:i') . "] " . $adminNotes : "[Admin: " . date('Y-m-d H:i') . "] " . $adminNotes;
            }
            
            // Update pickup/delivery information
            if ($pickupDate) {
                $updateData['pickup_date'] = date('Y-m-d H:i:s', strtotime($pickupDate));
            }
            if ($deliveryDate) {
                $updateData['delivery_date'] = date('Y-m-d H:i:s', strtotime($deliveryDate));
            }
            if ($deliveryAddress) {
                $updateData['delivery_address'] = $deliveryAddress;
            }
            if ($codPayment) {
                $updateData['payment_status'] = 'paid_on_delivery_cod';
            }
            
            // Recalculate fees if delivery type or distance changes
            // Get current booking to check if we need to recalculate
            $currentBookingStmt = $db->prepare("SELECT `total_amount`, `distance_km` FROM `bookings` WHERE `id` = ?");
            $currentBookingStmt->execute([$bookingId]);
            $currentBooking = $currentBookingStmt->fetch();
            
            $baseServicePrice = floatval($currentBooking['total_amount'] ?? 0);
            $currentDistance = floatval($currentBooking['distance_km'] ?? 0);
            
            // Check if distance is being updated
            $newDistance = isset($_POST['distance_km']) ? floatval($_POST['distance_km']) : $currentDistance;
            
            // Determine delivery type from status or POST data
            $newDeliveryType = 'pickup'; // Default
            if ($deliveryType === 'delivery' || $newStatus === 'out_for_delivery') {
                $newDeliveryType = 'delivery';
            } elseif ($deliveryType === 'pickup' || $pickupDate) {
                $newDeliveryType = 'pickup';
            }
            
            // Recalculate fees if delivery type changed or distance was updated
            if ($baseServicePrice > 0 && ($newDistance != $currentDistance || $deliveryType)) {
                $fees = FeeCalculator::calculateFees($baseServicePrice, $newDeliveryType, $newDistance);
                
                // Update fee fields
                $updateData['labor_fee'] = $fees['labor_fee'];
                $updateData['pickup_fee'] = $fees['pickup_fee'];
                $updateData['delivery_fee'] = $fees['delivery_fee'];
                $updateData['gas_fee'] = $fees['gas_fee'];
                $updateData['travel_fee'] = $fees['travel_fee'];
                $updateData['distance_km'] = $fees['distance_km'];
                $updateData['total_additional_fees'] = $fees['total_additional_fees'];
                $updateData['grand_total'] = $fees['grand_total'];
                
                error_log("Fees recalculated: Grand Total = " . $fees['grand_total']);
            }
            
            // Use direct SQL update to ensure status is definitely changed
            // Use booking_id as primary key (not id) - use backticks for safety
            $updateFields = [];
            $updateValues = [];
            foreach ($updateData as $field => $value) {
                $updateFields[] = "`$field` = ?";
                $updateValues[] = $value;
            }
            $updateValues[] = $bookingId;
            
            // Build and execute update query
            // Use id as primary key for db_upholcare database
            $updateQuery = "UPDATE `bookings` SET " . implode(', ', $updateFields) . ", `updated_at` = NOW() WHERE `id` = ?";
            error_log("Update query: " . $updateQuery);
            error_log("Update values: " . print_r($updateValues, true));
            error_log("Booking ID being used: " . $bookingId);
            
            $updateStmt = null;
            $result = false;
            $updateError = null;
            
            try {
                $updateStmt = $db->prepare($updateQuery);
                $result = $updateStmt->execute($updateValues);
                
                error_log("Update result: " . ($result ? 'SUCCESS' : 'FAILED'));
                if (!$result) {
                    $errorInfo = $updateStmt->errorInfo();
                    error_log("Update error: " . print_r($errorInfo, true));
                    $updateError = $errorInfo[2] ?? 'Unknown error';
                }
            } catch (PDOException $e) {
                error_log("PDO Exception in update: " . $e->getMessage());
                error_log("SQL State: " . $e->getCode());
                $updateError = $e->getMessage();
                
                // Check if it's a column not found error (shouldn't happen now, but keep as safety)
                if (strpos($e->getMessage(), "Column not found") !== false || 
                    strpos($e->getMessage(), "Unknown column") !== false ||
                    $e->getCode() == '42S22') {
                    
                    // Try with 'booking_id' instead of 'id' as fallback (for upholcare_customers database)
                    error_log("Column 'id' not found, trying fallback with 'booking_id' column...");
                    $fallbackQuery = str_replace('`id`', '`booking_id`', $updateQuery);
                    try {
                        $fallbackStmt = $db->prepare($fallbackQuery);
                        $result = $fallbackStmt->execute($updateValues);
                        if ($result) {
                            error_log("Fallback query succeeded with 'id' column");
                            $updateStmt = $fallbackStmt; // Use the fallback statement
                        } else {
                            $errorInfo = $fallbackStmt->errorInfo();
                            error_log("Fallback query also failed: " . print_r($errorInfo, true));
                            throw new Exception("Update failed with both booking_id and id: " . ($errorInfo[2] ?? 'Unknown error'));
                        }
                    } catch (Exception $e2) {
                        error_log("Fallback query exception: " . $e2->getMessage());
                        throw new Exception("Update failed: " . $e->getMessage() . " | Fallback also failed: " . $e2->getMessage());
                    }
                } else {
                    throw $e; // Re-throw if it's a different error
                }
            }
            
            if (!$result && $updateError) {
                throw new Exception("Update failed: " . $updateError);
            }
            
            if ($result) {
                // Verify the update actually happened - check both status and payment_status
                $verifyStmt = $db->prepare("SELECT `status`, `payment_status` FROM `bookings` WHERE `id` = ?");
                $verifyStmt->execute([$bookingId]);
                $verified = $verifyStmt->fetch();
                
                error_log("Verified status after update: " . ($verified['status'] ?? 'NULL'));
                error_log("Verified payment_status after update: " . ($verified['payment_status'] ?? 'NULL'));
                
                // If status didn't update, force update with explicit WHERE clause
                if (!$verified || $verified['status'] !== $newStatus) {
                    error_log("Status mismatch detected! Expected: " . $newStatus . ", Got: " . ($verified['status'] ?? 'NULL') . ". Forcing update...");
                    
                    // Try multiple update methods
                    $forceUpdateStmt = $db->prepare("UPDATE `bookings` SET `status` = ?, `updated_at` = NOW() WHERE `id` = ?");
                    $forceResult = $forceUpdateStmt->execute([$newStatus, $bookingId]);
                    
                    if ($forceResult) {
                        // Verify again
                        $verifyStmt2 = $db->prepare("SELECT `status`, `payment_status` FROM `bookings` WHERE `id` = ?");
                        $verifyStmt2->execute([$bookingId]);
                        $verified2 = $verifyStmt2->fetch();
                        error_log("After force update, status is: " . ($verified2['status'] ?? 'NULL'));
                        error_log("After force update, payment_status is: " . ($verified2['payment_status'] ?? 'NULL'));
                    }
                }
                
                // If payment_status was supposed to be updated but wasn't, force update it
                if (isset($updateData['payment_status'])) {
                    $expectedPaymentStatus = $updateData['payment_status'];
                    $actualPaymentStatus = $verified['payment_status'] ?? null;
                    
                    // Handle empty strings as null
                    if ($actualPaymentStatus === '') {
                        $actualPaymentStatus = null;
                    }
                    
                    if ($actualPaymentStatus !== $expectedPaymentStatus) {
                        error_log("Payment status mismatch! Expected: " . $expectedPaymentStatus . ", Got: " . ($actualPaymentStatus ?? 'NULL/EMPTY') . ". Forcing update...");
                        $forcePaymentStmt = $db->prepare("UPDATE `bookings` SET `payment_status` = ?, `updated_at` = NOW() WHERE `id` = ?");
                        $forcePaymentResult = $forcePaymentStmt->execute([$expectedPaymentStatus, $bookingId]);
                        if ($forcePaymentResult) {
                            error_log("Payment status force update successful");
                            // Verify the force update worked
                            $verifyPaymentStmt = $db->prepare("SELECT `payment_status` FROM `bookings` WHERE `id` = ?");
                            $verifyPaymentStmt->execute([$bookingId]);
                            $verifyPaymentResult = $verifyPaymentStmt->fetch();
                            $forceUpdatedStatus = $verifyPaymentResult['payment_status'] ?? null;
                            error_log("After force update, payment_status is: " . ($forceUpdatedStatus ?? 'NULL'));
                            // Update verified array with the forced value
                            $verified['payment_status'] = $forceUpdatedStatus ?? $expectedPaymentStatus;
                        } else {
                            $errorInfo = $forcePaymentStmt->errorInfo();
                            error_log("Payment status force update FAILED: " . print_r($errorInfo, true));
                        }
                    } else {
                        error_log("Payment status matches expected value: " . $expectedPaymentStatus);
                    }
                }
                
                // Add progress update if provided
                if ($progressType && $progressNotes) {
                    $this->addProgressUpdate($bookingId, $adminId, $progressType, $progressNotes);
                }
                
                // Send notifications if requested
                if ($notifyCustomer) {
                    $this->sendStatusUpdateNotification($bookingId, $newStatus, $paymentStatus);
                }
                
                if ($notifyCustomerProgress && $progressType && $progressNotes) {
                    $this->sendProgressUpdateNotification($bookingId, $progressType, $progressNotes);
                }
                
                if ($notifyCustomerDelivery && ($pickupDate || $deliveryDate)) {
                    $this->sendPickupDeliveryNotification($bookingId, $deliveryType, $pickupDate, $deliveryDate, $deliveryAddress);
                }
                
                // Automatically send receipt notification when transaction is completed AND payment is paid
                // Check both status and payment_status changes
                $shouldSendReceipt = false;
                
                // Get final status and payment status after update
                $finalStatus = $newStatus;
                $finalPaymentStatus = isset($updateData['payment_status']) ? strtolower(trim($updateData['payment_status'])) : strtolower(trim($currentPaymentStatus ?? 'unpaid'));
                
                // Check if transaction is completed (status is completed or delivered_and_paid)
                $isCompleted = in_array($finalStatus, ['completed', 'delivered_and_paid']);
                
                // Check if payment is paid
                $isPaid = in_array($finalPaymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod']);
                
                if ($isCompleted && $isPaid) {
                    // Check if this is a new completion (status changed to completed) OR payment just became paid
                    $statusChangedToCompleted = in_array($finalStatus, ['completed', 'delivered_and_paid']) && 
                                                !in_array($currentStatus, ['completed', 'delivered_and_paid']);
                    
                    $paymentChangedToPaid = isset($updateData['payment_status']) && 
                                           in_array($finalPaymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod']) &&
                                           !in_array(strtolower(trim($currentPaymentStatus ?? 'unpaid')), ['paid', 'paid_full_cash', 'paid_on_delivery_cod']);
                    
                    // Also check if status was already completed and payment just became paid
                    $wasAlreadyCompleted = in_array($currentStatus, ['completed', 'delivered_and_paid']);
                    $paymentJustBecamePaid = isset($updateData['payment_status']) && $paymentChangedToPaid;
                    
                    // Send receipt if:
                    // 1. Status just changed to completed AND payment is paid, OR
                    // 2. Status was already completed AND payment just became paid
                    if (($statusChangedToCompleted && $isPaid) || ($wasAlreadyCompleted && $paymentJustBecamePaid)) {
                        $shouldSendReceipt = true;
                    }
                }
                
                if ($shouldSendReceipt) {
                    // Automatically send receipt notification to customer
                    $this->sendReceiptNotification($bookingId);
                }
                
                // Commit the transaction to save all changes
                $db->commit();
                
                // Final verification - get the actual saved status and payment status
                // Use id as primary key for db_upholcare - use backticks for safety
                $finalVerifyStmt = $db->prepare("SELECT `status`, `payment_status` FROM `bookings` WHERE `id` = ?");
                $finalVerifyStmt->execute([$bookingId]);
                $finalStatus = $finalVerifyStmt->fetch();
                $actualStatus = $finalStatus['status'] ?? $newStatus;
                
                // Get payment_status - handle empty strings and null values
                $dbPaymentStatus = $finalStatus['payment_status'] ?? null;
                if ($dbPaymentStatus === '' || $dbPaymentStatus === null) {
                    // If database has empty/null, use what we tried to save, or current value
                    $actualPaymentStatus = $finalPaymentStatus ?? $currentPaymentStatus ?? 'unpaid';
                } else {
                    // Use the actual value from database
                    $actualPaymentStatus = $dbPaymentStatus;
                }
                
                // If we tried to update payment_status but it's still not correct, force update one more time
                if (isset($updateData['payment_status']) && $actualPaymentStatus !== $updateData['payment_status']) {
                    error_log("Final check: payment_status mismatch. Expected: " . $updateData['payment_status'] . ", Got: " . $actualPaymentStatus . ". Forcing final update...");
                    $finalForceStmt = $db->prepare("UPDATE `bookings` SET `payment_status` = ?, `updated_at` = NOW() WHERE `id` = ?");
                    $finalForceResult = $finalForceStmt->execute([$updateData['payment_status'], $bookingId]);
                    if ($finalForceResult) {
                        $actualPaymentStatus = $updateData['payment_status'];
                        error_log("Final force update successful. payment_status is now: " . $actualPaymentStatus);
                    }
                }
                
                error_log("Final status returned to client: " . $actualStatus);
                error_log("Final payment status returned to client: " . $actualPaymentStatus);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Booking updated successfully. All changes have been saved.',
                    'status' => $actualStatus,
                    'payment_status' => $actualPaymentStatus,
                    'booking_id' => $bookingId,
                    'previous_status' => $currentStatus,
                    'new_status' => $actualStatus
                ]);
            } else {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update booking status']);
            }
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            error_log("Error updating booking status: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Assign technician to booking
     */
    private function assignTechnicianToBooking($bookingId, $technicianId, $adminId, $notes = '') {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Create booking_technicians table if it doesn't exist
            $db->exec("
                CREATE TABLE IF NOT EXISTS booking_technicians (
                    id INT(11) AUTO_INCREMENT PRIMARY KEY,
                    booking_id INT(11) NOT NULL,
                    technician_id INT(11) NOT NULL,
                    assigned_by_admin_id INT(11) NOT NULL,
                    notes TEXT,
                    status ENUM('active', 'completed', 'cancelled') DEFAULT 'active',
                    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    completed_at TIMESTAMP NULL,
                    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
                    FOREIGN KEY (technician_id) REFERENCES users(id) ON DELETE CASCADE,
                    KEY idx_booking_id (booking_id),
                    KEY idx_technician_id (technician_id)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            // Deactivate previous assignments
            $stmt = $db->prepare("UPDATE booking_technicians SET status = 'cancelled' WHERE booking_id = ? AND status = 'active'");
            $stmt->execute([$bookingId]);
            
            // Assign new technician
            $stmt = $db->prepare("
                INSERT INTO booking_technicians (booking_id, technician_id, assigned_by_admin_id, notes, status)
                VALUES (?, ?, ?, ?, 'active')
            ");
            $stmt->execute([$bookingId, $technicianId, $adminId, $notes]);
        } catch (Exception $e) {
            error_log("Error assigning technician: " . $e->getMessage());
        }
    }
    
    /**
     * Send Preview to Customer
     * Admin can send a preview image/note to customer about their purchase
     */
    public function sendPreviewToCustomer() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        $bookingId = $_POST['booking_id'] ?? null;
        $previewNotes = $_POST['preview_notes'] ?? '';
        $previewImage = null;
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get full booking details with all related information
            $checkColumn = $db->query("SHOW COLUMNS FROM bookings LIKE 'selected_color_id'");
            $hasSelectedColorId = $checkColumn->rowCount() > 0;
            
            if ($hasSelectedColorId) {
                $sql = "SELECT b.*, 
                        s.service_name, 
                        s.service_type, 
                        sc.category_name,
                        u.fullname as customer_name, 
                        u.email, 
                        u.phone,
                        i.color_name,
                        i.color_code,
                        i.color_hex,
                        COALESCE(b.status, 'pending') as status
                        FROM bookings b
                        LEFT JOIN services s ON b.service_id = s.id
                        LEFT JOIN service_categories sc ON s.category_id = sc.id
                        LEFT JOIN users u ON b.user_id = u.id
                        LEFT JOIN inventory i ON b.selected_color_id = i.id
                        WHERE b.id = ?";
            } else {
                $sql = "SELECT b.*, 
                        s.service_name, 
                        s.service_type, 
                        sc.category_name,
                        u.fullname as customer_name, 
                        u.email, 
                        u.phone,
                        NULL as color_name,
                        NULL as color_code,
                        NULL as color_hex,
                        COALESCE(b.status, 'pending') as status
                        FROM bookings b
                        LEFT JOIN services s ON b.service_id = s.id
                        LEFT JOIN service_categories sc ON s.category_id = sc.id
                        LEFT JOIN users u ON b.user_id = u.id
                        WHERE b.id = ?";
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                exit;
            }
            
            // Handle file upload if provided
            if (isset($_FILES['preview_image']) && $_FILES['preview_image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = ROOT . DS . 'public' . DS . 'uploads' . DS . 'previews' . DS;
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileExtension = pathinfo($_FILES['preview_image']['name'], PATHINFO_EXTENSION);
                $fileName = 'preview_' . $bookingId . '_' . time() . '.' . $fileExtension;
                $filePath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['preview_image']['tmp_name'], $filePath)) {
                    $previewImage = 'uploads/previews/' . $fileName;
                }
            }
            
            // Update booking with preview info
            $stmt = $db->prepare("UPDATE bookings SET preview_image = ?, preview_notes = ?, preview_sent_at = NOW() WHERE id = ?");
            $stmt->execute([$previewImage, $previewNotes, $bookingId]);
            
            // Send notification to customer
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            // Create in-app notification
            $stmt = $db->prepare("INSERT INTO notifications (user_id, type, title, message, related_id, created_at) 
                                VALUES (?, 'preview', 'Booking Preview Available', 'Admin has sent you a preview of your booking details and pricing.', ?, NOW())");
            $stmt->execute([$booking['user_id'], $bookingId]);
            
            // Send preview email with booking details and receipt-style pricing
            $notificationService->sendPreviewEmail(
                $booking['email'],
                $booking['customer_name'],
                $booking,
                $previewImage,
                $previewNotes
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Preview sent to customer successfully!'
            ]);
        } catch (Exception $e) {
            error_log('Error sending preview: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to send preview: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Send Quotation After Inspection (for PICKUP service option)
     * This is Email #2 - sent AFTER item is picked up and inspected
     * Contains FINAL pricing based on actual measurements and damage assessment
     */
    public function sendQuotationAfterInspection() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        header('Content-Type: application/json');
        
        $bookingId = $_POST['booking_id'] ?? null;
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if booking has selected_color_id for joining inventory table
            $checkStmt = $db->prepare("SELECT selected_color_id FROM bookings WHERE id = ?");
            $checkStmt->execute([$bookingId]);
            $checkResult = $checkStmt->fetch();
            $hasSelectedColorId = !empty($checkResult['selected_color_id']);
            
            // Get booking details with customer info
            if ($hasSelectedColorId) {
                $sql = "SELECT b.*, 
                        s.service_name, 
                        s.service_type, 
                        sc.category_name,
                        u.fullname as customer_name, 
                        u.email, 
                        u.phone,
                        i.color_name,
                        i.color_code,
                        i.color_hex,
                        COALESCE(b.status, 'pending') as status
                        FROM bookings b
                        LEFT JOIN services s ON b.service_id = s.id
                        LEFT JOIN service_categories sc ON s.category_id = sc.id
                        LEFT JOIN users u ON b.user_id = u.id
                        LEFT JOIN inventory i ON b.selected_color_id = i.id
                        WHERE b.id = ?";
            } else {
                $sql = "SELECT b.*, 
                        s.service_name, 
                        s.service_type, 
                        sc.category_name,
                        u.fullname as customer_name, 
                        u.email, 
                        u.phone,
                        NULL as color_name,
                        NULL as color_code,
                        NULL as color_hex,
                        COALESCE(b.status, 'pending') as status
                        FROM bookings b
                        LEFT JOIN services s ON b.service_id = s.id
                        LEFT JOIN service_categories sc ON s.category_id = sc.id
                        LEFT JOIN users u ON b.user_id = u.id
                        WHERE b.id = ?";
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                exit;
            }
            
            // Verify this is a PICKUP service option booking
            $serviceOption = strtolower($booking['service_option'] ?? 'pickup');
            if ($serviceOption !== 'pickup') {
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Quotation after inspection is only for PICKUP service option. This booking is: ' . $serviceOption
                ]);
                exit;
            }
            
            // Update booking - mark that quotation was sent
            $stmt = $db->prepare("UPDATE bookings SET quotation_sent_at = NOW(), updated_at = NOW() WHERE id = ?");
            $stmt->execute([$bookingId]);
            
            // Send notification to customer
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            // Create in-app notification
            $stmt = $db->prepare("INSERT INTO notifications (user_id, type, title, message, related_id, created_at) 
                                VALUES (?, 'quotation', 'Final Quotation Available', 'Your item has been inspected. Final quotation with pricing is now available.', ?, NOW())");
            $stmt->execute([$booking['user_id'], $bookingId]);
            
            // Send quotation email after inspection
            $notificationService->sendQuotationAfterInspection(
                $booking['email'],
                $booking['customer_name'],
                'Booking #' . $bookingId, // Use booking ID instead of booking number
                $booking
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Final quotation sent to customer successfully! Customer will receive email with complete pricing details.'
            ]);
        } catch (Exception $e) {
            error_log('Error sending quotation after inspection: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to send quotation: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Add progress update to booking
     */
    private function addProgressUpdate($bookingId, $adminId, $progressType, $notes) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Create booking_progress_logs table if it doesn't exist
            $db->exec("
                CREATE TABLE IF NOT EXISTS booking_progress_logs (
                    id INT(11) AUTO_INCREMENT PRIMARY KEY,
                    booking_id INT(11) NOT NULL,
                    admin_id INT(11) NOT NULL,
                    progress_type VARCHAR(100) NOT NULL,
                    notes TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
                    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE,
                    KEY idx_booking_id (booking_id),
                    KEY idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
            ");
            
            $stmt = $db->prepare("
                INSERT INTO booking_progress_logs (booking_id, admin_id, progress_type, notes)
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([$bookingId, $adminId, $progressType, $notes]);
        } catch (Exception $e) {
            error_log("Error adding progress update: " . $e->getMessage());
        }
    }
    
    /**
     * Send status update notification
     */
    private function sendStatusUpdateNotification($bookingId, $status, $paymentStatus) {
        // Implementation for sending status update email
        // This would use NotificationService
    }
    
    /**
     * Send progress update notification
     */
    private function sendProgressUpdateNotification($bookingId, $progressType, $notes) {
        // Implementation for sending progress update email
    }
    
    /**
     * Send pickup/delivery notification
     */
    private function sendPickupDeliveryNotification($bookingId, $deliveryType, $pickupDate, $deliveryDate, $address) {
        // Implementation for sending pickup/delivery email
    }
    
    /**
     * Automatically send receipt notification to customer when payment is completed
     */
    private function sendReceiptNotification($bookingId) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get booking details with customer info
            $stmt = $db->prepare("
                SELECT b.*, u.id as customer_id, u.fullname as customer_name, u.email as customer_email
                FROM bookings b
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.id = ?
            ");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking || !$booking['customer_id']) {
                error_log("Cannot send receipt notification: Booking or customer not found for booking ID: " . $bookingId);
                return false;
            }
            
            $customerId = $booking['customer_id'];
            $customerName = $booking['customer_name'] ?? 'Customer';
            $bookingNumber = 'Booking #' . $bookingId; // Use booking ID instead
            $grandTotal = floatval($booking['grand_total'] ?? $booking['total_amount'] ?? 0);
            
            // Check if receipt notification was already sent for this booking to avoid duplicates
            $checkStmt = $db->prepare("
                SELECT id FROM notifications 
                WHERE user_id = ? 
                AND related_id = ? 
                AND related_type = 'booking' 
                AND title = 'Payment Receipt Available'
                LIMIT 1
            ");
            $checkStmt->execute([$customerId, $bookingId]);
            $existingNotification = $checkStmt->fetch();
            
            // Only send notification and email if it hasn't been sent before
            if (!$existingNotification) {
                // Create notification for customer
                $stmt = $db->prepare("
                    INSERT INTO notifications (user_id, type, title, message, related_id, related_type, created_at)
                    VALUES (?, 'success', ?, ?, ?, 'booking', NOW())
                ");
                
                $title = 'Payment Receipt Available';
                $message = "Your payment receipt for booking {$bookingNumber} (Amount: ₱" . number_format($grandTotal, 2) . ") is now available. You can view it in your notifications.";
                
                $stmt->execute([
                    $customerId,
                    $title,
                    $message,
                    $bookingId
                ]);
                
                // Also send email notification if email is configured
                if (!empty($booking['customer_email'])) {
                    try {
                        require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                        $notificationService = new NotificationService();
                        
                        // Send receipt email using NotificationService
                        $notificationService->sendPaymentReceipt(
                            $booking['customer_email'],
                            $customerName,
                            $bookingNumber,
                            $booking,
                            $grandTotal
                        );
                    } catch (Exception $e) {
                        error_log("Error sending receipt email: " . $e->getMessage());
                        // Don't fail the whole process if email fails
                    }
                }
                
                error_log("Receipt notification sent successfully to customer ID: " . $customerId . " for booking ID: " . $bookingId);
            } else {
                error_log("Receipt notification already sent for booking ID: " . $bookingId . ", skipping duplicate notification and email.");
            }
            
            return true;
            
        } catch (Exception $e) {
            error_log("Error sending receipt notification: " . $e->getMessage());
            return false;
        }
    }
    
    
    /**
     * Delete Booking (AJAX)
     */
    public function deleteBooking() {
        // Start output buffering to prevent any accidental output
        ob_start();
        
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Check request method
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        if ($requestMethod !== 'POST') {
            ob_clean();
            http_response_code(405);
            echo json_encode([
                'success' => false, 
                'message' => 'Method not allowed. Expected POST, got ' . $requestMethod,
                'received_method' => $requestMethod
            ]);
            ob_end_flush();
            exit;
        }
        
        // Get booking ID from POST data
        $bookingId = $_POST['booking_id'] ?? null;
        
        // Also check if it's in the request body (for FormData)
        if (!$bookingId && !empty(file_get_contents('php://input'))) {
            parse_str(file_get_contents('php://input'), $input);
            $bookingId = $input['booking_id'] ?? null;
        }
        
        if (!$bookingId || !is_numeric($bookingId)) {
            ob_clean();
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
            ob_end_flush();
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if booking exists
            $stmt = $db->prepare("SELECT id, status, payment_status, user_id FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                ob_clean();
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                ob_end_flush();
                exit;
            }
            
            // Check if booking can be deleted
            // Allow deletion if:
            // 1. Status is pending, cancelled, rejected, declined, OR
            // 2. Status is completed but payment is unpaid (unused completed bookings)
            // 3. Payment status is unpaid or cancelled
            $status = strtolower($booking['status'] ?? '');
            $paymentStatus = strtolower($booking['payment_status'] ?? 'unpaid');
            
            $allowedStatuses = ['pending', 'cancelled', 'rejected', 'declined'];
            $restrictedStatuses = ['delivered_and_paid', 'in_queue', 'under_repair'];
            
            // Allow deletion of completed bookings only if unpaid
            if ($status === 'completed' && !in_array($paymentStatus, ['unpaid', 'cancelled'])) {
                ob_clean();
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Cannot delete completed booking with payment status: ' . ucfirst($paymentStatus) . '. Only unpaid completed bookings can be deleted.'
                ]);
                ob_end_flush();
                exit;
            }
            
            // Restrict deletion of active/processing bookings
            if (in_array($status, $restrictedStatuses)) {
                ob_clean();
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Cannot delete booking with status: ' . ucfirst($status) . '. Only unused bookings (pending, cancelled, rejected, completed unpaid) can be deleted.'
                ]);
                ob_end_flush();
                exit;
            }
            
            // Restrict deletion of paid bookings (except cancelled)
            if (!in_array($paymentStatus, ['unpaid', 'cancelled']) && $status !== 'completed') {
                ob_clean();
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Cannot delete booking with payment status: ' . ucfirst($paymentStatus) . '. Only unpaid bookings can be deleted.'
                ]);
                ob_end_flush();
                exit;
            }
            
            // Start transaction
            $db->beginTransaction();
            
            try {
                // Delete related records first (if any)
                // Check if related_id column exists in notifications table
                $hasRelatedId = false;
                try {
                    $checkStmt = $db->query("
                        SELECT COUNT(*) as cnt 
                        FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'notifications' 
                        AND COLUMN_NAME = 'related_id'
                    ");
                    $hasRelatedId = $checkStmt->fetch()['cnt'] > 0;
                } catch (Exception $e) {
                    // Column doesn't exist, skip notification deletion
                    error_log("related_id column not found in notifications table: " . $e->getMessage());
                }
                
                // Delete notifications related to this booking (only if related_id column exists)
                if ($hasRelatedId) {
                    try {
                        // Check if related_type column also exists
                        $checkStmt = $db->query("
                            SELECT COUNT(*) as cnt 
                            FROM INFORMATION_SCHEMA.COLUMNS 
                            WHERE TABLE_SCHEMA = DATABASE() 
                            AND TABLE_NAME = 'notifications' 
                            AND COLUMN_NAME = 'related_type'
                        ");
                        $hasRelatedType = $checkStmt->fetch()['cnt'] > 0;
                        
                        if ($hasRelatedType) {
                            $stmt = $db->prepare("DELETE FROM notifications WHERE related_id = ? AND related_type = 'booking'");
                            $stmt->execute([$bookingId]);
                        } else {
                            $stmt = $db->prepare("DELETE FROM notifications WHERE related_id = ?");
                            $stmt->execute([$bookingId]);
                        }
                    } catch (Exception $e) {
                        // If deletion fails, log but continue
                        error_log("Error deleting notifications: " . $e->getMessage());
                    }
                } else {
                    // Try to delete by message content if related_id doesn't exist
                    try {
                        $stmt = $db->prepare("DELETE FROM notifications WHERE user_id = ? AND message LIKE ?");
                        $stmt->execute([$booking['user_id'] ?? 0, '%booking%' . $bookingId . '%']);
                    } catch (Exception $e) {
                        error_log("Error deleting notifications by message: " . $e->getMessage());
                    }
                }
                
                // Delete related quotations (if any) - must be deleted before booking due to foreign key
                try {
                    // Check if quotations table exists
                    $checkTable = $db->query("
                        SELECT COUNT(*) as cnt 
                        FROM INFORMATION_SCHEMA.TABLES 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'quotations'
                    ");
                    $tableExists = $checkTable->fetch()['cnt'] > 0;
                    
                    if ($tableExists) {
                        $stmt = $db->prepare("DELETE FROM quotations WHERE booking_id = ?");
                        $stmt->execute([$bookingId]);
                        error_log("Deleted quotations for booking ID: " . $bookingId);
                    }
                } catch (Exception $e) {
                    error_log("Error deleting quotations: " . $e->getMessage());
                    // Continue even if quotations deletion fails
                }
                
                // Delete related payments (if any) - must be deleted before booking due to foreign key
                try {
                    // Check if payments table exists
                    $checkTable = $db->query("
                        SELECT COUNT(*) as cnt 
                        FROM INFORMATION_SCHEMA.TABLES 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'payments'
                    ");
                    $tableExists = $checkTable->fetch()['cnt'] > 0;
                    
                    if ($tableExists) {
                        $stmt = $db->prepare("DELETE FROM payments WHERE booking_id = ?");
                        $stmt->execute([$bookingId]);
                        error_log("Deleted payments for booking ID: " . $bookingId);
                    }
                } catch (Exception $e) {
                    error_log("Error deleting payments: " . $e->getMessage());
                    // Continue even if payments deletion fails
                }
                
                // Delete booking
                $stmt = $db->prepare("DELETE FROM bookings WHERE id = ?");
                $stmt->execute([$bookingId]);
                
                // Commit transaction
                $db->commit();
                
                ob_clean();
                echo json_encode([
                    'success' => true,
                    'message' => 'Booking deleted successfully'
                ]);
            } catch (Exception $e) {
                $db->rollBack();
                throw $e;
            }
        } catch (Exception $e) {
            error_log("Error deleting booking: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            ob_clean();
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'Error deleting booking: ' . $e->getMessage(),
                'error' => (defined('DEBUG_MODE') && DEBUG_MODE) ? $e->getMessage() : null
            ]);
        } finally {
            if (ob_get_level()) {
                ob_end_flush();
            }
        }
        exit;
    }
    
    /**
     * Get Booking Details (AJAX)
     */
    public function getBookingDetails($bookingId) {
        // Start output buffering to catch any unexpected output
        ob_start();
        
        try {
            // Set headers first to prevent any output before JSON
            header('Content-Type: application/json');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            header('Pragma: no-cache');
            header('Expires: 0');
            
            if (empty($bookingId) || !is_numeric($bookingId)) {
                http_response_code(400);
                ob_clean(); // Clear any output
                echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
                ob_end_flush();
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Check if selected_color_id column exists in bookings table
            $checkColumn = $db->query("SHOW COLUMNS FROM bookings LIKE 'selected_color_id'");
            $hasSelectedColorId = $checkColumn->rowCount() > 0;
            
            // Check which type column exists in inventory table
            $checkFabricType = $db->query("SHOW COLUMNS FROM inventory LIKE 'fabric_type'");
            $hasFabricType = $checkFabricType->rowCount() > 0;
            $checkLeatherType = $db->query("SHOW COLUMNS FROM inventory LIKE 'leather_type'");
            $hasLeatherType = $checkLeatherType->rowCount() > 0;
            
            // Build inventory type selection based on which column exists
            $inventoryTypeSelect = '';
            if ($hasFabricType && $hasLeatherType) {
                // Both exist, use COALESCE to get either one
                $inventoryTypeSelect = "COALESCE(i.fabric_type, i.leather_type) as inventory_type";
            } elseif ($hasFabricType) {
                $inventoryTypeSelect = "i.fabric_type as inventory_type";
            } elseif ($hasLeatherType) {
                $inventoryTypeSelect = "i.leather_type as inventory_type";
            } else {
                $inventoryTypeSelect = "NULL as inventory_type";
            }
            
            // Build SQL query based on whether selected_color_id column exists
            if ($hasSelectedColorId) {
                $sql = "SELECT b.*, 
                        s.service_name, 
                        s.service_type, 
                        sc.category_name,
                        u.fullname as customer_name, 
                        u.email, 
                        u.phone,
                        i.color_name,
                        i.color_code,
                        i.color_hex,
                        {$inventoryTypeSelect},
                        COALESCE(b.status, 'pending') as status
                        FROM bookings b
                        LEFT JOIN services s ON b.service_id = s.id
                        LEFT JOIN service_categories sc ON s.category_id = sc.id
                        LEFT JOIN users u ON b.user_id = u.id
                        LEFT JOIN inventory i ON b.selected_color_id = i.id
                        WHERE b.id = ?";
            } else {
                // Column doesn't exist, skip the inventory join
                $sql = "SELECT b.*, 
                        s.service_name, 
                        s.service_type, 
                        sc.category_name,
                        u.fullname as customer_name, 
                        u.email, 
                        u.phone,
                        NULL as color_name,
                        NULL as color_code,
                        NULL as color_hex,
                        COALESCE(b.status, 'pending') as status
                        FROM bookings b
                        LEFT JOIN services s ON b.service_id = s.id
                        LEFT JOIN service_categories sc ON s.category_id = sc.id
                        LEFT JOIN users u ON b.user_id = u.id
                        WHERE b.id = ?";
            }
            
            $stmt = $db->prepare($sql);
            if (!$stmt) {
                $errorInfo = $db->errorInfo();
                throw new Exception('Failed to prepare database query: ' . ($errorInfo[2] ?? 'Unknown error'));
            }
            
            $execResult = $stmt->execute([$bookingId]);
            if (!$execResult) {
                $errorInfo = $stmt->errorInfo();
                throw new Exception('Failed to execute query: ' . ($errorInfo[2] ?? 'Unknown error'));
            }
            
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Preserve actual status - only default to 'pending' if truly NULL or empty
            if ($booking) {
                $status = trim($booking['status'] ?? '');
                if ($status === '' || $status === null || strtolower($status) === 'null') {
                    $booking['status'] = 'pending';
                } else {
                    // Preserve the actual status from database
                    $booking['status'] = $status;
                }
                
                // Ensure all values are JSON-serializable
                foreach ($booking as $key => $value) {
                    if (is_resource($value)) {
                        $booking[$key] = null;
                    } elseif (is_object($value)) {
                        $booking[$key] = (string)$value;
                    } elseif (is_array($value)) {
                        // Handle nested arrays
                        $booking[$key] = json_decode(json_encode($value), true);
                    }
                }
                
                ob_clean(); // Clear any unexpected output
                echo json_encode(['success' => true, 'booking' => $booking], JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR);
            } else {
                http_response_code(404);
                ob_clean(); // Clear any unexpected output
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
            }
        } catch (PDOException $e) {
            error_log("Database error in getBookingDetails (Booking ID: {$bookingId}): " . $e->getMessage());
            error_log("PDO Error Info: " . print_r($e->errorInfo ?? [], true));
            http_response_code(500);
            ob_clean(); // Clear any unexpected output
            echo json_encode([
                'success' => false, 
                'message' => 'Database error occurred. Please try again later.',
                'error' => (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error in getBookingDetails (Booking ID: {$bookingId}): " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            ob_clean(); // Clear any unexpected output
            echo json_encode([
                'success' => false, 
                'message' => 'An error occurred while loading booking details.',
                'error' => (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : null
            ], JSON_UNESCAPED_UNICODE);
        } finally {
            ob_end_flush();
        }
        exit;
    }
    
    /**
     * Accept Reservation (AJAX)
     */
    /**
     * Accept Reservation and Assign Booking Number
     */
    public function acceptReservation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        header('Content-Type: application/json');
        
        $bookingId = $_POST['booking_id'] ?? null;
        $bookingNumberId = $_POST['booking_number_id'] ?? null; // Optional - will auto-assign if not provided
        $adminNotes = $_POST['admin_notes'] ?? '';
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Start transaction to ensure atomic update
            $db->beginTransaction();
            
            // Get booking details
            $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                return;
            }
            
            // Booking numbers removed - system now uses availability based on stock and capacity
            
            // First, set status to "approved" (admin has reviewed and approved)
            // Then, if service option is PICKUP, automatically update to "for_pickup"
            $serviceOption = strtolower(trim($booking['service_option'] ?? 'pickup'));
            $newStatus = 'approved'; // Always start with "approved" after admin approval
            
            // Update booking: Set status based on service option (queue number already assigned)
            // Use direct SQL update to ensure status is definitely changed
            $updateData = [
                'status' => $newStatus,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Add admin notes if provided
            if ($adminNotes) {
                $updateData['notes'] = ($booking['notes'] ?? '') . "\n\n[Admin Notes: " . $adminNotes . "]";
            }
            
            // Update using direct SQL to ensure it works
            // CRITICAL: Use explicit status = 'approved' to prevent any default value issues
            $updateFields = [];
            $updateValues = [];
            foreach ($updateData as $field => $value) {
                $updateFields[] = "$field = ?";
                $updateValues[] = $value;
            }
            $updateValues[] = $bookingId;
            
            // Build update query with explicit status assignment
            $updateQuery = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE id = ?";
            error_log("AcceptReservation - Update query: " . $updateQuery);
            error_log("AcceptReservation - Update values: " . print_r($updateValues, true));
            
            $updateStmt = $db->prepare($updateQuery);
            $updateResult = $updateStmt->execute($updateValues);
            
            error_log("AcceptReservation - Update result: " . ($updateResult ? 'SUCCESS' : 'FAILED'));
            
            // Immediately verify after update (before commit)
            $preCommitCheck = $db->prepare("SELECT status FROM bookings WHERE id = ?");
            $preCommitCheck->execute([$bookingId]);
            $preCommitStatus = $preCommitCheck->fetch();
            error_log("AcceptReservation - Pre-commit status: " . ($preCommitStatus['status'] ?? 'NULL'));
            
            // CRITICAL: Verify the update actually happened and commit transaction
            if ($updateResult) {
                // Commit transaction first to ensure changes are saved
                if ($db->inTransaction()) {
                    $db->commit();
                }
                
                // Verify the update actually happened
                $verifyStmt = $db->prepare("SELECT status FROM bookings WHERE id = ?");
                $verifyStmt->execute([$bookingId]);
                $verified = $verifyStmt->fetch();
                
                if (!$verified || $verified['status'] !== 'approved') {
                    // Status didn't update, try again with explicit update
                    error_log("Status verification failed for booking ID: " . $bookingId . ". Attempting force update...");
                    $forceUpdateStmt = $db->prepare("UPDATE bookings SET status = 'approved', updated_at = NOW() WHERE id = ?");
                    $forceUpdateResult = $forceUpdateStmt->execute([$bookingId]);
                    
                    if ($forceUpdateResult) {
                        // Verify again after force update
                        $verifyStmt2 = $db->prepare("SELECT status FROM bookings WHERE id = ?");
                        $verifyStmt2->execute([$bookingId]);
                        $verified2 = $verifyStmt2->fetch();
                        error_log("After force update, status is: " . ($verified2['status'] ?? 'NULL'));
                    } else {
                        // Log error for debugging
                        error_log("CRITICAL: Failed to update booking status to 'approved' for booking ID: " . $bookingId);
                    }
                }
            } else {
                // Update failed
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update booking status']);
                exit;
            }
            
            // After setting to "approved", check if service option is Pick Up
            // If so, automatically update to "for_pickup" and send email
            if ($updateResult && $newStatus === 'approved') {
                $serviceOption = strtolower(trim($booking['service_option'] ?? 'pickup'));
                
                // If service option is Pick Up or Both, automatically update to "for_pickup"
                if ($serviceOption === 'pickup' || $serviceOption === 'both') {
                    $pickupUpdateStmt = $db->prepare("UPDATE bookings SET status = 'for_pickup', updated_at = NOW() WHERE id = ?");
                    $pickupUpdateResult = $pickupUpdateStmt->execute([$bookingId]);
                    
                    if ($pickupUpdateResult) {
                        $newStatus = 'for_pickup'; // Update for response
                        error_log("Auto-updated booking ID " . $bookingId . " from 'approved' to 'for_pickup' (Pick Up service)");
                    }
                }
            }
            
            // Get customer details for notification (only if update was successful)
            if ($updateResult) {
                // Get customer details for notification
                $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$booking['user_id']]);
                $customer = $stmt->fetch();
                
                // Queue numbers removed - calculate customers ahead based on creation date
                $customersAhead = 0;
                $stmt = $db->prepare("
                    SELECT COUNT(*) as customers_ahead
                    FROM bookings b
                    WHERE b.status IN ('pending', 'approved', 'in_queue', 'under_repair', 'for_quality_check', 'ready_for_pickup', 'out_for_delivery')
                    AND b.id != ?
                    AND b.created_at < (SELECT created_at FROM bookings WHERE id = ?)
                ");
                $stmt->execute([$bookingId, $bookingId]);
                $aheadResult = $stmt->fetch();
                $customersAhead = (int)($aheadResult['customers_ahead'] ?? 0);
                
                // Send notification to customer
                if ($customer) {
                    require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                    $notificationService = new NotificationService();
                    
                    // Send approval email with "ready for repair" message
                    $notificationService->sendReservationApprovalEmail(
                        $customer['email'],
                        $customer['fullname'],
                        'Booking #' . $bookingId, // Use booking ID instead
                        $booking,
                        0, // No queue position
                        $customersAhead
                    );
                    
                    // Create in-app notification for customer
                    // Check if related_id column exists in notifications table
                    $hasRelatedId = false;
                    try {
                        $checkStmt = $db->query("
                            SELECT COUNT(*) as cnt 
                            FROM INFORMATION_SCHEMA.COLUMNS 
                            WHERE TABLE_SCHEMA = DATABASE() 
                            AND TABLE_NAME = 'notifications' 
                            AND COLUMN_NAME = 'related_id'
                        ");
                        $hasRelatedId = $checkStmt->fetch()['cnt'] > 0;
                    } catch (Exception $e) {
                        // Ignore error
                    }
                    
                    // Create notification with better message based on service option
                    $serviceOption = strtolower(trim($booking['service_option'] ?? 'pickup'));
                    $notificationMessage = "Great news! Your reservation has been approved by the admin.";
                    
                    if ($serviceOption === 'pickup' || $serviceOption === 'both') {
                        $notificationMessage .= " Your booking status is now 'For Pick Up'. We will pick up your item on the scheduled date. You will receive an email confirmation shortly.";
                    } else {
                        $notificationMessage .= " Your booking status is now 'Approved'. You can now track your repair progress.";
                    }
                    
                    if ($hasRelatedId) {
                        $notifStmt = $db->prepare("
                            INSERT INTO notifications (user_id, type, title, message, related_id, created_at) 
                            VALUES (?, 'success', ?, ?, ?, NOW())
                        ");
                        $notifStmt->execute([
                            $customer['id'],
                            'Reservation Approved',
                            $notificationMessage,
                            $bookingId
                        ]);
                    } else {
                        $notifStmt = $db->prepare("
                            INSERT INTO notifications (user_id, type, title, message, created_at) 
                            VALUES (?, 'success', ?, ?, NOW())
                        ");
                        $notifStmt->execute([
                            $customer['id'],
                            'Reservation Approved',
                            $notificationMessage
                        ]);
                    }
                }
                
                // Final verification - double check status was updated (after commit)
                // Wait a moment for database to fully commit
                usleep(100000); // 100ms delay to ensure commit is complete
                
                $finalCheckStmt = $db->prepare("SELECT status FROM bookings WHERE id = ?");
                $finalCheckStmt->execute([$bookingId]);
                $finalStatus = $finalCheckStmt->fetch();
                
                $actualStatus = trim($finalStatus['status'] ?? '');
                error_log("Final status check for booking ID " . $bookingId . ": '" . $actualStatus . "'");
                
                // Ensure status is 'approved' - if not, force update one more time
                if (!$finalStatus || strtolower($actualStatus) !== 'approved') {
                    error_log("Final check failed for booking ID: " . $bookingId . ". Status is: '" . $actualStatus . "'. Performing final force update...");
                    
                    // Use explicit UPDATE with WHERE clause to ensure it works
                    $finalForceStmt = $db->prepare("UPDATE bookings SET status = 'approved', updated_at = NOW() WHERE id = ?");
                    $finalForceResult = $finalForceStmt->execute([$bookingId]);
                    
                    if ($finalForceResult) {
                        // Wait again for update to complete
                        usleep(100000); // 100ms delay
                        
                        // Verify one more time
                        $finalCheckStmt2 = $db->prepare("SELECT status FROM bookings WHERE id = ?");
                        $finalCheckStmt2->execute([$bookingId]);
                        $finalStatus = $finalCheckStmt2->fetch();
                        $actualStatus2 = trim($finalStatus['status'] ?? '');
                        error_log("After final force update, status is: '" . $actualStatus2 . "'");
                        
                        // If still not approved, log critical error
                        if (strtolower($actualStatus2) !== 'approved') {
                            error_log("CRITICAL: Status still not 'approved' after force update for booking ID: " . $bookingId);
                        } else {
                            $actualStatus = $actualStatus2;
                        }
                    } else {
                        error_log("CRITICAL: Final force update failed for booking ID: " . $bookingId);
                    }
                    
                    // Ensure we return 'approved' in response even if verification fails
                    if (strtolower($actualStatus) !== 'approved') {
                        $actualStatus = 'approved';
                    }
                }
                
                // Get the final verified status
                $responseStatus = $actualStatus ?? ($finalStatus['status'] ?? 'approved');
                $responseStatus = trim($responseStatus);
                
                // Use the actual status (for_pickup for PICKUP, approved for others)
                $statusMessage = ($newStatus === 'for_pickup') 
                    ? 'Reservation approved! Status set to "For Pickup". Item will be collected for inspection. Customer notified via email.'
                    : 'Reservation approved successfully! Status changed to "Approved". Customer notified via email.';
                
                // Ensure response status matches the intended status
                if (strtolower($responseStatus) !== strtolower($newStatus)) {
                    error_log("WARNING: Response status '" . $responseStatus . "' does not match intended status '" . $newStatus . "' for booking ID " . $bookingId);
                    $responseStatus = $newStatus;
                }
                
                error_log("Sending response for booking ID " . $bookingId . " with status: '" . $responseStatus . "'");
                
                echo json_encode([
                    'success' => true,
                    'message' => $statusMessage,
                    'booking_id' => $bookingId,
                    'customers_ahead' => $customersAhead,
                    'status' => $responseStatus // Return actual status
                ]);
            } else {
                if ($db->inTransaction()) {
                    $db->rollBack();
                }
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to accept reservation']);
            }
        } catch (Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Save Calculated Total Payment (Bayronon)
     * Admin calculates total after examining item and measuring fabric
     */
    public function saveCalculatedTotal() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        header('Content-Type: application/json');
        
        $bookingId = $_POST['booking_id'] ?? null;
        $fabricLength = $_POST['fabric_length'] ?? null;
        $fabricWidth = $_POST['fabric_width'] ?? null;
        $fabricArea = $_POST['fabric_area'] ?? null;
        $fabricCostPerMeter = $_POST['fabric_cost_per_meter'] ?? null;
        $fabricTotal = $_POST['fabric_total'] ?? null;
        $laborFee = $_POST['labor_fee'] ?? null;
        $materialCost = $_POST['material_cost'] ?? 0;
        $serviceFees = $_POST['service_fees'] ?? 0;
        $totalAmount = $_POST['total_amount'] ?? null;
        $calculationNotes = $_POST['calculation_notes'] ?? '';
        
        if (!$bookingId || !$totalAmount) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID and total amount are required']);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if booking exists
            $stmt = $db->prepare("SELECT id FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                return;
            }
            
            // Prepare update data
            $updateData = [
                'fabric_length' => $fabricLength,
                'fabric_width' => $fabricWidth,
                'fabric_area' => $fabricArea,
                'fabric_cost_per_meter' => $fabricCostPerMeter,
                'fabric_total' => $fabricTotal,
                'labor_fee' => $laborFee,
                'material_cost' => $materialCost,
                'service_fees' => $serviceFees,
                'total_amount' => $totalAmount,
                'calculated_total_saved' => '1',
                'calculation_notes' => $calculationNotes,
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Build update query
            $updateFields = [];
            $updateValues = [];
            foreach ($updateData as $field => $value) {
                // Check if column exists before adding to update
                $updateFields[] = "$field = ?";
                $updateValues[] = $value;
            }
            $updateValues[] = $bookingId;
            
            $updateQuery = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $result = $updateStmt->execute($updateValues);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Calculated total saved successfully. You can now approve the booking.',
                    'total_amount' => $totalAmount
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save calculated total']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Old acceptReservation method - replaced above
     */
    public function acceptReservation_OLD() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $bookingId = $_POST['booking_id'] ?? null;
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            return;
        }
        
        // Get booking details with customer information
        $booking = $this->getBookingWithCustomerDetails($bookingId);
        if (!$booking) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
            return;
        }
        
        if ($booking['status'] !== 'pending') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Only pending reservations can be accepted']);
            return;
        }
        
        // Update status to confirmed
        $result = $this->bookingModel->update($bookingId, [
            'status' => 'confirmed',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($result) {
            // Send approval notification email
            $this->sendApprovalNotification($booking);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Reservation accepted successfully. Customer has been notified.',
                'new_status' => 'confirmed'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to accept reservation']);
        }
    }
    
    /**
     * Reject Reservation (AJAX)
     */
    public function rejectReservation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $bookingId = $_POST['booking_id'] ?? null;
        $reason = $_POST['reason'] ?? 'No reason provided';
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            return;
        }
        
        // Get booking details with customer information
        $booking = $this->getBookingWithCustomerDetails($bookingId);
        if (!$booking) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
            return;
        }
        
        if ($booking['status'] !== 'pending') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Only pending reservations can be rejected']);
            return;
        }
        
        // Update status to rejected and add rejection reason
        $result = $this->bookingModel->update($bookingId, [
            'status' => 'rejected',
            'notes' => $booking['notes'] ? $booking['notes'] . "\n\nRejection Reason: " . $reason : "Rejection Reason: " . $reason,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($result) {
            // Send rejection notification email
            $this->sendRejectionNotification($booking, $reason);
            
            echo json_encode([
                'success' => true, 
                'message' => 'Reservation rejected successfully. Customer has been notified.',
                'new_status' => 'rejected'
            ]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to reject reservation']);
        }
    }
    
    /**
     * Get booking with customer details for notifications
     */
    private function getBookingWithCustomerDetails($bookingId) {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT b.*, s.service_name, s.service_type, sc.category_name,
                u.fullname as customer_name, u.email as customer_email, u.phone
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$bookingId]);
        return $stmt->fetch();
    }
    
    /**
     * Send approval notification
     */
    private function sendApprovalNotification($booking) {
        try {
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            $result = $notificationService->sendReservationApproval(
                $booking['customer_email'],
                $booking['customer_name'],
                $booking
            );
            
            if (!$result) {
                error_log("Failed to send approval notification for booking ID: " . $booking['id']);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error sending approval notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Send rejection notification
     */
    private function sendRejectionNotification($booking, $reason) {
        try {
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            $result = $notificationService->sendReservationRejection(
                $booking['customer_email'],
                $booking['customer_name'],
                $booking,
                $reason
            );
            
            if (!$result) {
                error_log("Failed to send rejection notification for booking ID: " . $booking['id']);
            }
            
            return $result;
        } catch (Exception $e) {
            error_log("Error sending rejection notification: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get Pending Reservations (AJAX)
     */
    public function getPendingReservations() {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT b.*, s.service_name, s.service_type, sc.category_name,
                u.fullname as customer_name, u.email, u.phone
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.status = 'pending'
                ORDER BY b.created_at ASC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $reservations = $stmt->fetchAll();
        
        echo json_encode([
            'success' => true,
            'reservations' => $reservations,
            'count' => count($reservations)
        ]);
    }
    
    /**
     * Get Booking Numbers (AJAX)
     */
    public function getBookingNumbers() {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Booking numbers removed - return empty array
            $bookingNumbers = [];
            
            echo json_encode([
                'success' => true,
                'bookingNumbers' => $bookingNumbers
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error loading booking numbers: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Get Customers (AJAX)
     */
    public function getCustomers() {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getInstance()->getConnection();
            
            $sql = "SELECT id, fullname, email, phone FROM users WHERE role = 'customer' ORDER BY fullname ASC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $customers = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'customers' => $customers
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error loading customers: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Assign Booking Number to Customer (AJAX)
     */
    public function assignBookingNumber() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $bookingNumberId = $_POST['booking_number_id'] ?? null;
        $customerId = $_POST['customer_id'] ?? null;
        
        if (!$bookingNumberId || !$customerId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking number ID and customer ID are required']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            // Check if customer already has an active booking number
            $stmt = $db->prepare("SELECT id FROM customer_booking_numbers WHERE customer_id = ? AND status = 'active'");
            $stmt->execute([$customerId]);
            
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Customer already has an active booking number']);
                return;
            }
            
            // Get booking number details
            $stmt = $db->prepare("SELECT booking_number FROM booking_numbers WHERE id = ?");
            $stmt->execute([$bookingNumberId]);
            $bookingNumberData = $stmt->fetch();
            
            if (!$bookingNumberData) {
                echo json_encode(['success' => false, 'message' => 'Booking number not found']);
                return;
            }
            
            // Get customer information
            $stmt = $db->prepare("SELECT fullname, email FROM users WHERE id = ? AND role = 'customer'");
            $stmt->execute([$customerId]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                echo json_encode(['success' => false, 'message' => 'Customer not found']);
                return;
            }
            
            // Assign booking number to customer
            $stmt = $db->prepare("
                INSERT INTO customer_booking_numbers 
                (customer_id, booking_number_id, assigned_by_admin_id, status, assigned_at) 
                VALUES (?, ?, ?, 'active', NOW())
            ");
            
            $adminId = $this->currentUser()['id'];
            $result = $stmt->execute([$customerId, $bookingNumberId, $adminId]);
            
            if ($result) {
                // Get the inserted ID
                $customerBookingNumberId = $db->lastInsertId();
                
                // Get the assigned_at timestamp for this assignment
                $stmt = $db->prepare("SELECT assigned_at FROM customer_booking_numbers WHERE id = ?");
                $stmt->execute([$customerBookingNumberId]);
                $assignmentData = $stmt->fetch();
                $assignedAt = $assignmentData['assigned_at'] ?? date('Y-m-d H:i:s');
                
                // Calculate queue position (line number)
                // Count how many customers with active booking numbers were assigned before or at the same time
                // This determines their position in the queue
                $stmt = $db->prepare("
                    SELECT COUNT(*) + 1 as queue_position
                    FROM customer_booking_numbers
                    WHERE status = 'active' 
                    AND (
                        assigned_at < ? 
                        OR (assigned_at = ? AND id < ?)
                    )
                ");
                $stmt->execute([$assignedAt, $assignedAt, $customerBookingNumberId]);
                $queueResult = $stmt->fetch();
                $queuePosition = (int)($queueResult['queue_position'] ?? 1);
                
                // Calculate customers ahead
                $customersAhead = max(0, $queuePosition - 1);
                
                // Send email notification with queue position and customers ahead
                require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                $notificationService = new NotificationService();
                $emailSent = $notificationService->sendBookingNumberAssignmentEmail(
                    $customer['email'],
                    $customer['fullname'],
                    $bookingNumberData['booking_number'],
                    $queuePosition,
                    $customersAhead
                );
                
                $message = 'Booking number assigned successfully';
                if ($emailSent) {
                    $message .= ' and email notification sent';
                } else {
                    $message .= ' (email notification failed)';
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => $message,
                    'queue_position' => $queuePosition,
                    'booking_number' => $bookingNumberData['booking_number']
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to assign booking number']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Delete Booking Number (AJAX)
     */
    public function deleteBookingNumber() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $bookingNumberId = $_POST['booking_number_id'] ?? null;
        
        if (!$bookingNumberId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking number ID is required']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            // Check if booking number is assigned to any customer
            $stmt = $db->prepare("SELECT id FROM customer_booking_numbers WHERE booking_number_id = ? AND status = 'active'");
            $stmt->execute([$bookingNumberId]);
            
            if ($stmt->fetch()) {
                echo json_encode(['success' => false, 'message' => 'Cannot delete booking number that is assigned to a customer']);
                return;
            }
            
            // Delete booking number
            $stmt = $db->prepare("DELETE FROM booking_numbers WHERE id = ?");
            $result = $stmt->execute([$bookingNumberId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Booking number deleted successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to delete booking number']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Get Workers/Technicians (AJAX)
     */
    public function getWorkers() {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get all users with role 'admin' or 'technician' or 'worker'
            $stmt = $db->prepare("
                SELECT id, fullname, email, role 
                FROM users 
                WHERE role IN ('admin', 'technician', 'worker') 
                ORDER BY fullname ASC
            ");
            $stmt->execute();
            $workers = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'workers' => $workers
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error loading workers: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Send Receipt to Customer (AJAX)
     * Called when admin views/generates a receipt - automatically sends it to customer
     */
    public function sendReceiptToCustomer() {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            $bookingId = $input['booking_id'] ?? $_POST['booking_id'] ?? null;
            
            if (empty($bookingId) || !is_numeric($bookingId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Update status to for_repair (ready for repair after sending receipt) if currently in inspection stage
            $checkStmt = $db->prepare("SELECT status FROM bookings WHERE id = ?");
            $checkStmt->execute([$bookingId]);
            $currentBooking = $checkStmt->fetch(PDO::FETCH_ASSOC);
            $currentStatus = $currentBooking['status'] ?? '';
            
            // Update status to for_repair if in inspection-related status (receipt sent, ready for repair)
            if (in_array($currentStatus, ['to_inspect', 'for_inspection', 'picked_up'])) {
                $updateStmt = $db->prepare("UPDATE bookings SET status = 'for_repair', quotation_sent_at = NOW(), updated_at = NOW() WHERE id = ?");
                $updateStmt->execute([$bookingId]);
            }
            
            // Use the existing sendReceiptNotification method
            $result = $this->sendReceiptNotification($bookingId);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Preview receipt sent to customer successfully. Status updated to "For Repair".',
                    'status' => 'for_repair'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Failed to send receipt to customer'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error in sendReceiptToCustomer: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while sending receipt',
                'error' => (defined('APP_DEBUG') && APP_DEBUG) ? $e->getMessage() : null
            ]);
        }
        exit;
    }
    
    /**
     * Save Receipt (AJAX)
     * Saves leather details, meters, price per meter, and labor fee
     */
    public function saveReceipt() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        try {
            // Get JSON input
            $input = json_decode(file_get_contents('php://input'), true);
            $bookingId = $input['booking_id'] ?? $_POST['booking_id'] ?? null;
            $leatherQuality = $input['leather_quality'] ?? $_POST['leather_quality'] ?? 'standard';
            $leatherColorId = $input['leather_color_id'] ?? $_POST['leather_color_id'] ?? null;
            $numberOfMeters = floatval($input['number_of_meters'] ?? $_POST['number_of_meters'] ?? 0);
            $pricePerMeter = floatval($input['price_per_meter'] ?? $_POST['price_per_meter'] ?? 0);
            $laborFee = floatval($input['labor_fee'] ?? $_POST['labor_fee'] ?? 0);
            
            if (!$bookingId || !is_numeric($bookingId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
                exit;
            }
            
            if ($numberOfMeters <= 0 || $pricePerMeter <= 0) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Number of meters and price per meter must be greater than 0']);
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Check if booking exists
            $stmt = $db->prepare("SELECT id FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            if (!$stmt->fetch()) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                exit;
            }
            
            // Calculate totals
            $fabricCost = $numberOfMeters * $pricePerMeter;
            $grandTotal = $fabricCost + $laborFee;
            
            // Get leather color name for notes
            $leatherColorName = '';
            if ($leatherColorId) {
                $inventoryModel = $this->model('Inventory');
                $color = $inventoryModel->getColorById($leatherColorId);
                $leatherColorName = $color ? $color['color_name'] : 'Selected Color';
            }
            
            // Check which columns exist in bookings table
            $checkColumns = $db->query("SHOW COLUMNS FROM bookings");
            $existingColumns = [];
            while ($row = $checkColumns->fetch(PDO::FETCH_ASSOC)) {
                $existingColumns[] = $row['Field'];
            }
            
            // Update booking with receipt data
            // Use existing columns: fabric_cost_per_meter, fabric_total (for total fabric cost), labor_fee, grand_total
            $updateData = [];
            
            // Only update selected_color_id if column exists and color is provided
            if (in_array('selected_color_id', $existingColumns) && $leatherColorId) {
                $updateData['selected_color_id'] = $leatherColorId;
            }
            
            // Save quality - check which column exists (color_type, fabric_type, or leather_type)
            if (in_array('color_type', $existingColumns)) {
                $updateData['color_type'] = $leatherQuality;
            } elseif (in_array('fabric_type', $existingColumns)) {
                $updateData['fabric_type'] = $leatherQuality;
            } elseif (in_array('leather_type', $existingColumns)) {
                $updateData['leather_type'] = $leatherQuality;
            }
            
            // Add pricing fields if they exist
            if (in_array('fabric_cost_per_meter', $existingColumns)) {
                $updateData['fabric_cost_per_meter'] = $pricePerMeter;
            }
            if (in_array('fabric_total', $existingColumns)) {
                $updateData['fabric_total'] = $fabricCost; // Total fabric cost (meters × price)
            }
            if (in_array('fabric_cost', $existingColumns)) {
                $updateData['fabric_cost'] = $fabricCost;
            }
            if (in_array('labor_fee', $existingColumns)) {
                $updateData['labor_fee'] = $laborFee;
            }
            if (in_array('grand_total', $existingColumns)) {
                $updateData['grand_total'] = $grandTotal;
            }
            if (in_array('total_amount', $existingColumns)) {
                $updateData['total_amount'] = $grandTotal;
            }
            if (in_array('calculation_notes', $existingColumns)) {
                $updateData['calculation_notes'] = "Receipt Details - Quality: " . ucfirst($leatherQuality) . ", Leather: {$leatherColorName}, Meters: {$numberOfMeters}, Price per Meter: ₱{$pricePerMeter}, Leather Cost: ₱{$fabricCost}, Labor Fee: ₱{$laborFee}, Grand Total: ₱{$grandTotal}";
            }
            
            // Always update updated_at if column exists
            if (in_array('updated_at', $existingColumns)) {
                $updateData['updated_at'] = date('Y-m-d H:i:s');
            }
            
            // Build update query only with existing columns
            if (empty($updateData)) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'No valid columns found to update']);
                exit;
            }
            
            $updateFields = [];
            $updateValues = [];
            foreach ($updateData as $field => $value) {
                $updateFields[] = "$field = ?";
                $updateValues[] = $value;
            }
            $updateValues[] = $bookingId;
            
            $updateQuery = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $result = $updateStmt->execute($updateValues);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Receipt saved successfully',
                    'grand_total' => $grandTotal,
                    'fabric_cost' => $fabricCost,
                    'labor_fee' => $laborFee
                ]);
            } else {
                $errorInfo = $updateStmt->errorInfo();
                error_log("Failed to save receipt - SQL Error: " . print_r($errorInfo, true));
                http_response_code(500);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to save receipt. Database error occurred.',
                    'error' => $errorInfo[2] ?? 'Unknown database error'
                ]);
            }
        } catch (Exception $e) {
            error_log("Error in saveReceipt: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'An error occurred while saving receipt: ' . $e->getMessage(),
                'error' => $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Save Measurements (AJAX)
     * Saves item measurements during inspection
     */
    public function saveMeasurements() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $bookingId = $input['booking_id'] ?? $_POST['booking_id'] ?? null;
            
            if (!$bookingId || !is_numeric($bookingId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Check which measurement columns exist
            $checkColumns = $db->query("SHOW COLUMNS FROM bookings");
            $existingColumns = [];
            while ($row = $checkColumns->fetch(PDO::FETCH_ASSOC)) {
                $existingColumns[] = $row['Field'];
            }
            
            $updateData = [];
            if (in_array('measurement_height', $existingColumns)) {
                $updateData['measurement_height'] = floatval($input['height'] ?? $_POST['height'] ?? 0);
            }
            if (in_array('measurement_width', $existingColumns)) {
                $updateData['measurement_width'] = floatval($input['width'] ?? $_POST['width'] ?? 0);
            }
            if (in_array('measurement_thickness', $existingColumns)) {
                $updateData['measurement_thickness'] = floatval($input['thickness'] ?? $_POST['thickness'] ?? 0);
            }
            if (in_array('measurement_custom', $existingColumns)) {
                $updateData['measurement_custom'] = $input['custom_measurements'] ?? $_POST['custom_measurements'] ?? '';
            }
            if (in_array('measurement_notes', $existingColumns)) {
                $updateData['measurement_notes'] = $input['notes'] ?? $_POST['notes'] ?? '';
            }
            
            if (empty($updateData)) {
                // Store in calculation_notes if no dedicated columns
                $notes = "Measurements - Height: " . ($input['height'] ?? $_POST['height'] ?? 'N/A') . 
                         "cm, Width: " . ($input['width'] ?? $_POST['width'] ?? 'N/A') . 
                         "cm, Thickness: " . ($input['thickness'] ?? $_POST['thickness'] ?? 'N/A') . 
                         "cm. " . ($input['custom_measurements'] ?? $_POST['custom_measurements'] ?? '');
                $updateData['calculation_notes'] = $notes;
            }
            
            if (in_array('updated_at', $existingColumns)) {
                $updateData['updated_at'] = date('Y-m-d H:i:s');
            }
            
            $updateFields = [];
            $updateValues = [];
            foreach ($updateData as $field => $value) {
                $updateFields[] = "$field = ?";
                $updateValues[] = $value;
            }
            $updateValues[] = $bookingId;
            
            $updateQuery = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $result = $updateStmt->execute($updateValues);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Measurements saved successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save measurements']);
            }
        } catch (Exception $e) {
            error_log("Error in saveMeasurements: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error saving measurements: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Save Damages (AJAX)
     * Saves damage/defect records during inspection
     */
    public function saveDamages() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $bookingId = $input['booking_id'] ?? $_POST['booking_id'] ?? null;
            
            if (!$bookingId || !is_numeric($bookingId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Check which damage columns exist
            $checkColumns = $db->query("SHOW COLUMNS FROM bookings");
            $existingColumns = [];
            while ($row = $checkColumns->fetch(PDO::FETCH_ASSOC)) {
                $existingColumns[] = $row['Field'];
            }
            
            $damageTypes = $input['damage_types'] ?? $_POST['damage_types'] ?? [];
            $damageDescription = $input['description'] ?? $_POST['description'] ?? '';
            $damageLocation = $input['location'] ?? $_POST['location'] ?? '';
            $damageSeverity = $input['severity'] ?? $_POST['severity'] ?? 'moderate';
            
            $updateData = [];
            
            // Store damage info in calculation_notes or dedicated columns
            $damageNotes = "DAMAGES RECORDED:\n";
            $damageNotes .= "Types: " . (is_array($damageTypes) ? implode(', ', $damageTypes) : $damageTypes) . "\n";
            $damageNotes .= "Description: " . $damageDescription . "\n";
            $damageNotes .= "Location: " . $damageLocation . "\n";
            $damageNotes .= "Severity: " . ucfirst($damageSeverity);
            
            if (in_array('damage_description', $existingColumns)) {
                $updateData['damage_description'] = $damageDescription;
            }
            if (in_array('damage_location', $existingColumns)) {
                $updateData['damage_location'] = $damageLocation;
            }
            if (in_array('damage_severity', $existingColumns)) {
                $updateData['damage_severity'] = $damageSeverity;
            }
            
            // Always update calculation_notes with damage info
            if (in_array('calculation_notes', $existingColumns)) {
                $existingNotes = '';
                $stmt = $db->prepare("SELECT calculation_notes FROM bookings WHERE id = ?");
                $stmt->execute([$bookingId]);
                $booking = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($booking && $booking['calculation_notes']) {
                    $existingNotes = $booking['calculation_notes'] . "\n\n";
                }
                $updateData['calculation_notes'] = $existingNotes . $damageNotes;
            }
            
            if (in_array('updated_at', $existingColumns)) {
                $updateData['updated_at'] = date('Y-m-d H:i:s');
            }
            
            if (empty($updateData)) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'No valid columns found to update']);
                exit;
            }
            
            $updateFields = [];
            $updateValues = [];
            foreach ($updateData as $field => $value) {
                $updateFields[] = "$field = ?";
                $updateValues[] = $value;
            }
            $updateValues[] = $bookingId;
            
            $updateQuery = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $result = $updateStmt->execute($updateValues);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Damage record saved successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save damage record']);
            }
        } catch (Exception $e) {
            error_log("Error in saveDamages: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error saving damages: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Save Materials (AJAX)
     * Saves materials/fabrics used during inspection
     */
    public function saveMaterials() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            $bookingId = $input['booking_id'] ?? $_POST['booking_id'] ?? null;
            
            if (!$bookingId || !is_numeric($bookingId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Check which material columns exist
            $checkColumns = $db->query("SHOW COLUMNS FROM bookings");
            $existingColumns = [];
            while ($row = $checkColumns->fetch(PDO::FETCH_ASSOC)) {
                $existingColumns[] = $row['Field'];
            }
            
            $fabricType = $input['fabric_type'] ?? $_POST['fabric_type'] ?? '';
            $meters = floatval($input['meters'] ?? $_POST['meters'] ?? 0);
            $foamReplacement = $input['foam_replacement'] ?? $_POST['foam_replacement'] ?? 'none';
            $foamThickness = floatval($input['foam_thickness'] ?? $_POST['foam_thickness'] ?? 0);
            $accessories = $input['accessories'] ?? $_POST['accessories'] ?? [];
            $notes = $input['notes'] ?? $_POST['notes'] ?? '';
            
            $updateData = [];
            
            // Store material info
            $materialNotes = "MATERIALS USED:\n";
            $materialNotes .= "Fabric Type: " . $fabricType . "\n";
            $materialNotes .= "Meters/Yards: " . $meters . "\n";
            $materialNotes .= "Foam Replacement: " . ucfirst($foamReplacement);
            if ($foamThickness > 0) {
                $materialNotes .= " (" . $foamThickness . " inches)";
            }
            $materialNotes .= "\n";
            if (is_array($accessories) && !empty($accessories)) {
                $materialNotes .= "Accessories: " . implode(', ', $accessories) . "\n";
            }
            if ($notes) {
                $materialNotes .= "Notes: " . $notes;
            }
            
            if (in_array('material_fabric_type', $existingColumns)) {
                $updateData['material_fabric_type'] = $fabricType;
            }
            if (in_array('material_meters', $existingColumns)) {
                $updateData['material_meters'] = $meters;
            }
            if (in_array('material_foam', $existingColumns)) {
                $updateData['material_foam'] = $foamReplacement;
            }
            if (in_array('material_foam_thickness', $existingColumns)) {
                $updateData['material_foam_thickness'] = $foamThickness;
            }
            if (in_array('material_notes', $existingColumns)) {
                $updateData['material_notes'] = $notes;
            }
            
            // Always update calculation_notes with material info
            if (in_array('calculation_notes', $existingColumns)) {
                $existingNotes = '';
                $stmt = $db->prepare("SELECT calculation_notes FROM bookings WHERE id = ?");
                $stmt->execute([$bookingId]);
                $booking = $stmt->fetch(PDO::FETCH_ASSOC);
                if ($booking && $booking['calculation_notes']) {
                    $existingNotes = $booking['calculation_notes'] . "\n\n";
                }
                $updateData['calculation_notes'] = $existingNotes . $materialNotes;
            }
            
            if (in_array('updated_at', $existingColumns)) {
                $updateData['updated_at'] = date('Y-m-d H:i:s');
            }
            
            if (empty($updateData)) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'No valid columns found to update']);
                exit;
            }
            
            $updateFields = [];
            $updateValues = [];
            foreach ($updateData as $field => $value) {
                $updateFields[] = "$field = ?";
                $updateValues[] = $value;
            }
            $updateValues[] = $bookingId;
            
            $updateQuery = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $result = $updateStmt->execute($updateValues);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Materials saved successfully']);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to save materials']);
            }
        } catch (Exception $e) {
            error_log("Error in saveMaterials: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error saving materials: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Get Booking Technician Assignment (AJAX)
     */
    public function getBookingTechnician($bookingId) {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if booking_technicians table exists
            $tableExists = false;
            try {
                $checkStmt = $db->query("SHOW TABLES LIKE 'booking_technicians'");
                $tableExists = $checkStmt->rowCount() > 0;
            } catch (Exception $e) {
                // Table doesn't exist
            }
            
            if ($tableExists) {
                $stmt = $db->prepare("
                    SELECT bt.technician_id, u.fullname as name
                    FROM booking_technicians bt
                    LEFT JOIN users u ON bt.technician_id = u.id
                    WHERE bt.booking_id = ? AND bt.status = 'active'
                    ORDER BY bt.assigned_at DESC
                    LIMIT 1
                ");
                $stmt->execute([$bookingId]);
                $technician = $stmt->fetch();
                
                if ($technician) {
                    echo json_encode([
                        'success' => true,
                        'technician' => [
                            'id' => $technician['technician_id'],
                            'name' => $technician['name']
                        ]
                    ]);
                } else {
                    echo json_encode([
                        'success' => true,
                        'technician' => null
                    ]);
                }
            } else {
                echo json_encode([
                    'success' => true,
                    'technician' => null
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error loading technician: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Get Booking Progress History (AJAX)
     */
    public function getBookingProgress($bookingId) {
        header('Content-Type: application/json');
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if booking_progress_logs table exists
            $tableExists = false;
            try {
                $checkStmt = $db->query("SHOW TABLES LIKE 'booking_progress_logs'");
                $tableExists = $checkStmt->rowCount() > 0;
            } catch (Exception $e) {
                // Table doesn't exist
            }
            
            if ($tableExists) {
                $stmt = $db->prepare("
                    SELECT bpl.*, u.fullname as admin_name
                    FROM booking_progress_logs bpl
                    LEFT JOIN users u ON bpl.admin_id = u.id
                    WHERE bpl.booking_id = ?
                    ORDER BY bpl.created_at DESC
                ");
                $stmt->execute([$bookingId]);
                $progress = $stmt->fetchAll();
                
                echo json_encode([
                    'success' => true,
                    'progress' => $progress
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'progress' => []
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error loading progress: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Cancel Booking (Admin)
     */
    public function cancelBooking($id) {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            $db->beginTransaction();
            
            // Check if booking exists
            $stmt = $db->prepare("SELECT id, status, payment_status FROM bookings WHERE id = ?");
            $stmt->execute([$id]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                throw new Exception('Booking not found');
            }
            
            // Only allow cancellation if not already completed and paid
            $status = strtolower($booking['status'] ?? '');
            $paymentStatus = strtolower($booking['payment_status'] ?? 'unpaid');
            
            if (in_array($status, ['completed', 'delivered_and_paid']) && 
                in_array($paymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod'])) {
                throw new Exception('Cannot cancel a completed and paid booking.');
            }
            
            // Update booking status to cancelled
            $stmt = $db->prepare("UPDATE bookings SET status = 'cancelled', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$id]);
            
            $db->commit();
            
            echo json_encode(['success' => true, 'message' => 'Booking cancelled successfully.']);
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Error cancelling booking: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Get Booking Compliance Data (AJAX)
     */
    public function getBookingCompliance() {
        $bookingId = $_GET['booking_id'] ?? null;
        $customerId = $_GET['customer_id'] ?? null;
        
        if (!$bookingId || !$customerId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID and Customer ID are required']);
            return;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            // Get customer information
            $stmt = $db->prepare("SELECT * FROM users WHERE id = ? AND role = 'customer'");
            $stmt->execute([$customerId]);
            $customer = $stmt->fetch();
            
            if (!$customer) {
                echo json_encode(['success' => false, 'message' => 'Customer not found']);
                return;
            }
            
            // Get current booking details
            // Note: Queue number is automatically assigned when customer submits reservation
            // No need to check customer_booking_numbers table
            $stmt = $db->prepare("
                SELECT b.*, s.service_name, s.service_type, sc.category_name
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                WHERE b.id = ? AND b.user_id = ?
            ");
            $stmt->execute([$bookingId, $customerId]);
            $booking = $stmt->fetch();
            
            // Check if booking was previously rejected
            // Check booking status for 'cancelled' or 'rejected'
            $hasRejection = false;
            $rejectionReason = null;
            
            if ($booking) {
                // Check if status is cancelled or rejected
                if (in_array(strtolower($booking['status']), ['cancelled', 'rejected'])) {
                    $hasRejection = true;
                }
                
                // Check if there's a rejection reason in notes
                if ($booking['notes'] && (
                    stripos($booking['notes'], 'rejected') !== false || 
                    stripos($booking['notes'], 'rejection') !== false ||
                    stripos($booking['notes'], 'declined') !== false
                )) {
                    $hasRejection = true;
                    // Try to extract rejection reason
                    if (preg_match('/rejection.*?reason[:\s]+(.+?)(?:\n|$)/i', $booking['notes'], $matches)) {
                        $rejectionReason = trim($matches[1]);
                    }
                }
                
                // Check for rejection records in notifications or logs if tables exist
                try {
                    $rejectionStmt = $db->prepare("
                        SELECT message 
                        FROM notifications 
                        WHERE related_id = ? 
                        AND (type = 'error' OR type = 'danger' OR message LIKE '%reject%' OR message LIKE '%decline%')
                        ORDER BY created_at DESC 
                        LIMIT 1
                    ");
                    $rejectionStmt->execute([$bookingId]);
                    $rejectionNotif = $rejectionStmt->fetch();
                    if ($rejectionNotif) {
                        $hasRejection = true;
                        if (!$rejectionReason) {
                            $rejectionReason = $rejectionNotif['message'];
                        }
                    }
                } catch (Exception $e) {
                    // Table might not exist, ignore
                }
            }
            
            // Calculate compliance score
            // Note: Queue number will be assigned by admin when approving reservation
            // Queue number is NOT a requirement for approval - admin assigns it during approval
            $complianceScore = 0;
            $totalChecks = 4; // Queue number assignment is done by admin, not a requirement
            
            // Queue number is optional - admin will assign it when approving
            // if ($booking && $booking['booking_number']) $complianceScore += 25; // Not required
            
            if ($customer['email']) $complianceScore += 25; // Valid email
            if ($customer['phone']) $complianceScore += 25; // Contact info
            if ($booking && $booking['service_id']) $complianceScore += 25; // Service selection
            if ($booking && $booking['booking_date']) $complianceScore += 25; // Booking date
            
            $complianceScore = round($complianceScore);
            
            echo json_encode([
                'success' => true,
                'customer' => $customer,
                'booking' => $booking,
                'complianceScore' => $complianceScore,
                'hasRejection' => $hasRejection,
                'rejectionReason' => $rejectionReason
            ]);
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
    
    /**
     * Email Notifications Management
     */
    public function emailNotifications() {
        $data = [
            'title' => 'Email Notifications - ' . APP_NAME,
            'user' => $this->currentUser()
        ];
        
        $this->view('admin/email_notifications', $data);
    }
    
    /**
     * Test Email Configuration
     */
    public function testEmail() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $testEmail = $_POST['test_email'] ?? null;
        
        if (!$testEmail || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Valid email address is required']);
            return;
        }
        
        try {
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            $result = $notificationService->testEmailConfiguration();
            
            if ($result) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Test email sent successfully to ' . $testEmail
                ]);
            } else {
                echo json_encode([
                    'success' => false, 
                    'message' => 'Failed to send test email. Check your email configuration.'
                ]);
            }
        } catch (Exception $e) {
            error_log("Test email error: " . $e->getMessage());
            echo json_encode([
                'success' => false, 
                'message' => 'Error sending test email: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get Notifications (AJAX)
     */
    public function getNotifications() {
        header('Content-Type: application/json');
        ob_start();
        
        try {
            if (!$this->isLoggedIn()) {
                http_response_code(401);
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                ob_end_flush();
                exit;
            }
            
            $db = Database::getInstance()->getConnection();
            
            // Get user ID from session directly as fallback
            $userId = null;
            if (isset($_SESSION['user_id']) && !empty($_SESSION['user_id'])) {
                $userId = $_SESSION['user_id'];
            } else {
                // Try currentUser method
                $currentUser = $this->currentUser();
                if ($currentUser && isset($currentUser['id'])) {
                    $userId = $currentUser['id'];
                }
            }
            
            if (!$userId) {
                http_response_code(401);
                ob_clean();
                echo json_encode(['success' => false, 'message' => 'User not found or not logged in']);
                ob_end_flush();
                exit;
            }
            
            // Check if notifications table exists
            $checkTable = $db->query("SHOW TABLES LIKE 'notifications'");
            if ($checkTable->rowCount() === 0) {
                // Table doesn't exist, return empty notifications
                ob_clean();
                echo json_encode([
                    'success' => true,
                    'unread_count' => 0,
                    'notifications' => []
                ]);
                ob_end_flush();
                exit;
            }
            
            // Get unread count
            $countStmt = $db->prepare("SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
            $countStmt->execute([$userId]);
            $countResult = $countStmt->fetch(PDO::FETCH_ASSOC);
            $unreadCount = $countResult['unread_count'] ?? 0;
            
            // Get recent notifications (last 10)
            $notifStmt = $db->prepare("
                SELECT id, title, message, type, is_read, created_at 
                FROM notifications 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT 10
            ");
            $notifStmt->execute([$userId]);
            $notifications = $notifStmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Format notifications for display
            $formattedNotifications = [];
            foreach ($notifications as $notif) {
                $formattedNotifications[] = [
                    'id' => $notif['id'] ?? 0,
                    'title' => $notif['title'] ?? '',
                    'message' => $notif['message'] ?? '',
                    'type' => $notif['type'] ?? 'info',
                    'is_read' => (bool)($notif['is_read'] ?? 0),
                    'created_at' => $notif['created_at'] ?? date('Y-m-d H:i:s'),
                    'time_ago' => isset($notif['created_at']) ? $this->timeAgo($notif['created_at']) : 'Just now'
                ];
            }
            
            ob_clean();
            echo json_encode([
                'success' => true,
                'unread_count' => (int)$unreadCount,
                'notifications' => $formattedNotifications
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log("Error in getNotifications: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            http_response_code(500);
            ob_clean();
            echo json_encode([
                'success' => false, 
                'message' => 'Error fetching notifications: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        } finally {
            if (ob_get_level()) {
                ob_end_flush();
            }
        }
        exit;
    }
    
    /**
     * Mark notification as read (AJAX)
     */
    public function markNotificationRead() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        if (!$this->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        $notificationId = $_POST['notification_id'] ?? null;
        
        if (!$notificationId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Notification ID is required']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $userId = $this->currentUser()['id'];
            
            // Mark notification as read
            $stmt = $db->prepare("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?");
            $stmt->execute([$notificationId, $userId]);
            
            echo json_encode(['success' => true, 'message' => 'Notification marked as read']);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Helper function to calculate time ago
     */
    private function timeAgo($datetime) {
        $timestamp = strtotime($datetime);
        $diff = time() - $timestamp;
        
        if ($diff < 60) {
            return 'Just now';
        } elseif ($diff < 3600) {
            $mins = floor($diff / 60);
            return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
        } else {
            return date('M d, Y', $timestamp);
        }
    }
    
    /**
     * Get Email Logs
     */
    public function getEmailLogs() {
        $logFile = ROOT . DS . 'logs' . DS . 'email_notifications.log';
        
        if (!file_exists($logFile)) {
            echo json_encode([
                'success' => true,
                'logs' => []
            ]);
            return;
        }
        
        $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        // Get last 50 logs
        $logs = array_slice(array_reverse($logs), 0, 50);
        
        echo json_encode([
            'success' => true,
            'logs' => $logs
        ]);
    }
    
    /**
     * Create quotation for a booking
     */
    public function createQuotation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $bookingId = $_POST['booking_id'] ?? null;
        $totalAmount = $_POST['total_amount'] ?? null;
        $validUntil = $_POST['valid_until'] ?? null;
        $notes = $_POST['notes'] ?? '';
        
        if (!$bookingId || !$totalAmount) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID and total amount are required']);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Verify booking exists
            $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                return;
            }
            
            $quotationModel = $this->model('Quotation');
            
            // Check if quotation already exists for this booking
            $existingQuotations = $quotationModel->getQuotationsByBookingId($bookingId);
            
            // If quotation exists, update it; otherwise create new
            if (!empty($existingQuotations)) {
                // Find draft quotation
                $draftQuotation = null;
                foreach ($existingQuotations as $quote) {
                    if ($quote['status'] === 'draft') {
                        $draftQuotation = $quote;
                        break;
                    }
                }
                
                if ($draftQuotation) {
                    // Update existing draft quotation
                    $updateData = [
                        'total_amount' => $totalAmount,
                        'notes' => $notes
                    ];
                    
                    if ($validUntil) {
                        $updateData['valid_until'] = $validUntil;
                    }
                    
                    $result = $quotationModel->update($draftQuotation['id'], $updateData);
                    $quotationId = $draftQuotation['id'];
                } else {
                    // Create new quotation
                    $quotationData = [
                        'booking_id' => $bookingId,
                        'total_amount' => $totalAmount,
                        'status' => 'draft',
                        'notes' => $notes
                    ];
                    
                    if ($validUntil) {
                        $quotationData['valid_until'] = $validUntil;
                    }
                    
                    $quotationId = $quotationModel->createQuotation($quotationData);
                    $result = $quotationId !== false;
                }
            } else {
                // Create new quotation
                $quotationData = [
                    'booking_id' => $bookingId,
                    'total_amount' => $totalAmount,
                    'status' => 'draft',
                    'notes' => $notes
                ];
                
                if ($validUntil) {
                    $quotationData['valid_until'] = $validUntil;
                }
                
                $quotationId = $quotationModel->createQuotation($quotationData);
                $result = $quotationId !== false;
            }
            
            if ($result) {
                $quotation = $quotationModel->getQuotationById($quotationId);
                echo json_encode([
                    'success' => true,
                    'message' => 'Quotation created successfully',
                    'quotation' => $quotation
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create quotation']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Send quotation to customer
     */
    public function sendQuotation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $quotationId = $_POST['quotation_id'] ?? null;
        
        if (!$quotationId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Quotation ID is required']);
            return;
        }
        
        try {
            $quotationModel = $this->model('Quotation');
            $quotation = $quotationModel->getQuotationById($quotationId);
            
            if (!$quotation) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Quotation not found']);
                return;
            }
            
            // Check if quotation has total amount
            if (!$quotation['total_amount']) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Quotation must have a total amount before sending']);
                return;
            }
            
            // Send quotation (change status to 'sent')
            $result = $quotationModel->sendQuotation($quotationId);
            
            if ($result) {
                // Send notification to customer (optional)
                $db = Database::getInstance()->getConnection();
                
                // Create notification for customer
                $hasRelatedId = false;
                try {
                    $checkStmt = $db->query("
                        SELECT COUNT(*) as cnt 
                        FROM INFORMATION_SCHEMA.COLUMNS 
                        WHERE TABLE_SCHEMA = DATABASE() 
                        AND TABLE_NAME = 'notifications' 
                        AND COLUMN_NAME = 'related_id'
                    ");
                    $hasRelatedId = $checkStmt->fetch()['cnt'] > 0;
                } catch (Exception $e) {
                    // Ignore error
                }
                
                if ($hasRelatedId) {
                    $notifStmt = $db->prepare("
                        INSERT INTO notifications (user_id, type, title, message, related_id, created_at) 
                        VALUES (?, 'info', ?, ?, ?, NOW())
                    ");
                    $notifStmt->execute([
                        $quotation['user_id'],
                        'New Quotation Available',
                        "A quotation has been sent for your booking. Quotation Number: " . $quotation['quotation_number'],
                        $quotationId
                    ]);
                } else {
                    $notifStmt = $db->prepare("
                        INSERT INTO notifications (user_id, type, title, message, created_at) 
                        VALUES (?, 'info', ?, ?, NOW())
                    ");
                    $notifStmt->execute([
                        $quotation['user_id'],
                        'New Quotation Available',
                        "A quotation has been sent for your booking. Quotation Number: " . $quotation['quotation_number']
                    ]);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Quotation sent to customer successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to send quotation']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Get quotations for admin
     */
    public function quotations() {
        $status = $_GET['status'] ?? null;
        $quotationModel = $this->model('Quotation');
        $quotations = $quotationModel->getAllQuotations($status);
        
        $data = [
            'title' => 'Quotations - ' . APP_NAME,
            'user' => $this->currentUser(),
            'quotations' => $quotations,
            'currentStatus' => $status
        ];
        
        $this->view('admin/quotations', $data);
    }
    
    /**
     * Services Management
     */
    public function services() {
        $serviceModel = $this->model('Service');
        $services = $serviceModel->getAllServices();
        $categories = $serviceModel->getAllCategories();
        
        $data = [
            'title' => 'Services Management - ' . APP_NAME,
            'user' => $this->currentUser(),
            'services' => $services,
            'categories' => $categories
        ];
        
        $this->view('admin/services', $data);
    }
    
    /**
     * Create Service
     */
    public function createService() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $serviceName = trim($_POST['service_name'] ?? '');
        $serviceType = trim($_POST['service_type'] ?? '');
        $categoryId = $_POST['category_id'] ?? null;
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? null;
        $status = $_POST['status'] ?? 'active';
        
        if (empty($serviceName)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Service name is required']);
            return;
        }
        
        try {
            $serviceModel = $this->model('Service');
            
            $serviceData = [
                'service_name' => $serviceName,
                'service_type' => $serviceType,
                'description' => $description,
                'price' => $price ? (float)$price : null,
                'status' => $status
            ];
            
            if ($categoryId) {
                $serviceData['category_id'] = (int)$categoryId;
            }
            
            $serviceId = $serviceModel->createService($serviceData);
            
            if ($serviceId) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Service created successfully',
                    'service_id' => $serviceId
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create service']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Update Service
     */
    public function updateService() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $serviceId = $_POST['service_id'] ?? null;
        $serviceName = trim($_POST['service_name'] ?? '');
        $serviceType = trim($_POST['service_type'] ?? '');
        $categoryId = $_POST['category_id'] ?? null;
        $description = trim($_POST['description'] ?? '');
        $price = $_POST['price'] ?? null;
        $status = $_POST['status'] ?? 'active';
        
        if (!$serviceId || empty($serviceName)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Service ID and name are required']);
            return;
        }
        
        try {
            $serviceModel = $this->model('Service');
            
            // Verify service exists
            $service = $serviceModel->getById($serviceId);
            if (!$service) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Service not found']);
                return;
            }
            
            $updateData = [
                'service_name' => $serviceName,
                'service_type' => $serviceType,
                'description' => $description,
                'price' => $price ? (float)$price : null,
                'status' => $status
            ];
            
            if ($categoryId) {
                $updateData['category_id'] = (int)$categoryId;
            } else {
                $updateData['category_id'] = null;
            }
            
            $result = $serviceModel->updateService($serviceId, $updateData);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Service updated successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to update service']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Delete Service
     */
    public function deleteService() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $serviceId = $_POST['service_id'] ?? null;
        
        if (!$serviceId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Service ID is required']);
            return;
        }
        
        try {
            $serviceModel = $this->model('Service');
            
            // Verify service exists
            $service = $serviceModel->getById($serviceId);
            if (!$service) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Service not found']);
                return;
            }
            
            // Soft delete (set status to inactive)
            $result = $serviceModel->deleteService($serviceId);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Service deleted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to delete service']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
}

