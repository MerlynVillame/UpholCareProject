<?php
/**
 * Customer Controller
 */

require_once ROOT . DS . 'core' . DS . 'Controller.php';
require_once ROOT . DS . 'helpers' . DS . 'FeeCalculator.php';

class CustomerController extends Controller {
    
    private $bookingModel;
    private $serviceModel;
    private $storeModel;
    private $businessModel;
    
    public function __construct() {
        // Require customer role for all methods
        $this->requireRole(ROLE_CUSTOMER);
        $this->bookingModel = $this->model('Booking');
        $this->serviceModel = $this->model('Service');
        $this->storeModel = $this->model('Store');
        $this->businessModel = $this->model('CustomerBusiness');
    }
    
    /**
     * Create store_ratings table if it doesn't exist
     * This is called automatically when needed
     */
    private function createStoreRatingsTable($db) {
        try {
            // Check if table already exists
            $checkStmt = $db->query("SHOW TABLES LIKE 'store_ratings'");
            if ($checkStmt->fetch()) {
                error_log("INFO: store_ratings table already exists");
                return true;
            }
            
            // Create table (without foreign keys first to avoid errors)
            $createTableSql = "
                CREATE TABLE store_ratings (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    store_id INT NOT NULL,
                    user_id INT NOT NULL,
                    rating DECIMAL(2, 1) NOT NULL COMMENT 'Rating from 1.0 to 5.0',
                    review_text TEXT NULL COMMENT 'Optional review text',
                    status ENUM('active', 'hidden') DEFAULT 'active',
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                    UNIQUE KEY unique_store_user_rating (store_id, user_id),
                    INDEX idx_store_id (store_id),
                    INDEX idx_user_id (user_id),
                    INDEX idx_rating (rating),
                    INDEX idx_created_at (created_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ";
            
            $db->exec($createTableSql);
            error_log("SUCCESS: store_ratings table created successfully");
            
            // Verify table was created
            $verifyStmt = $db->query("SHOW TABLES LIKE 'store_ratings'");
            if (!$verifyStmt->fetch()) {
                throw new Exception("Table creation verification failed - table does not exist after creation");
            }
            
            // Try to add foreign keys (optional - may fail, that's OK)
            try {
                // Check if store_locations table exists before adding FK
                $checkStoreTable = $db->query("SHOW TABLES LIKE 'store_locations'");
                if ($checkStoreTable->fetch()) {
                    try {
                        // Check if constraint already exists
                        $checkConstraint = $db->query("
                            SELECT COUNT(*) as cnt FROM information_schema.table_constraints 
                            WHERE table_schema = DATABASE() 
                            AND table_name = 'store_ratings' 
                            AND constraint_name = 'fk_store_ratings_store_id'
                        ");
                        $constraintExists = $checkConstraint->fetch()['cnt'] > 0;
                        
                        if (!$constraintExists) {
                            $db->exec("
                                ALTER TABLE store_ratings 
                                ADD CONSTRAINT fk_store_ratings_store_id 
                                FOREIGN KEY (store_id) REFERENCES store_locations(id) ON DELETE CASCADE
                            ");
                            error_log("SUCCESS: Foreign key fk_store_ratings_store_id added");
                        }
                    } catch (Exception $e) {
                        error_log("Warning: Could not add foreign key for store_id: " . $e->getMessage());
                        // Continue - foreign key is optional
                    }
                }
                
                // Check if users table exists before adding FK
                $checkUsersTable = $db->query("SHOW TABLES LIKE 'users'");
                if ($checkUsersTable->fetch()) {
                    try {
                        // Check if constraint already exists
                        $checkConstraint = $db->query("
                            SELECT COUNT(*) as cnt FROM information_schema.table_constraints 
                            WHERE table_schema = DATABASE() 
                            AND table_name = 'store_ratings' 
                            AND constraint_name = 'fk_store_ratings_user_id'
                        ");
                        $constraintExists = $checkConstraint->fetch()['cnt'] > 0;
                        
                        if (!$constraintExists) {
                            $db->exec("
                                ALTER TABLE store_ratings 
                                ADD CONSTRAINT fk_store_ratings_user_id 
                                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                            ");
                            error_log("SUCCESS: Foreign key fk_store_ratings_user_id added");
                        }
                    } catch (Exception $e) {
                        error_log("Warning: Could not add foreign key for user_id: " . $e->getMessage());
                        // Continue - foreign key is optional
                    }
                }
            } catch (Exception $e) {
                error_log("Warning: Error adding foreign keys: " . $e->getMessage());
                // Continue without foreign keys - table will still work
            }
            
            return true;
        } catch (Exception $e) {
            error_log("ERROR: Failed to create store_ratings table: " . $e->getMessage());
            error_log("ERROR: Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }
    
    /**
     * Customer Dashboard
     */
    public function dashboard() {
        $userId = $this->currentUser()['id'];
        
        // Get statistics
        $totalBookings = $this->bookingModel->getTotalBookingsCount($userId);
        $pendingBookings = $this->bookingModel->getBookingCountByStatus($userId, 'pending');
        $inProgressBookings = $this->bookingModel->getBookingCountByStatus($userId, 'in_progress');
        $completedBookings = $this->bookingModel->getBookingCountByStatus($userId, 'completed');
        $totalSpent = $this->bookingModel->getTotalSpent($userId);
        
        // Get recent bookings
        $recentBookings = $this->bookingModel->getRecentBookings($userId, 5);
        
        $data = [
            'title' => 'Dashboard - ' . APP_NAME,
            'user' => $this->currentUser(),
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings,
            'inProgressBookings' => $inProgressBookings,
            'completedBookings' => $completedBookings,
            'totalSpent' => $totalSpent,
            'recentBookings' => $recentBookings
        ];
        
        $this->view('customer/dashboard', $data);
    }
    
    /**
     * My Bookings
     */
    public function bookings() {
        $userId = $this->currentUser()['id'];
        $status = $_GET['status'] ?? 'all';
        
        $bookings = $this->bookingModel->getCustomerBookings($userId, $status);
        
        // Get repair reservations
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT ri.*, 'repair' as booking_type
                FROM repair_items ri
                WHERE ri.customer_id = ?
                ORDER BY ri.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        $repairReservations = $stmt->fetchAll();
        
        $data = [
            'title' => 'My Bookings - ' . APP_NAME,
            'user' => $this->currentUser(),
            'bookings' => $bookings,
            'repairReservations' => $repairReservations,
            'currentStatus' => $status,
            'totalBookings' => $this->bookingModel->getTotalBookingsCount($userId),
            'pendingCount' => $this->bookingModel->getBookingCountByStatus($userId, 'pending')
        ];
        
        $this->view('customer/bookings', $data);
    }
    
    /**
     * New Booking - Redirected to unified repair reservation form
     */
    public function newBooking() {
        // Redirect to the unified repair reservation form
        $this->redirect('customer/newRepairReservation');
    }
    
    /**
     * Process New Booking
     */
    public function processBooking() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('customer/bookings');
        }
        
        $userId = $this->currentUser()['id'];
        
        // Get the service that matches the category and service name
        $categoryId = $_POST['service_category'];
        $serviceName = $_POST['service_type']; // Now contains service_name instead of service_type
        
        // Try to find by service name first (new approach)
        $service = $this->serviceModel->getByCategoryAndName($categoryId, $serviceName);
        if ($service) {
            $serviceId = $service['id'];
            $serviceType = $service['service_type'] ?? $serviceName;
        } else {
            // Fallback to old method (service_type) for backward compatibility
            $services = $this->serviceModel->getByCategoryAndType($categoryId, $serviceName);
            if (!empty($services)) {
                $service = $services[0];
                $serviceId = $service['id'];
                $serviceType = $service['service_type'] ?? $serviceName;
            } else {
                $serviceId = null;
                $serviceType = $serviceName;
            }
        }
        
        if (!$serviceId) {
            $_SESSION['error'] = 'Service not found. Please try again.';
            $this->redirect('customer/newRepairReservation');
            return;
        }
        
        // Check if this is a business mode booking
        $isBusinessMode = isset($_GET['mode']) && $_GET['mode'] === 'business';
        $customerBusinessId = null;

        if ($isBusinessMode) {
            // Verify role from database
            $userModel = $this->model('User');
            $dbUser = $userModel->getById($userId);
            
            if ($this->currentUser()['role'] !== 'customer' || !$dbUser || $dbUser['role'] !== 'customer') {
                $_SESSION['error'] = 'Access denied: Only customers can make business bookings.';
                $this->redirect('customer/profile');
                return;
            }

            $businessProfile = $this->customerBusinessModel->getBusinessProfile($userId);
            if (!$businessProfile || $businessProfile['status'] !== 'approved') {
                $_SESSION['error'] = 'Your business account is not yet approved. Please complete your business profile and wait for Super Admin approval.';
                $this->redirect('customer/profile');
                return;
            }
            $customerBusinessId = $businessProfile['id'];
        }
        
        $db = Database::getInstance()->getConnection();
        
        // Check availability: fabric/color stock and store capacity
        $storeLocationId = $_POST['store_location_id'] ?? null;
        $selectedColorId = $_POST['selected_color_id'] ?? null;
        $serviceOption = strtolower(trim($_POST['service_option'] ?? 'pickup'));
        
        // Only check availability if not delivery service (delivery selects fabric during inspection)
        if ($serviceOption !== 'delivery' && $selectedColorId && $storeLocationId) {
            // Check if fabric/color is in stock at the selected store
            $inventoryModel = $this->model('Inventory');
            $color = $inventoryModel->getColorById($selectedColorId);
            
            if (!$color) {
                $_SESSION['error'] = 'Selected fabric/color not found. Please select a different color.';
                $this->redirect('customer/newRepairReservation');
                return;
            }
            
            // Check if color is available at the store
            // Color is available if:
            // 1. store_location_id matches the selected store (assigned to this store)
            // 2. store_location_id is NULL (available at all stores)
            $colorStoreId = $color['store_location_id'] ?? null;
            $storeLocationIdInt = intval($storeLocationId);
            $colorStoreIdInt = $colorStoreId ? intval($colorStoreId) : null;
            
            // Allow if color is assigned to this store OR if color is available at all stores (NULL)
            if ($colorStoreIdInt !== null && $colorStoreIdInt !== $storeLocationIdInt) {
                $_SESSION['error'] = 'Selected fabric/color is not available at the chosen store. Please select a different color or store.';
                $this->redirect('customer/newRepairReservation');
                return;
            }
            
            // Check if color is out of stock
            $stockQuantity = floatval($color['quantity'] ?? 0);
            if ($stockQuantity <= 0) {
                $_SESSION['error'] = 'Selected fabric/color is out of stock. Please select a different color.';
                $this->redirect('customer/newRepairReservation');
                return;
            }
        }
        
        // Check store capacity (check if store is fully booked)
        if ($storeLocationId) {
            // Count active bookings for this store (pending, approved, in_progress, etc.)
            $stmt = $db->prepare("
                SELECT COUNT(*) as active_count
                FROM bookings
                WHERE store_location_id = ?
                AND status IN ('pending', 'for_pickup', 'picked_up', 'for_inspection', 'to_inspect', 'preview_receipt_sent', 'under_repair')
            ");
            $stmt->execute([$storeLocationId]);
            $storeCapacity = $stmt->fetch();
            $activeBookings = (int)($storeCapacity['active_count'] ?? 0);
            
            // Get store capacity limit (you may need to add this field to stores table)
            // For now, we'll use a default limit of 50 bookings per store
            $maxCapacity = 50; // TODO: Get from stores table if capacity field exists
            
            if ($activeBookings >= $maxCapacity) {
                $_SESSION['error'] = 'The selected store is currently fully booked. Please try another store or check back later.';
                $this->redirect('customer/newRepairReservation');
                return;
            }
        }
        
        // IMPORTANT: Always set status explicitly - never leave it NULL or empty
        $defaultStatus = $isBusinessMode ? 'admin_review' : 'pending_schedule';
        
        // Get base service price
        $baseServicePrice = floatval($_POST['total_amount'] ?? 0.00);
        
        // Service option already retrieved above
        
        // Map service_option to delivery_type for FeeCalculator
        // FeeCalculator expects: 'pickup', 'delivery', or 'pickup_and_delivery'
        $deliveryType = 'pickup'; // default
        if ($serviceOption === 'delivery') {
            $deliveryType = 'delivery';
        } elseif ($serviceOption === 'both') {
            $deliveryType = 'pickup_and_delivery';
        } elseif ($serviceOption === 'walk_in') {
            $deliveryType = 'pickup'; // Walk-in doesn't need pickup/delivery fees
        }
        
        // Distance removed - no longer required
        $distanceKm = 0.00;
        
        // Calculate fees using FeeCalculator
        $fees = FeeCalculator::calculateFees($baseServicePrice, $deliveryType, $distanceKm);
        
        // For walk-in, no pickup/delivery fees
        if ($serviceOption === 'walk_in') {
            $fees['pickup_fee'] = 0;
            $fees['delivery_fee'] = 0;
            $fees['gas_fee'] = 0;
            $fees['travel_fee'] = 0;
            $fees['total_additional_fees'] = $fees['labor_fee']; // Only labor fee
            $fees['grand_total'] = $baseServicePrice + $fees['labor_fee'];
        }
        
        // Handle pickup and delivery addresses and dates based on service option
        $pickupAddress = null;
        $pickupDate = null;
        $deliveryAddress = null;
        $deliveryDate = null;
        
        if ($serviceOption === 'pickup' || $serviceOption === 'both') {
            $pickupAddress = $_POST['pickup_address'] ?? null;
            $pickupDate = $_POST['pickup_date'] ?? null;
            if (!$pickupAddress) {
                $_SESSION['error'] = 'Please provide pickup address.';
                $this->redirect('customer/newRepairReservation');
                return;
            }
        }
        
        // Handle delivery service option - customer drops off item to shop (no address needed, only date)
        if ($serviceOption === 'delivery') {
            $deliveryDate = $_POST['delivery_date'] ?? null;
            if (!$deliveryDate) {
                $_SESSION['error'] = 'Please select a drop-off date.';
                $this->redirect('customer/newRepairReservation');
                return;
            }
            $deliveryAddress = null; // No address needed for delivery service (customer brings item to shop)
        } elseif ($serviceOption === 'both') {
            // For "both" service: needs delivery address (for final delivery after repair)
            // Drop-off date is not required for "Both" option (only pickup date is needed)
            $deliveryAddress = $_POST['delivery_address'] ?? null;
            // If delivery address is not provided, use user's account address
            if (!$deliveryAddress) {
                $userModel = $this->model('User');
                $userDetails = $userModel->getById($userId);
                $deliveryAddress = $userDetails['address'] ?? null;
            }
            if (!$deliveryAddress) {
                $_SESSION['error'] = 'Please provide delivery address for final delivery or update your account address.';
                $this->redirect('customer/newRepairReservation');
                return;
            }
            // No delivery date validation needed for "Both" option
            $deliveryDate = null;
        }
        
        // Get color selection and calculate color price
        $selectedColorId = $_POST['selected_color_id'] ?? null;
        $colorType = $_POST['color_type'] ?? 'standard';
        $colorPrice = 0.00;
        
        if ($selectedColorId) {
            $inventoryModel = $this->model('Inventory');
            $colorPrice = $inventoryModel->getColorPrice($selectedColorId, $colorType);
        }
        
        // Add color price to grand total
        $finalGrandTotal = $fees['grand_total'] + $colorPrice;
        
        $bookingData = [
            'user_id' => $userId,
            'service_id' => $serviceId,
            'booking_number_id' => null, // No booking number - availability based on stock and capacity
            'store_location_id' => $storeLocationId,
            'service_type' => $serviceType,
            'service_option' => $serviceOption, // Store the service option
            'item_description' => $_POST['item_description'] ?? '',
            'pickup_date' => $pickupDate,
            'pickup_address' => $pickupAddress,
            'delivery_address' => $deliveryAddress,
            'delivery_date' => $deliveryDate,
            'notes' => $_POST['notes'] ?? '',
            'selected_color_id' => $selectedColorId,
            'color_type' => $colorType,
            'color_price' => $colorPrice,
            'total_amount' => $baseServicePrice, // Base service price
            'labor_fee' => $fees['labor_fee'],
            'pickup_fee' => $fees['pickup_fee'],
            'delivery_fee' => $fees['delivery_fee'],
            'gas_fee' => $fees['gas_fee'],
            'travel_fee' => $fees['travel_fee'],
            'distance_km' => $fees['distance_km'],
            'total_additional_fees' => $fees['total_additional_fees'],
            'grand_total' => $finalGrandTotal, // Include color price
            // Payment status logic:
            // Pick Up and Walk In: unpaid (will be paid full cash)
            // Delivery and Both: unpaid (will be paid on delivery COD)
            'payment_status' => 'unpaid',
            'booking_type' => $isBusinessMode ? 'business' : 'personal',
            'customer_business_id' => $customerBusinessId,
            'status' => $defaultStatus // ALWAYS set status - never NULL or empty
        ];
        
        // Remove null values that might cause issues, but keep booking_number_id
        $bookingData = array_filter($bookingData, function($value, $key) {
            // Keep booking_number_id even if it's null (shouldn't be null now)
            if ($key === 'booking_number_id') {
                return true;
            }
            return $value !== null;
        }, ARRAY_FILTER_USE_BOTH);
        
        $bookingId = $this->bookingModel->createBooking($bookingData);
        
        if ($bookingId) {
            // Calculate customers ahead (for email notification)
            $customersAhead = max(0, $queueNumber - 1);
            
            // Send confirmation email to customer WITH queue number
            $this->sendBookingConfirmationEmail($bookingId, $userId, $newBookingNumber, $queueNumber, $customersAhead);
            
            // Notify admin about new booking
            $this->notifyAdminAboutNewBooking($bookingId, $userId);
            
            if ($isBusinessMode) {
                $_SESSION['success'] = 'Business booking created successfully! Your Queue Number: ' . $newBookingNumber . '. Admin will review your booking.';
            } else {
                $_SESSION['success'] = 'Booking created successfully! Your Queue Number: ' . $newBookingNumber . '. Admin will review and approve it.';
            }
        } else {
            $_SESSION['error'] = 'Failed to create booking. Please try again.';
        }
        
        if ($isBusinessMode) {
            $this->redirect('customer/businessBookings');
        } else {
            $this->redirect('customer/bookings');
        }
    }
    
    /**
     * Process New Booking via AJAX
     */
    /**
     * Check Logistic Availability (AJAX)
     */
    public function checkLogisticAvailability() {
        header('Content-Type: application/json');
        
        $storeId = $_GET['store_id'] ?? null;
        $date = $_GET['date'] ?? null;
        $type = $_GET['type'] ?? 'pickup'; // 'pickup' or 'delivery'
        
        if (!$storeId || !$date) {
            echo json_encode(['success' => false, 'message' => 'Missing parameters']);
            exit;
        }
        
        $count = $this->getLogisticCount($storeId, $date, $type);
        $max = $this->getLogisticCapacity($storeId, $date, $type);
        
        echo json_encode([
            'success' => true,
            'available' => ($count < $max),
            'count' => $count,
            'max' => $max,
            'remaining' => max(0, $max - $count)
        ]);
        exit;
    }

    private function getLogisticCount($storeId, $date, $type) {
        $db = Database::getInstance()->getConnection();
        if ($type === 'pickup') {
            $sql = "SELECT COUNT(*) FROM bookings WHERE store_location_id = ? AND pickup_date = ? AND status NOT IN ('cancelled', 'rejected', 'declined')";
        } else if ($type === 'delivery') {
            $sql = "SELECT COUNT(*) FROM bookings WHERE store_location_id = ? AND delivery_date = ? AND status NOT IN ('cancelled', 'rejected', 'declined')";
        } else {
            return 0;
        }
        $stmt = $db->prepare($sql);
        $stmt->execute([$storeId, $date]);
        return (int)$stmt->fetchColumn();
    }

    private function getLogisticCapacity($storeId, $date, $type) {
        $db = Database::getInstance()->getConnection();
        $column = 'max_' . $type;
        if (!in_array($column, ['max_pickup', 'max_delivery', 'max_inspection'])) {
            $column = 'max_pickup';
        }
        
        $sql = "SELECT $column FROM store_logistic_capacities WHERE store_id = ? AND date = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$storeId, $date]);
        $val = $stmt->fetchColumn();
        
        if ($val === false) {
            if ($type === 'pickup') return 2;
            if ($type === 'delivery') return 2;
            if ($type === 'inspection') return 3;
        }
        return (int)$val;
    }

    public function processBookingAjax() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        header('Content-Type: application/json');
        
        $userId = $this->currentUser()['id'];
        
        // Get the service that matches the category and service name
        $categoryId = $_POST['service_category'] ?? null;
        $serviceName = $_POST['service_type'] ?? null;
        
        if (!$categoryId || !$serviceName) {
            echo json_encode(['success' => false, 'message' => 'Please select service category and type']);
            exit;
        }
        
        $service = $this->serviceModel->getByCategoryAndName($categoryId, $serviceName);
        if ($service) {
            $serviceId = $service['id'];
        } else {
            $services = $this->serviceModel->getByCategoryAndType($categoryId, $serviceName);
            $serviceId = !empty($services) ? $services[0]['id'] : null;
        }
        
        if (!$serviceId) {
            echo json_encode(['success' => false, 'message' => 'Service not found. Please try again.']);
            exit;
        }
        
        // Check if this is a business mode booking
        $isBusinessMode = isset($_GET['mode']) && $_GET['mode'] === 'business';
        $customerBusinessId = null;

        if ($isBusinessMode) {
            // Verify role from database
            $userModel = $this->model('User');
            $dbUser = $userModel->getById($userId);
            
            if ($this->currentUser()['role'] !== 'customer' || !$dbUser || $dbUser['role'] !== 'customer') {
                echo json_encode(['success' => false, 'message' => 'Access denied: Only customers can make business bookings.']);
                exit;
            }

            $businessProfile = $this->customerBusinessModel->getBusinessProfile($userId);
            if (!$businessProfile || $businessProfile['status'] !== 'approved') {
                echo json_encode(['success' => false, 'message' => 'Your business account is not yet approved. Please complete your business profile and wait for Super Admin approval.']);
                exit;
            }
            $customerBusinessId = $businessProfile['id'];
        }

        $storeLocationId = $_POST['store_location_id'] ?? null;
        $selectedColorId = $_POST['selected_color_id'] ?? null;
        $serviceOption = strtolower(trim($_POST['service_option'] ?? 'pickup'));
        
        $pickupDate = $_POST['pickup_date'] ?? null;
        $deliveryDate = $_POST['delivery_date'] ?? null;

        // Logistic Capacity Validation
        if ($storeLocationId) {
            if (($serviceOption === 'pickup' || $serviceOption === 'both') && $pickupDate) {
                $count = $this->getLogisticCount($storeLocationId, $pickupDate, 'pickup');
                $max = $this->getLogisticCapacity($storeLocationId, $pickupDate, 'pickup');
                if ($count >= $max) {
                    echo json_encode(['success' => false, 'message' => "Pickup service is fully booked on " . date('M d, Y', strtotime($pickupDate)) . ". Please choose another date."]);
                    exit;
                }
            }
            if ($serviceOption === 'delivery' && $deliveryDate) {
                // For Customer Drop-off, we check inspection capacity as owner must be at shop
                $count = $this->getLogisticCount($storeLocationId, $deliveryDate, 'delivery');
                $max = $this->getLogisticCapacity($storeLocationId, $deliveryDate, 'inspection');
                if ($count >= $max) {
                    echo json_encode(['success' => false, 'message' => "Inspection slots are fully booked for drop-offs on " . date('M d, Y', strtotime($deliveryDate)) . ". Please choose another date."]);
                    exit;
                }
            }
        }
        
        try {
            $pickupAddress = $_POST['pickup_address'] ?? null;
            $deliveryAddress = $_POST['delivery_address'] ?? null;
            $notes = $_POST['notes'] ?? '';
            $totalAmount = floatval($_POST['total_amount'] ?? 0.00);
            
            $bookingData = [
                'user_id' => $userId,
                'service_id' => $serviceId,
                'store_location_id' => $storeLocationId,
                'selected_color_id' => $selectedColorId,
                'service_option' => $serviceOption,
                'pickup_date' => $pickupDate,
                'delivery_date' => $deliveryDate,
                'pickup_address' => $pickupAddress,
                'delivery_address' => $deliveryAddress,
                'notes' => $notes,
                'total_amount' => $totalAmount,
                'status' => $isBusinessMode ? 'admin_review' : 'pending_schedule',
                'customer_business_id' => $customerBusinessId,
                'payment_status' => 'unpaid'
            ];
            
            $bookingId = $this->bookingModel->createBooking($bookingData);
            
            if ($bookingId) {
                echo json_encode([
                    'success' => true, 
                    'message' => 'Reservation created successfully!',
                    'booking_id' => $bookingId
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create reservation.']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * View Booking Details
     */
    public function viewBooking($id) {
        $userId = $this->currentUser()['id'];
        $booking = $this->bookingModel->getBookingDetails($id, $userId);
        
        if (!$booking) {
            $_SESSION['error'] = 'Booking not found.';
            $this->redirect('customer/bookings');
        }
        
        // Get user's address from database
        $userModel = $this->model('User');
        $userDetails = $userModel->getById($userId);
        $userAddress = $userDetails['address'] ?? '';
        
        $data = [
            'title' => 'Booking Details - ' . APP_NAME,
            'user' => $this->currentUser(),
            'booking' => $booking,
            'userAddress' => $userAddress
        ];
        
        $this->view('customer/view_booking', $data);
    }

    /**
     * View Booking Details Partial (for Modal)
     */
    public function viewBookingPartial($id) {
        $userId = $this->currentUser()['id'];
        $booking = $this->bookingModel->getBookingDetails($id, $userId);
        
        if (!$booking) {
            echo '<div class="alert alert-danger">Booking not found.</div>';
            exit;
        }
        
        // Get user's address from database
        $userModel = $this->model('User');
        $userDetails = $userModel->getById($userId);
        $userAddress = $userDetails['address'] ?? '';
        
        $data = [
            'booking' => $booking,
            'userAddress' => $userAddress
        ];
        
        $this->view('customer/booking_details_partial', $data);
        exit;
    }

    /**
     * Get Booking Details (AJAX)
     */
    public function getBookingDetails($id) {
        header('Content-Type: application/json');
        // Prevent caching
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $userId = $this->currentUser()['id'];
        $booking = $this->bookingModel->getBookingDetails($id, $userId);
        if ($booking) {
            // Preserve the actual status from database - only default to 'pending' if truly NULL or empty
            // Don't override valid statuses
            $status = trim($booking['status'] ?? '');
            if ($status === '' || $status === null || strtolower($status) === 'null') {
                $booking['status'] = 'pending';
            } else {
                // Preserve the actual status from database
                $booking['status'] = $status;
            }
            
            // Debug logging to track status
            error_log("Customer getBookingDetails - Booking ID: " . $id . ", Status: " . $booking['status']);
            
            echo json_encode(['success' => true, 'data' => $booking]);
        } else {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
        }
        exit;
    }
    
    /**
     * Cancel Booking
     */
    public function cancelBooking($id) {
        $userId = $this->currentUser()['id'];
        
        if ($this->bookingModel->cancelBooking($id, $userId)) {
            $_SESSION['success'] = 'Booking cancelled successfully.';
        } else {
            $_SESSION['error'] = 'Unable to cancel booking. It may have already been processed.';
        }
        
        $this->redirect('customer/bookings');
    }
    
    /**
     * Delete Booking
     */
    public function deleteBooking($id) {
        header('Content-Type: application/json');
        
        $userId = $this->currentUser()['id'];
        $db = Database::getInstance()->getConnection();
        
        try {
            // Check if booking exists and belongs to user
            $stmt = $db->prepare("SELECT id, status FROM bookings WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                exit;
            }
            
            // Only allow deletion if status is pending or cancelled
            $status = strtolower($booking['status'] ?? '');
            if (!in_array($status, ['pending', 'cancelled'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'You can only delete pending or cancelled bookings.']);
                exit;
            }
            
            // Delete the booking
            $stmt = $db->prepare("DELETE FROM bookings WHERE id = ? AND user_id = ?");
            $stmt->execute([$id, $userId]);
            
            echo json_encode(['success' => true, 'message' => 'Booking deleted successfully.']);
        } catch (Exception $e) {
            error_log("Error deleting booking: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error deleting booking.']);
        }
        exit;
    }
    
    /**
     * Update Service Option
     */
    public function updateServiceOption() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        $userId = $this->currentUser()['id'];
        $bookingId = $_POST['booking_id'] ?? null;
        $newServiceOption = strtolower(trim($_POST['service_option'] ?? ''));
        
        if (!$bookingId || !$newServiceOption) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID and service option are required']);
            exit;
        }
        
        if (!in_array($newServiceOption, ['pickup', 'delivery', 'both', 'walk_in'])) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid service option']);
            exit;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            $db->beginTransaction();
            
            // Check if booking exists, belongs to user, and status allows update
            $stmt = $db->prepare("SELECT id, status, service_id, total_amount FROM bookings WHERE id = ? AND user_id = ?");
            $stmt->execute([$bookingId, $userId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                throw new Exception('Booking not found');
            }
            
            $status = strtolower($booking['status'] ?? '');
            if (!in_array($status, ['pending'])) {
                throw new Exception('You can only update service option when booking status is Pending.');
            }
            
            // Get service details for fee calculation
            $stmt = $db->prepare("SELECT price FROM services WHERE id = ?");
            $stmt->execute([$booking['service_id']]);
            $service = $stmt->fetch();
            $baseServicePrice = floatval($service['price'] ?? $booking['total_amount']);
            
            // Map service option to delivery type for FeeCalculator
            $deliveryType = 'pickup';
            if ($newServiceOption === 'delivery') {
                $deliveryType = 'delivery';
            } elseif ($newServiceOption === 'both') {
                $deliveryType = 'pickup_and_delivery';
            }
            
            // Distance removed - no longer required
            // Use 0 for distance (fees will be calculated without distance-based charges)
            $distanceKm = 0.00;
            
            // Calculate fees (without distance-based charges)
            require_once ROOT . DS . 'helpers' . DS . 'FeeCalculator.php';
            $fees = FeeCalculator::calculateFees($baseServicePrice, $deliveryType, $distanceKm);
            
            // For walk-in, no pickup/delivery fees
            if ($newServiceOption === 'walk_in') {
                $fees['pickup_fee'] = 0;
                $fees['delivery_fee'] = 0;
                $fees['gas_fee'] = 0;
                $fees['travel_fee'] = 0;
                $fees['total_additional_fees'] = $fees['labor_fee'];
                $fees['grand_total'] = $baseServicePrice + $fees['labor_fee'];
            }
            
            // Handle addresses
            $pickupAddress = null;
            $deliveryAddress = null;
            
            if ($newServiceOption === 'pickup' || $newServiceOption === 'both') {
                $pickupAddress = $_POST['pickup_address'] ?? null;
                if (!$pickupAddress) {
                    throw new Exception('Pickup address is required.');
                }
            }
            
            // Handle delivery service option - customer drops off item to shop (no address needed)
            if ($newServiceOption === 'delivery') {
                $deliveryAddress = null; // No address needed for delivery service (customer brings item to shop)
            } elseif ($newServiceOption === 'both') {
                // For "both" service: needs delivery address (for final delivery after repair)
                $deliveryAddress = $_POST['delivery_address'] ?? null;
                // If delivery address is not provided, use user's account address
                if (!$deliveryAddress) {
                    $userModel = $this->model('User');
                    $userDetails = $userModel->getById($userId);
                    $deliveryAddress = $userDetails['address'] ?? null;
                }
                if (!$deliveryAddress) {
                    throw new Exception('Delivery address is required for final delivery. Please provide it or update your account address.');
                }
            }
            
            // Update booking
            $updateData = [
                'service_option' => $newServiceOption,
                'pickup_address' => $pickupAddress,
                'delivery_address' => $deliveryAddress,
                'labor_fee' => $fees['labor_fee'],
                'pickup_fee' => $fees['pickup_fee'],
                'delivery_fee' => $fees['delivery_fee'],
                'gas_fee' => $fees['gas_fee'],
                'travel_fee' => $fees['travel_fee'],
                'distance_km' => $fees['distance_km'],
                'total_additional_fees' => $fees['total_additional_fees'],
                'grand_total' => $fees['grand_total'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $setParts = [];
            $values = [];
            foreach ($updateData as $key => $value) {
                $setParts[] = "`$key` = ?";
                $values[] = $value;
            }
            $values[] = $bookingId;
            $values[] = $userId;
            
            $sql = "UPDATE bookings SET " . implode(', ', $setParts) . " WHERE id = ? AND user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute($values);
            
            $db->commit();
            
            echo json_encode(['success' => true, 'message' => 'Service option updated successfully.']);
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Error updating service option: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Update Booking Details (Service, Date, Address)
     * Allows customers to update booking details when status is pending, approved, or in_progress
     */
    public function updateBooking() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        $userId = $this->currentUser()['id'];
        $bookingId = $_POST['booking_id'] ?? null;
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            exit;
        }
        
        $db = Database::getInstance()->getConnection();
        
        try {
            $db->beginTransaction();
            
            // Check if booking exists, belongs to user, and status allows update
            $stmt = $db->prepare("SELECT id, status, service_id, total_amount, service_option FROM bookings WHERE id = ? AND user_id = ?");
            $stmt->execute([$bookingId, $userId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                throw new Exception('Booking not found');
            }
            
            $status = strtolower($booking['status'] ?? '');
            $allowedStatuses = ['pending', 'under_repair'];
            
            if (!in_array($status, $allowedStatuses)) {
                throw new Exception('You can only update booking details when status is Pending or Under Repair.');
            }
            
            // Get service details for fee calculation
            $stmt = $db->prepare("SELECT price FROM services WHERE id = ?");
            $stmt->execute([$booking['service_id']]);
            $service = $stmt->fetch();
            $baseServicePrice = floatval($service['price'] ?? $booking['total_amount']);
            
            // Update service option if provided
            $newServiceOption = $_POST['service_option'] ?? null;
            if ($newServiceOption) {
                $newServiceOption = strtolower(trim($newServiceOption));
                if (!in_array($newServiceOption, ['pickup', 'delivery', 'both', 'walk_in'])) {
                    throw new Exception('Invalid service option');
                }
            } else {
                $newServiceOption = $booking['service_option'] ?? 'pickup';
            }
            
            // Map service option to delivery type for FeeCalculator
            $deliveryType = 'pickup';
            if ($newServiceOption === 'delivery') {
                $deliveryType = 'delivery';
            } elseif ($newServiceOption === 'both') {
                $deliveryType = 'pickup_and_delivery';
            }
            
            $distanceKm = 0.00;
            
            // Calculate fees
            require_once ROOT . DS . 'helpers' . DS . 'FeeCalculator.php';
            $fees = FeeCalculator::calculateFees($baseServicePrice, $deliveryType, $distanceKm);
            
            // For walk-in, no pickup/delivery fees
            if ($newServiceOption === 'walk_in') {
                $fees['pickup_fee'] = 0;
                $fees['delivery_fee'] = 0;
                $fees['gas_fee'] = 0;
                $fees['travel_fee'] = 0;
                $fees['total_additional_fees'] = $fees['labor_fee'];
                $fees['grand_total'] = $baseServicePrice + $fees['labor_fee'];
            }
            
            // Handle addresses
            $pickupAddress = $_POST['pickup_address'] ?? null;
            $deliveryAddress = $_POST['delivery_address'] ?? null;
            
            if ($newServiceOption === 'pickup' || $newServiceOption === 'both') {
                if (!$pickupAddress) {
                    $pickupAddress = $booking['pickup_address'] ?? null;
                }
                if (!$pickupAddress) {
                    throw new Exception('Pickup address is required.');
                }
            }
            
            // Handle delivery service option - customer drops off item to shop (no address needed)
            if ($newServiceOption === 'delivery') {
                $deliveryAddress = null; // No address needed for delivery service (customer brings item to shop)
            } elseif ($newServiceOption === 'both') {
                // For "both" service: needs delivery address (for final delivery after repair)
                if (!$deliveryAddress) {
                    $deliveryAddress = $booking['delivery_address'] ?? null;
                    // If still not provided, use user's account address
                    if (!$deliveryAddress) {
                        $userModel = $this->model('User');
                        $userDetails = $userModel->getById($userId);
                        $deliveryAddress = $userDetails['address'] ?? null;
                    }
                }
                if (!$deliveryAddress) {
                    throw new Exception('Delivery address is required for final delivery. Please provide it or update your account address.');
                }
            }
            
            // Update dates if provided
            $pickupDate = $_POST['pickup_date'] ?? null;
            $deliveryDate = $_POST['delivery_date'] ?? null;
            
            // Build update data
            $updateData = [
                'service_option' => $newServiceOption,
                'labor_fee' => $fees['labor_fee'],
                'pickup_fee' => $fees['pickup_fee'],
                'delivery_fee' => $fees['delivery_fee'],
                'gas_fee' => $fees['gas_fee'],
                'travel_fee' => $fees['travel_fee'],
                'distance_km' => $fees['distance_km'],
                'total_additional_fees' => $fees['total_additional_fees'],
                'grand_total' => $fees['grand_total'],
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            // Add addresses if provided
            if ($pickupAddress !== null) {
                $updateData['pickup_address'] = $pickupAddress;
            }
            if ($deliveryAddress !== null) {
                $updateData['delivery_address'] = $deliveryAddress;
            }
            
            // Add dates if provided
            if ($pickupDate) {
                $updateData['pickup_date'] = $pickupDate;
            }
            if ($deliveryDate) {
                $updateData['delivery_date'] = $deliveryDate;
            }
            
            // Update booking
            $setParts = [];
            $values = [];
            foreach ($updateData as $key => $value) {
                $setParts[] = "`$key` = ?";
                $values[] = $value;
            }
            $values[] = $bookingId;
            $values[] = $userId;
            
            $sql = "UPDATE bookings SET " . implode(', ', $setParts) . " WHERE id = ? AND user_id = ?";
            $stmt = $db->prepare($sql);
            $stmt->execute($values);
            
            $db->commit();
            
            echo json_encode([
                'success' => true, 
                'message' => 'Booking details updated successfully.',
                'data' => $updateData
            ]);
        } catch (Exception $e) {
            $db->rollBack();
            error_log("Error updating booking: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Payments
     */
    public function payments() {
        $userId = $this->currentUser()['id'];
        
        // Get all bookings with payment and delivery information
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT b.*, s.service_name, s.service_type, sc.category_name,
                COALESCE(b.status, 'pending') as status,
                COALESCE(b.payment_status, 'unpaid') as payment_status,
                b.pickup_date, b.delivery_date, b.pickup_address, b.delivery_address,
                b.service_option, b.created_at, b.quotation_sent_at, b.grand_total,
                b.total_amount, b.labor_fee, b.pickup_fee, b.delivery_fee, b.materials_cost,
                b.fabric_cost, b.service_fee, b.notes as calculation_notes,
                CASE WHEN b.quotation_sent_at IS NOT NULL THEN 1 ELSE 0 END as quotation_sent
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                WHERE b.user_id = ?
                ORDER BY b.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        $bookings = $stmt->fetchAll();
        
        $data = [
            'title' => 'Payments - ' . APP_NAME,
            'user' => $this->currentUser(),
            'bookings' => $bookings
        ];
        
        $this->view('customer/payments', $data);
    }
    
    
    /**
     * Request Receipt (AJAX)
     */
    public function requestReceipt() {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }
        
        $bookingId = $_POST['booking_id'] ?? null;
        $bookingNumber = $_POST['booking_number'] ?? null;
        $userId = $this->currentUser()['id'];
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Verify booking belongs to user and is paid
            $stmt = $db->prepare("
                SELECT b.*, CONCAT('Booking #', b.id) as booking_number
                FROM bookings b
                WHERE b.id = ? AND b.user_id = ?
            ");
            $stmt->execute([$bookingId, $userId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                exit;
            }
            
            // Check if booking is paid
            $paymentStatus = strtolower(trim($booking['payment_status'] ?? 'unpaid'));
            if (!in_array($paymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod'])) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Receipt can only be requested for paid bookings']);
                exit;
            }
            
            // Get customer details
            $userModel = $this->model('User');
            $customer = $userModel->getById($userId);
            
            // Get all admin users
            $stmt = $db->prepare("SELECT id FROM users WHERE role = 'admin' AND status = 'active'");
            $stmt->execute();
            $admins = $stmt->fetchAll();
            
            // Create notification for all admins about receipt request
            $bookingNumberDisplay = $bookingNumber ?: ($booking['booking_number'] ?? 'N/A');
            $customerName = $customer['fullname'] ?? 'Customer';
            
            foreach ($admins as $admin) {
                $stmt = $db->prepare("
                    INSERT INTO notifications (user_id, type, title, message, related_id, related_type, created_at)
                    VALUES (?, 'info', ?, ?, ?, 'booking', NOW())
                ");
                $title = 'Receipt Request';
                $message = "Customer {$customerName} (Booking: {$bookingNumberDisplay}) has requested a receipt.";
                $stmt->execute([
                    $admin['id'],
                    $title,
                    $message,
                    $bookingId
                ]);
            }
            
            // Also create a notification for the customer confirming the request
            $stmt = $db->prepare("
                INSERT INTO notifications (user_id, type, title, message, related_id, related_type, created_at)
                VALUES (?, 'success', ?, ?, ?, 'booking', NOW())
            ");
            $stmt->execute([
                $userId,
                'Receipt Request Submitted',
                'Your receipt request has been submitted. The receipt will be sent to your notification once processed by the admin.',
                $bookingId
            ]);
            
            echo json_encode([
                'success' => true,
                'message' => 'Receipt request submitted successfully. The receipt will be sent to your notification.'
            ]);
        } catch (Exception $e) {
            error_log("Error requesting receipt: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to submit receipt request. Please try again.']);
        }
        exit;
    }
    
    /**
     * Services
     */
    public function services() {
        $services = $this->serviceModel->getAllActive();
        $categories = $this->serviceModel->getCategories();
        
        // Filter out "Vehicle Repair", "Furniture Repair", and "Bedding Repair" categories
        $categories = array_filter($categories, function($category) {
            $categoryName = strtolower(trim($category['name'] ?? ''));
            return $categoryName !== 'vehicle repair' 
                && $categoryName !== 'furniture repair' 
                && $categoryName !== 'bedding repair';
        });
        // Re-index array after filtering
        $categories = array_values($categories);
        
        $data = [
            'title' => 'Services - ' . APP_NAME,
            'user' => $this->currentUser(),
            'services' => $services,
            'categories' => $categories
        ];
        
        $this->view('customer/services', $data);
    }
    
    /**
     * Services Catalog - Disabled/Removed
     * Redirects to bookings page
     */
    public function servicesCatalog() {
        // Services Catalog has been removed from customer portal
        // Redirect to bookings page
        $this->redirect('customer/bookings');
    }
    
    /**
     * Fabric/Color Catalog
     */
    public function fabricsCatalog() {
        $inventoryModel = $this->model('Inventory');
        $storeModel = $this->model('Store');
        
        // Get all available colors/fabrics
        $colors = $inventoryModel->getAll();
        
        // Get all stores for filter
        $stores = $storeModel->getAll();
        
        $data = [
            'title' => 'Fabric/Color Catalog - ' . APP_NAME,
            'user' => $this->currentUser(),
            'colors' => $colors,
            'stores' => $stores
        ];
        
        $this->view('customer/fabrics_catalog', $data);
    }
    
    /**
     * Quotations
     */
    public function quotations() {
        $userId = $this->currentUser()['id'];
        $quotationModel = $this->model('Quotation');
        $quotations = $quotationModel->getCustomerQuotations($userId);
        
        $data = [
            'title' => 'Quotations - ' . APP_NAME,
            'user' => $this->currentUser(),
            'quotations' => $quotations
        ];
        
        $this->view('customer/quotations', $data);
    }
    
    /**
     * Request quotation for a booking
     */
    public function requestQuotation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $bookingId = $_POST['booking_id'] ?? null;
        $userId = $this->currentUser()['id'];
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Verify booking belongs to user
            $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
            $stmt->execute([$bookingId, $userId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                return;
            }
            
            // Check if quotation already exists for this booking
            $quotationModel = $this->model('Quotation');
            $existingQuotations = $quotationModel->getQuotationsByBookingId($bookingId);
            
            if (!empty($existingQuotations)) {
                // Check if there's a pending or sent quotation
                $pendingQuotation = null;
                foreach ($existingQuotations as $quote) {
                    if (in_array($quote['status'], ['draft', 'sent'])) {
                        $pendingQuotation = $quote;
                        break;
                    }
                }
                
                if ($pendingQuotation) {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false, 
                        'message' => 'A quotation request already exists for this booking',
                        'quotation_id' => $pendingQuotation['id']
                    ]);
                    return;
                }
            }
            
            // Create quotation request (status: draft, admin will complete it)
            $quotationData = [
                'booking_id' => $bookingId,
                'status' => 'draft',
                'notes' => 'Quotation requested by customer'
            ];
            
            $quotationId = $quotationModel->createQuotation($quotationData);
            
            if ($quotationId) {
                // Get the created quotation
                $quotation = $quotationModel->getQuotationById($quotationId);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Quotation request submitted successfully. Admin will prepare the quotation.',
                    'quotation' => $quotation
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create quotation request']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Accept quotation
     */
    public function acceptQuotation() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        $quotationId = $_POST['quotation_id'] ?? null;
        $userId = $this->currentUser()['id'];
        
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
            
            // Verify quotation belongs to user
            if ($quotation['user_id'] != $userId) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Unauthorized']);
                return;
            }
            
            // Check if quotation is already accepted or rejected
            if ($quotation['status'] === 'accepted') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Quotation already accepted']);
                return;
            }
            
            if ($quotation['status'] === 'rejected') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Quotation has been rejected']);
                return;
            }
            
            // Check if quotation is expired
            if ($quotationModel->isQuotationExpired($quotationId)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Quotation has expired']);
                return;
            }
            
            // Check if quotation status is 'sent' (only sent quotations can be accepted)
            if ($quotation['status'] !== 'sent') {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Quotation is not yet sent by admin']);
                return;
            }
            
            // Accept quotation
            $result = $quotationModel->acceptQuotation($quotationId);
            
            if ($result) {
                // Update booking total amount if quotation is accepted
                if ($quotation['total_amount'] && $quotation['booking_id']) {
                    $db = Database::getInstance()->getConnection();
                    $stmt = $db->prepare("UPDATE bookings SET total_amount = ? WHERE id = ?");
                    $stmt->execute([$quotation['total_amount'], $quotation['booking_id']]);
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Quotation accepted successfully'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to accept quotation']);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Accept Booking Quotation/Receipt Preview (for bookings)
     */
    public function acceptBookingQuotation() {
        ob_start();
        
        // Set JSON header
        if (!headers_sent()) {
            header('Content-Type: application/json');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            ob_clean();
            http_response_code(405);
            echo json_encode([
                'success' => false, 
                'message' => 'Invalid request method. Expected POST, got ' . $_SERVER['REQUEST_METHOD'],
            ]);
            ob_end_flush();
            exit;
        }
        
        $json = file_get_contents("php://input");
        $data = json_decode($json, true);

        if (!$data || !isset($data['booking_id'])) {
                ob_clean();
                http_response_code(400);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid data sent.'
            ]);
                ob_end_flush();
                exit;
            }
            
        $bookingId = $data['booking_id'];

        // Get current user
            $currentUser = $this->currentUser();
            if (!$currentUser || !isset($currentUser['id'])) {
                ob_clean();
                http_response_code(401);
                echo json_encode(['success' => false, 'message' => 'User not authenticated']);
                ob_end_flush();
                exit;
            }
            
            $userId = $currentUser['id'];
            $db = Database::getInstance()->getConnection();
        
        // Verify booking belongs to user and get status
                $stmt = $db->prepare("SELECT id, status FROM bookings WHERE id = ? AND user_id = ?");
            $stmt->execute([$bookingId, $userId]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
        if (!$booking) {
                ob_clean();
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found or does not belong to you.']);
                ob_end_flush();
                exit;
            }
            
        // Check if status allows approval
        $allowedStatuses = ['inspection_completed_waiting_approval', 'inspect_completed', 'preview_receipt_sent'];
        if (!in_array($booking['status'], $allowedStatuses)) {
                ob_clean();
                http_response_code(400);
                echo json_encode([
                    'success' => false, 
                'message' => 'Receipt cannot be approved. Current status: ' . $booking['status']
                ]);
                ob_end_flush();
                exit;
            }
            
        // Update booking status to under_repair
        try {
            // Build update query with optional columns
            $updateFields = ['status = ?', 'updated_at = NOW()'];
            $updateValues = ['under_repair'];
            
            // Check and add optional columns if they exist
            $columns = ['quotation_accepted', 'quotation_accepted_at', 'repair_start_date'];
            foreach ($columns as $col) {
            try {
                    $check = $db->query("SHOW COLUMNS FROM bookings LIKE '{$col}'");
                    if ($check && $check->fetch()) {
                        if ($col === 'quotation_accepted') {
                            $updateFields[] = 'quotation_accepted = 1';
                        } elseif ($col === 'quotation_accepted_at') {
                            $updateFields[] = 'quotation_accepted_at = NOW()';
                        } elseif ($col === 'repair_start_date') {
                            $updateFields[] = 'repair_start_date = ?';
                            $updateValues[] = date('Y-m-d H:i:s');
                        }
                    }
            } catch (Exception $e) {
                    // Column doesn't exist, skip it
                }
            }
            
            $updateSql = "UPDATE bookings SET " . implode(', ', $updateFields) . " WHERE id = ?";
            $updateValues[] = $bookingId;
            
                $updateStmt = $db->prepare($updateSql);
            $result = $updateStmt->execute($updateValues);
                
            if ($result) {
            ob_clean();
                if (!headers_sent()) {
                    header('Content-Type: application/json');
                }
            echo json_encode([
                'success' => true,
                    'message' => 'Receipt approved successfully'
            ]);
            ob_end_flush();
            exit;
            } else {
                throw new Exception('Database update failed');
            }
        } catch (Exception $e) {
            ob_clean();
            if (!headers_sent()) {
                header('Content-Type: application/json');
            }
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Database error occurred while approving receipt. Please try again or contact support.'
            ]);
            ob_end_flush();
            exit;
        }
    }
    
    /**
     * Reject Booking Quotation/Receipt Preview (for bookings)
     */
    public function rejectBookingQuotation() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $bookingId = $input['booking_id'] ?? null;
        $reason = $input['reason'] ?? '';
        
        if (!$bookingId) {
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            exit;
        }
        
        if (empty($reason)) {
            echo json_encode(['success' => false, 'message' => 'Rejection reason is required']);
            exit;
        }
        
        $userId = $this->currentUser()['id'];
        $db = Database::getInstance()->getConnection();
        
        // Verify booking belongs to user
        $stmt = $db->prepare("SELECT id, quotation_sent FROM bookings WHERE id = ? AND user_id = ?");
        $stmt->execute([$bookingId, $userId]);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
            exit;
        }
        
        if (!$booking['quotation_sent']) {
            echo json_encode(['success' => false, 'message' => 'Quotation not yet sent']);
            exit;
        }
        
        // Update booking - mark quotation as rejected
        $updateStmt = $db->prepare("UPDATE bookings SET quotation_rejected = 1, quotation_rejection_reason = ?, quotation_rejected_at = NOW() WHERE id = ?");
        $updateStmt->execute([$reason, $bookingId]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Quotation rejected. Our team will review your feedback.'
        ]);
        exit;
    }
    
    /**
     * View quotation details
     */
    public function viewQuotation($quotationId = null) {
        if (!$quotationId) {
            $quotationId = $_GET['id'] ?? null;
        }
        
        if (!$quotationId) {
            header('Location: ' . BASE_URL . 'customer/quotations');
            exit;
        }
        
        $userId = $this->currentUser()['id'];
        $quotationModel = $this->model('Quotation');
        $quotation = $quotationModel->getQuotationById($quotationId);
        
        if (!$quotation || $quotation['user_id'] != $userId) {
            $_SESSION['error'] = 'Quotation not found';
            header('Location: ' . BASE_URL . 'customer/quotations');
            exit;
        }
        
        $data = [
            'title' => 'Quotation Details - ' . APP_NAME,
            'user' => $this->currentUser(),
            'quotation' => $quotation
        ];
        
        $this->view('customer/view_quotation', $data);
    }
    
    /**
     * History
     * Shows only completed/paid, delivered_and_paid, and cancelled bookings
     * These are finished transactions that should not appear in active bookings
     */
    public function history() {
        $userId = $this->currentUser()['id'];
        
        // Get only completed/paid, delivered_and_paid, and cancelled bookings
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT b.*, 
                CONCAT('Booking #', b.id) as booking_number,
                s.service_name, 
                s.service_type, 
                sc.category_name,
                u.fullname as customer_name, 
                u.email, 
                u.phone,
                COALESCE(b.status, 'pending') as status,
                COALESCE(b.payment_status, 'unpaid') as payment_status
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.user_id = ? 
                AND (
                    -- Completed bookings that are paid
                    (LOWER(COALESCE(b.status, 'pending')) = 'completed' 
                     AND LOWER(COALESCE(b.payment_status, 'unpaid')) IN ('paid', 'paid_full_cash', 'paid_on_delivery_cod'))
                    OR
                    -- Delivered and paid bookings
                    LOWER(COALESCE(b.status, 'pending')) = 'delivered_and_paid'
                    OR
                    -- Cancelled bookings
                    LOWER(COALESCE(b.status, 'pending')) = 'cancelled'
                )
                ORDER BY b.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$userId]);
        $bookings = $stmt->fetchAll();
        
        $data = [
            'title' => 'History - ' . APP_NAME,
            'user' => $this->currentUser(),
            'bookings' => $bookings
        ];
        
        $this->view('customer/history', $data);
    }
    
    /**
     * Download Receipt
     */
    public function downloadReceipt($bookingId) {
        $userId = $this->currentUser()['id'];
        $db = Database::getInstance()->getConnection();
        
        // Get booking details
        $sql = "SELECT b.*, 
                CONCAT('Booking #', b.id) as booking_number,
                s.service_name, 
                s.service_type, 
                sc.category_name,
                u.fullname as customer_name, 
                u.email, 
                u.phone,
                COALESCE(b.status, 'pending') as status,
                COALESCE(b.payment_status, 'unpaid') as payment_status
                FROM bookings b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.id = ? AND b.user_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$bookingId, $userId]);
        $booking = $stmt->fetch();
        
        if (!$booking) {
            $_SESSION['error'] = 'Booking not found';
            $this->redirect('customer/history');
            return;
        }
        
        $paymentStatus = strtolower($booking['payment_status'] ?? 'unpaid');
        $isPaid = in_array($paymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod']);
        
        if (!$isPaid) {
            $_SESSION['error'] = 'Receipt is only available for paid bookings';
            $this->redirect('customer/history');
            return;
        }
        
        // Generate receipt HTML
        $laborFee = floatval($booking['labor_fee'] ?? 0);
        $pickupFee = floatval($booking['pickup_fee'] ?? 0);
        $deliveryFee = floatval($booking['delivery_fee'] ?? 0);
        $colorPrice = floatval($booking['color_price'] ?? 0);
        $grandTotal = $laborFee + $pickupFee + $deliveryFee + $colorPrice;
        
        $receiptHtml = '
        <!DOCTYPE html>
        <html>
        <head>
            <title>Receipt - ' . htmlspecialchars($booking['booking_number']) . '</title>
            <style>
                body { font-family: Arial, sans-serif; padding: 40px; background: #f8f9fc; }
                .receipt-container { background: white; padding: 40px; border-radius: 10px; max-width: 800px; margin: 0 auto; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
                .header { text-align: center; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 3px solid #4e73df; }
                .header h1 { color: #4e73df; font-size: 2.5rem; margin: 0; font-weight: 700; }
                .header p { margin: 5px 0; color: #6c757d; }
                .receipt-title { color: #28a745; font-weight: 600; font-size: 1.2rem; }
                .info-section { margin: 20px 0; }
                .info-section p { margin: 5px 0; }
                table { width: 100%; border-collapse: collapse; margin: 20px 0; }
                table td { padding: 12px; border: 1px solid #e3e6f0; }
                table tr:last-child { background: #28a745; color: white; font-weight: bold; font-size: 1.1rem; }
                .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 2px solid #e3e6f0; color: #6c757d; }
            </style>
        </head>
        <body>
            <div class="receipt-container">
                <div class="header">
                    <h1>UpholCare</h1>
                    <p style="font-size: 1.2rem; font-weight: 600;">Upholstery Services</p>
                    <p class="receipt-title">Payment Receipt</p>
                </div>
                
                <div class="info-section">
                    <p><strong>Booking Number:</strong> ' . htmlspecialchars($booking['booking_number'] ?? 'N/A') . '</p>
                    <p><strong>Date:</strong> ' . date('F d, Y', strtotime($booking['created_at'])) . '</p>
                    <p><strong>Service:</strong> ' . htmlspecialchars($booking['service_name'] ?? 'N/A') . '</p>
                    <p><strong>Item Description:</strong> ' . htmlspecialchars($booking['item_description'] ?? 'N/A') . '</p>
                    <p><strong>Customer:</strong> ' . htmlspecialchars($booking['customer_name'] ?? 'N/A') . '</p>
                </div>
                
                <h3 style="color: #2c3e50; margin-top: 30px;">Payment Breakdown</h3>
                <table>
                    <tbody>';
        
        if ($laborFee > 0) {
            $receiptHtml .= '<tr><td>Labor Fee</td><td style="text-align: right;">₱' . number_format($laborFee, 2) . '</td></tr>';
        }
        if ($pickupFee > 0) {
            $receiptHtml .= '<tr><td>Pickup Fee</td><td style="text-align: right;">₱' . number_format($pickupFee, 2) . '</td></tr>';
        }
        if ($deliveryFee > 0) {
            $receiptHtml .= '<tr><td>Delivery Fee</td><td style="text-align: right;">₱' . number_format($deliveryFee, 2) . '</td></tr>';
        }
        if ($colorPrice > 0) {
            $receiptHtml .= '<tr><td>Fabric/Color Price</td><td style="text-align: right;">₱' . number_format($colorPrice, 2) . '</td></tr>';
        }
        
        $receiptHtml .= '
                        <tr>
                            <td><strong>TOTAL AMOUNT</strong></td>
                            <td style="text-align: right;"><strong>₱' . number_format($grandTotal, 2) . '</strong></td>
                        </tr>
                    </tbody>
                </table>
                
                <div class="footer">
                    <p>Thank you for your business!</p>
                    <p style="font-size: 0.9rem;">This is an official receipt from UpholCare</p>
                </div>
            </div>
        </body>
        </html>';
        
        // Output as PDF or HTML
        header('Content-Type: text/html');
        header('Content-Disposition: attachment; filename="receipt_' . htmlspecialchars($booking['booking_number']) . '_' . date('Y-m-d') . '.html"');
        echo $receiptHtml;
        exit;
    }
    
    /**
     * Cancel Repair Reservation
     */
    public function cancelRepairReservation($repairId) {
        $userId = $this->currentUser()['id'];
        $db = Database::getInstance()->getConnection();
        
        // Verify the repair item belongs to the current user
        $sql = "SELECT id, status FROM repair_items WHERE id = ? AND customer_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$repairId, $userId]);
        $repairItem = $stmt->fetch();
        
        if (!$repairItem) {
            $_SESSION['error'] = 'Repair reservation not found.';
            $this->redirect('customer/bookings');
            return;
        }
        
        // Update status to cancelled
        $updateSql = "UPDATE repair_items SET status = 'cancelled', updated_at = NOW() WHERE id = ?";
        $updateStmt = $db->prepare($updateSql);
        $updateStmt->execute([$repairId]);
        
        $_SESSION['success'] = 'Repair reservation has been cancelled successfully.';
        $this->redirect('customer/bookings');
    }
    
    /**
     * Message Store - Stub method
     */
    public function messageStore($bookingId) {
        $_SESSION['info'] = 'Messaging feature is coming soon.';
        $this->redirect('customer/bookings');
    }
    
    /**
     * Upload Photos - Stub method
     */
    public function uploadPhotos($bookingId) {
        $_SESSION['info'] = 'Photo upload feature is coming soon.';
        $this->redirect('customer/bookings');
    }
    
    /**
     * Track Queue Position - Stub method
     */
    public function trackQueuePosition($bookingId) {
        $_SESSION['info'] = 'Queue tracking feature is coming soon.';
        $this->redirect('customer/bookings');
    }
    
    /**
     * Track Progress - Stub method
     */
    public function trackProgress($bookingId) {
        $_SESSION['info'] = 'Progress tracking feature is coming soon.';
        $this->redirect('customer/bookings');
    }
    
    /**
     * View Progress Photos - Stub method
     */
    public function viewProgressPhotos($bookingId) {
        $_SESSION['info'] = 'Progress photos feature is coming soon.';
        $this->redirect('customer/bookings');
    }
    
    /**
     * Arrange Pickup - Stub method
     */
    public function arrangePickup($bookingId) {
        $_SESSION['info'] = 'Pickup arrangement feature is coming soon.';
        $this->redirect('customer/bookings');
    }
    
    /**
     * Payment - Redirect to payments page
     */
    public function payment($bookingId) {
        $this->redirect('customer/payments?booking=' . $bookingId);
    }
    
    /**
     * Generate Pickup Code - Stub method
     */
    public function generatePickupCode($bookingId) {
        $_SESSION['info'] = 'Pickup code generation feature is coming soon.';
        $this->redirect('customer/bookings');
    }
    
    /**
     * Track Delivery - Stub method
     */
    public function trackDelivery($bookingId) {
        $_SESSION['info'] = 'Delivery tracking feature is coming soon.';
        $this->redirect('customer/bookings');
    }
    
    /**
     * Contact Rider - Stub method
     */
    public function contactRider($bookingId) {
        $_SESSION['info'] = 'Rider contact feature is coming soon.';
        $this->redirect('customer/bookings');
    }
    
    /**
     * Rate Service - Stub method
     */
    public function rateService($bookingId) {
        $_SESSION['info'] = 'Service rating feature is coming soon.';
        $this->redirect('customer/bookings');
    }
    
    /**
     * View Before/After Photos - Stub method
     */
    public function viewBeforeAfterPhotos($bookingId) {
        $_SESSION['info'] = 'Before/after photos feature is coming soon.';
        $this->redirect('customer/bookings');
    }
    
    /**
     * Book Again - Stub method
     */
    public function bookAgain($bookingId) {
        // Redirect to new booking with service pre-selected if possible
        $this->redirect('customer/newBooking?duplicate=' . $bookingId);
    }
    
    /**
     * Request Refund - Stub method
     */
    public function requestRefund($bookingId) {
        $_SESSION['info'] = 'Refund request feature is coming soon.';
        $this->redirect('customer/bookings');
    }
    
    /**
     * Profile
     */
    public function profile() {
        $userModel = $this->model('User');
        $userId = $this->currentUser()['id'];
        
        // Get user's profile images with fallback
        // Check if database columns exist, if not create them
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check and add cover_image column if it doesn't exist
            $checkCover = $db->query("SHOW COLUMNS FROM users LIKE 'cover_image'");
            if ($checkCover->rowCount() == 0) {
                $db->exec("ALTER TABLE users ADD COLUMN cover_image VARCHAR(255) DEFAULT NULL");
            }
            
            // Check and add profile_image column if it doesn't exist
            $checkProfile = $db->query("SHOW COLUMNS FROM users LIKE 'profile_image'");
            if ($checkProfile->rowCount() == 0) {
                $db->exec("ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) DEFAULT NULL");
            }
        } catch (Exception $e) {
            error_log('Error checking/adding columns: ' . $e->getMessage());
            // Continue anyway - columns might already exist
        }
        
        // Always refresh userDetails from database to get latest profile_image
        // This ensures profile image persists after refresh
        $userDetails = $userModel->getById($userId);
        
        // Set default images
        $coverImage = BASE_URL . 'assets/images/default-cover.svg';
        $profileImage = BASE_URL . 'assets/images/default-avatar.svg';
        
        if (!empty($userDetails['cover_image'])) {
            $coverImagePath = $userDetails['cover_image'];
            // Check if file exists (same as admin)
            if (file_exists(ROOT . DS . $coverImagePath)) {
                $coverImage = BASE_URL . $coverImagePath . '?t=' . time();
            }
        }
        
        if (!empty($userDetails['profile_image'])) {
            $profileImagePath = $userDetails['profile_image'];
            // Check if file exists (same as admin)
            if (file_exists(ROOT . DS . $profileImagePath)) {
                $profileImage = BASE_URL . $profileImagePath . '?t=' . time(); // Add cache busting
            }
        }
        
        // Update session with latest user data from database FIRST to ensure profile image persists
        // This must happen before merging to ensure the session has the latest data
        if ($userDetails) {
            $_SESSION['user'] = $userDetails;
        }
        
        // Merge userDetails with currentUser to ensure all fields are available
        $currentUser = $this->currentUser();
        $mergedUser = array_merge($currentUser ?? [], $userDetails ?? []);
        
        // Fetch business types for the registration form
        $businessTypes = $this->businessModel->getBusinessTypes();
        
        // Fetch current business profile if any
        $businessProfile = $this->businessModel->getByUserId($userId);
        
        // Fetch Business Statistics for Dashboard
        $businessBookings = $this->bookingModel->getBusinessBookings($userId);
        $businessStats = [
            'totalBookings' => count($businessBookings),
            'totalRevenue' => 0,
            'pendingOrders' => 0,
            'activeProjects' => 0
        ];

        foreach ($businessBookings as $booking) {
            if (in_array($booking['status'], ['completed', 'delivered_and_paid'])) {
                $businessStats['totalRevenue'] += $booking['total_amount'];
            }
            if ($booking['status'] === 'pending') {
                $businessStats['pendingOrders']++;
            }
            if (in_array($booking['status'], ['confirmed', 'in_progress', 'ready_for_pickup'])) {
                $businessStats['activeProjects']++;
            }
        }
        
        // Formatting revenue
        $businessStats['totalRevenueFormatted'] = '₱' . number_format($businessStats['totalRevenue'], 2);
        
        // Get recent business transactions (limit to 5)
        $recentBusinessTransactions = array_slice($businessBookings, 0, 5);

        // Fetch categories and stores for reservation modal
        $categories = $this->serviceModel->getCategories();
        $categories = array_filter($categories, function($category) {
            $categoryName = strtolower(trim($category['name'] ?? ''));
            return $categoryName !== 'vehicle repair' 
                && $categoryName !== 'furniture repair' 
                && $categoryName !== 'bedding repair';
        });
        $categories = array_values($categories);
        $stores = $this->storeModel->getAllActive();

        $data = [
            'title' => 'My Profile - ' . APP_NAME,
            'user' => $mergedUser,
            'userDetails' => $userDetails,
            'coverImage' => $coverImage,
            'profileImage' => $profileImage,
            'businessTypes' => $businessTypes,
            'businessProfile' => $businessProfile,
            'businessStats' => $businessStats,
            'recentBusinessTransactions' => $recentBusinessTransactions,
            'businessBookings' => $businessBookings,
            'categories' => $categories,
            'stores' => $stores
        ];
        
        $this->view('customer/profile', $data);
    }
    
    /**
     * Get Service Details (AJAX)
     */
    public function getServiceDetails($serviceId) {
        header('Content-Type: application/json');
        
        $service = $this->serviceModel->getServiceDetails($serviceId);
        
        if ($service) {
            echo json_encode([
                'success' => true,
                'data' => $service
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Service not found'
            ]);
        }
        exit;
    }
    
    /**
     * Get Service Types by Category (AJAX)
     */
    public function getServiceTypesByCategory() {
        header('Content-Type: application/json');
        
        $categoryId = $_GET['category_id'] ?? null;
        
        if ($categoryId) {
            $serviceTypes = $this->serviceModel->getServiceTypesByCategory($categoryId);
            echo json_encode([
                'success' => true,
                'data' => $serviceTypes
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Category ID is required'
            ]);
        }
        exit;
    }
    
    /**
     * Get Services by Category and Type (AJAX)
     */
    public function getServicesByCategoryAndType() {
        header('Content-Type: application/json');
        
        $categoryId = $_GET['category_id'] ?? null;
        $serviceType = $_GET['service_type'] ?? null;
        
        if ($categoryId && $serviceType) {
            $services = $this->serviceModel->getByCategoryAndType($categoryId, $serviceType);
            echo json_encode([
                'success' => true,
                'data' => $services
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Category ID and Service Type are required'
            ]);
        }
        exit;
    }
    
    /**
     * Show store locations page
     */
    public function storeLocations() {
        $data = [
            'title' => 'Find Nearest Store - ' . APP_NAME,
            'user' => $this->currentUser()
        ];
        
        $this->view('customer/store_locations', $data);
    }
    
    /**
     * Get all store locations (AJAX)
     */
    public function getStoreLocations() {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        try {
        $stores = $this->storeModel->getAllActive();
            
            // Ensure stores is an array
            if ($stores === false) {
                $stores = [];
            }
        
        echo json_encode([
            'success' => true,
                'data' => $stores ? $stores : []
            ]);
        } catch (Exception $e) {
            error_log('Error loading store locations: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load store locations. Please try again later.',
                'data' => []
        ]);
        }
        exit;
    }
    
    /**
     * Find nearest stores (AJAX)
     */
    public function findNearestStores() {
        header('Content-Type: application/json');
        
        try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
            exit;
        }
        
        $userLat = floatval($_POST['latitude'] ?? 0);
        $userLng = floatval($_POST['longitude'] ?? 0);
        
        if ($userLat == 0 || $userLng == 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid coordinates'
            ]);
            exit;
        }
        
        $stores = $this->storeModel->findNearestStores($userLat, $userLng, 10);
            
            // Ensure stores is an array
            if ($stores === false) {
                $stores = [];
            }
        
        echo json_encode([
            'success' => true,
                'data' => $stores ? $stores : []
            ]);
        } catch (Exception $e) {
            error_log('Error finding nearest stores: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to find nearest stores. Please try again later.',
                'data' => []
        ]);
        }
        exit;
    }
    
    /**
     * Get store details (AJAX)
     */
    public function getStoreDetails() {
        header('Content-Type: application/json');
        
        try {
        $storeId = intval($_GET['id'] ?? 0);
        
        if ($storeId == 0) {
            echo json_encode([
                'success' => false,
                'message' => 'Store ID is required'
            ]);
            exit;
        }
        
            $db = Database::getInstance()->getConnection();
            
            // Check if store_ratings table exists
            $tableExists = false;
            try {
                $checkTableStmt = $db->query("
                    SELECT COUNT(*) as table_exists 
                    FROM information_schema.tables 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'store_ratings'
                ");
                $tableCheck = $checkTableStmt->fetch();
                $tableExists = ($tableCheck && $tableCheck['table_exists'] > 0);
            } catch (Exception $e) {
                // Table doesn't exist or error checking
                $tableExists = false;
            }
            
            $store = null;
            
            if ($tableExists) {
                // Get store with rating count (table exists)
                try {
                    $stmt = $db->prepare("
                        SELECT sl.*, 
                               COUNT(sr.id) as total_ratings
                        FROM store_locations sl
                        LEFT JOIN store_ratings sr ON sl.id = sr.store_id AND sr.status = 'active'
                        WHERE sl.id = ? AND sl.status = 'active'
                        GROUP BY sl.id
                    ");
                    $stmt->execute([$storeId]);
                    $store = $stmt->fetch();
                } catch (Exception $e) {
                    // If join fails, fall back to simple query
                    error_log("Warning: Could not join store_ratings table: " . $e->getMessage());
                    $tableExists = false;
                }
            }
            
            // Fallback: Get store without rating count (table doesn't exist or join failed)
            if (!$store) {
                $stmt = $db->prepare("
                    SELECT * FROM store_locations 
                    WHERE id = ? AND status = 'active'
                ");
                $stmt->execute([$storeId]);
                $store = $stmt->fetch();
                
                // Add default total_ratings if not present
                if ($store && !isset($store['total_ratings'])) {
                    $store['total_ratings'] = 0;
                }
            }
        
        if ($store) {
            // Check if user is eligible to rate (has completed/paid bookings at this store)
            $canRate = false;
            $userId = $this->currentUser()['id'] ?? null;
            if ($userId) {
                // Check bookings table
                $checkBookingStmt = $db->prepare("
                    SELECT COUNT(*) as completed_count 
                    FROM bookings 
                    WHERE user_id = ? AND store_location_id = ? 
                    AND status IN ('completed', 'delivered_and_paid')
                ");
                $checkBookingStmt->execute([$userId, $storeId]);
                $bookingResult = $checkBookingStmt->fetch();
                if ($bookingResult && $bookingResult['completed_count'] > 0) {
                    $canRate = true;
                }
            }
            $store['can_rate'] = $canRate;

            echo json_encode([
                'success' => true,
                'data' => $store
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Store not found'
                ]);
            }
        } catch (Exception $e) {
            error_log('Error loading store details: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load store details: ' . $e->getMessage()
            ]);
        }
        exit;
    }
    
    /**
     * Get Available Colors (AJAX)
     * Returns colors available at a specific store
     */
    public function getAvailableColors() {
        header('Content-Type: application/json');
        
        $storeId = $_GET['store_id'] ?? null;
        $fabricType = $_GET['fabric_type'] ?? null;
        
        if (!$storeId) {
            echo json_encode(['success' => false, 'message' => 'Store ID is required', 'colors' => []]);
            exit;
        }

        try {
            $inventoryModel = $this->model('Inventory');
            $colors = $inventoryModel->getAvailableColors($storeId, $fabricType);
            
            echo json_encode([
                'success' => true,
                'colors' => $colors ? $colors : []
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            error_log('Error in getAvailableColors: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to load colors', 'colors' => []]);
        }
        exit;
    }

    /**
     * Get Store-Specific Services and Categories (AJAX)
     */
    public function getStoreServicesAjax() {
        header('Content-Type: application/json');
        
        $storeId = $_GET['store_id'] ?? null;
        
        if (!$storeId) {
            echo json_encode(['success' => false, 'message' => 'Store ID is required']);
            exit;
        }

        try {
            $serviceModel = $this->model('Service');
            $categories = $serviceModel->getCategoriesByStore($storeId);
            $services = $serviceModel->getServicesByStore($storeId);
            
            echo json_encode([
                'success' => true,
                'categories' => $categories,
                'services' => $services
            ]);
        } catch (Exception $e) {
            error_log('Error in getStoreServicesAjax: ' . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Failed to load store services']);
        }
        exit;
    }
    
    /**
     * Search stores (AJAX)
     */
    public function searchStores() {
        header('Content-Type: application/json');
        
        try {
        $searchTerm = trim($_GET['q'] ?? '');
        
        if (empty($searchTerm)) {
            echo json_encode([
                'success' => false,
                'message' => 'Search term is required'
            ]);
            exit;
        }
        
        $stores = $this->storeModel->searchStores($searchTerm);
            
            // Ensure stores is an array
            if ($stores === false) {
                $stores = [];
            }
        
        echo json_encode([
            'success' => true,
                'data' => $stores ? $stores : []
            ]);
        } catch (Exception $e) {
            error_log('Error searching stores: ' . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Failed to search stores. Please try again later.',
                'data' => []
        ]);
        }
        exit;
    }
    
    /**
     * Update customer profile
     */
    public function updateProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('customer/profile');
        }
        
        $userId = $this->currentUser()['id'];
        $userModel = $this->model('User');
        
        // Get form data - check for both 'name' and 'fullname' columns
        $updateData = [];
        
        // Check which column exists
        try {
            $db = Database::getInstance()->getConnection();
            $checkFullname = $db->query("SHOW COLUMNS FROM users LIKE 'fullname'");
            $hasFullname = $checkFullname->rowCount() > 0;
            
            if ($hasFullname) {
                $updateData['fullname'] = trim($_POST['fullname'] ?? '');
            } else {
                $updateData['name'] = trim($_POST['fullname'] ?? trim($_POST['name'] ?? ''));
            }
        } catch (Exception $e) {
            // Default to 'name' if check fails
            $updateData['name'] = trim($_POST['fullname'] ?? trim($_POST['name'] ?? ''));
        }
        
        $updateData['email'] = trim($_POST['email'] ?? '');
        $updateData['phone'] = trim($_POST['phone'] ?? '');
        
        // Handle address if provided
        if (isset($_POST['address'])) {
            $updateData['address'] = trim($_POST['address'] ?? '');
        }
        
        // Validate required fields
        if (empty($updateData['fullname'] ?? $updateData['name'] ?? '')) {
            $_SESSION['error'] = 'Full name is required';
            $this->redirect('customer/profile');
        }
        
        if (empty($updateData['email'])) {
            $_SESSION['error'] = 'Email is required';
            $this->redirect('customer/profile');
        }
        
        // Check if email is already taken by another user
        $existingUser = $userModel->findByEmail($updateData['email']);
        if ($existingUser && $existingUser['id'] != $userId) {
            $_SESSION['error'] = 'Email is already taken by another user';
            $this->redirect('customer/profile');
        }
        
        // Update user in database
        $result = $userModel->updateUser($userId, $updateData);
        
        if ($result) {
            // Update session with new data
            $updatedUser = $userModel->getById($userId);
            if ($updatedUser) {
                $_SESSION['user'] = $updatedUser;
            }
            
            $_SESSION['success'] = 'Profile updated successfully!';
        } else {
            $_SESSION['error'] = 'Failed to update profile. Please try again.';
        }
        
        $this->redirect('customer/profile');
    }
    
    /**
     * Update business profile / Registration
     */
    public function updateBusinessProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('customer/profile');
        }
        
        $user = $this->currentUser();
        $userId = $user['id'];
        
        // Stricter Role Verification: Only customers can register businesses
        // Check both session and database for security
        $userModel = $this->model('User');
        $dbUser = $userModel->getById($userId);
        
        if ($user['role'] !== 'customer' || !$dbUser || $dbUser['role'] !== 'customer') {
            $_SESSION['error'] = 'Access denied: Only customers can register businesses.';
            $this->redirect('customer/profile');
            return;
        }
        
        $businessData = [
            'user_id' => $userId,
            'business_name' => trim($_POST['business_name'] ?? ''),
            'business_type_id' => !empty($_POST['business_type_id']) ? $_POST['business_type_id'] : null,
            'business_address' => trim($_POST['business_address'] ?? ''),
            'status' => 'pending' // Always reset to pending on update/new registration
        ];
        
        // Validate required fields
        if (empty($businessData['business_name']) || empty($businessData['business_address'])) {
            $_SESSION['error'] = 'Business name and address are required.';
            $this->redirect('customer/profile');
        }
        
        // Handle Business Permit Upload
        if (isset($_FILES['permit_file']) && $_FILES['permit_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['permit_file'];
            $allowedTypes = ['application/pdf', 'image/jpeg', 'image/png'];
            
            if (in_array($file['type'], $allowedTypes)) {
                $uploadDir = ROOT . DS . 'assets' . DS . 'uploads' . DS . 'permits' . DS;
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                
                $fileName = 'permit_' . $userId . '_' . time() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
                if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
                    $businessData['permit_file'] = 'assets/uploads/permits/' . $fileName;
                }
            } else {
                $_SESSION['error'] = 'Invalid permit file format. Allowed: PDF, JPG, PNG.';
                $this->redirect('customer/profile');
            }
        }
        
        if ($this->businessModel->saveProfile($businessData)) {
            $_SESSION['success'] = 'Business registration submitted for review. status: Pending';
        } else {
            $_SESSION['error'] = 'Failed to submit business registration.';
        }
        
        $this->redirect('customer/profile');
    }
    
    /**
     * Get active business bookings
     */
    public function businessBookings() {
        // Redirect to profile where business bookings are integrated
        header("Location: " . BASE_URL . "customer/profile");
        exit();
    }

    /**
     * Get business booking history
     */
    public function businessHistory() {
        // Redirect to profile where the history modal is now integrated
        header("Location: " . BASE_URL . "customer/profile");
        exit();
    }
    
    /**
     * Get business statistics (AJAX)
     */
    public function getBusinessStats() {
        header('Content-Type: application/json');
        
        $userId = $this->currentUser()['id'];
        
        // Get reliable business bookings
        $businessBookings = $this->bookingModel->getBusinessBookings($userId);
        
        $totalBookings = count($businessBookings);
        
        // Calculate total revenue from COMPLETED bookings
        $totalRevenue = array_reduce($businessBookings, function($carry, $booking) {
            if (in_array($booking['status'], ['completed', 'delivered_and_paid'])) {
                return $carry + $booking['total_amount'];
            }
            return $carry;
        }, 0);

        $pendingOrders = count(array_filter($businessBookings, function($booking) {
            return $booking['status'] === 'pending';
        }));

        $activeProjects = count(array_filter($businessBookings, function($booking) {
            return in_array($booking['status'], ['confirmed', 'in_progress', 'ready_for_pickup']);
        }));
        
        echo json_encode([
            'success' => true,
            'data' => [
                'totalBookings' => $totalBookings,
                'totalRevenue' => '₱' . number_format($totalRevenue, 2),
                'pendingOrders' => $pendingOrders,
                'activeProjects' => $activeProjects
            ]
        ]);
        exit;
    }
    
    /**
     * New Business Reservation Form
     */
    public function newBusinessReservation() {
        // Redirect to profile where the reservation modal is now integrated
        header("Location: " . BASE_URL . "customer/profile");
        exit();
    }
    
    /**
     * Process Business Reservation
     */
    public function processBusinessReservation() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate required fields
            $requiredFields = [
                'business_name', 'business_type', 'contact_person', 'business_phone', 
                'business_address', 'service_category', 'service_type', 'project_name', 
                'urgency_level', 'service_description'
            ];
            
            $errors = [];
            foreach ($requiredFields as $field) {
                if (empty($_POST[$field])) {
                    $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                }
            }
            
            if (!empty($errors)) {
                $_SESSION['error'] = implode(' ', $errors);
                $this->redirect('customer/newBusinessReservation');
                return;
            }

            // Enhanced Validation: Ensure the user has an APPROVED business profile and is a CUSTOMER
            $user = $this->currentUser();
            $userId = $user['id'];
            
            // Verify role from database
            $userModel = $this->model('User');
            $dbUser = $userModel->getById($userId);
            
            if ($user['role'] !== 'customer' || !$dbUser || $dbUser['role'] !== 'customer') {
                $_SESSION['error'] = 'Access denied: Only customers can make business reservations.';
                $this->redirect('customer/profile');
                return;
            }
            
            $businessProfile = $this->businessModel->getByUserId($userId);
            if (!$businessProfile || $businessProfile['status'] !== 'approved') {
                $_SESSION['error'] = 'Your business account must be approved by the Super Admin before you can make reservations.';
                $this->redirect('customer/profile');
                return;
            }
            $customerBusinessId = $businessProfile['id'];
            
            // Prepare reservation data
            $reservationData = [
                'user_id' => $userId,
                'customer_business_id' => $customerBusinessId,
                'booking_number_id' => null, // Will be assigned by admin when accepting the reservation
                'booking_type' => 'business_reservation',
                'status' => 'admin_review',
                'business_name' => $_POST['business_name'],
                'business_type' => $_POST['business_type'],
                'contact_person' => $_POST['contact_person'],
                'business_phone' => $_POST['business_phone'],
                'business_address' => $_POST['business_address'],
                'service_category' => $_POST['service_category'],
                'service_type' => $_POST['service_type'],
                'project_name' => $_POST['project_name'],
                'urgency_level' => $_POST['urgency_level'],
                'service_description' => $_POST['service_description'],
                'preferred_date' => !empty($_POST['preferred_date']) ? $_POST['preferred_date'] : null,
                'estimated_duration' => !empty($_POST['estimated_duration']) ? $_POST['estimated_duration'] : null,
                'budget_range' => !empty($_POST['budget_range']) ? $_POST['budget_range'] : null,
                'store_location_id' => !empty($_POST['store_location']) ? $_POST['store_location'] : null,
                'special_requirements' => !empty($_POST['special_requirements']) ? $_POST['special_requirements'] : null,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Create reservation
            $reservationId = $this->bookingModel->createBooking($reservationData);
            
            if ($reservationId) {
                $_SESSION['success'] = 'Business reservation submitted successfully! Admin will review and contact you within 24-48 hours.';
                $this->redirect('customer/businessHistory');
            } else {
                $_SESSION['error'] = 'Failed to submit business reservation. Please try again.';
                $this->redirect('customer/newBusinessReservation');
            }
        } else {
            $this->redirect('customer/newBusinessReservation');
        }
    }
    
    /**
     * New Repair Reservation Form - Unified form for all bookings
     */
    public function newRepairReservation() {
        $services = $this->serviceModel->getAllActive();
        $categories = $this->serviceModel->getCategories();
        
        // Filter out "Vehicle Repair", "Furniture Repair", and "Bedding Repair" categories
        $categories = array_filter($categories, function($category) {
            $categoryName = strtolower(trim($category['name'] ?? ''));
            return $categoryName !== 'vehicle repair' 
                && $categoryName !== 'furniture repair' 
                && $categoryName !== 'bedding repair';
        });
        // Re-index array after filtering
        $categories = array_values($categories);
        
        // Get user's address from database
        $userId = $this->currentUser()['id'];
        $userModel = $this->model('User');
        $userDetails = $userModel->getById($userId);
        $userAddress = $userDetails['address'] ?? '';
        
        $data = [
            'title' => 'Repair Reservation - ' . APP_NAME,
            'user' => $this->currentUser(),
            'services' => $services,
            'categories' => $categories,
            'userAddress' => $userAddress
        ];
        
        $this->view('customer/new_repair_reservation', $data);
    }

    /**
     * New Repair Reservation Partial for Modal
     */
    public function newRepairReservationPartial() {
        $services = $this->serviceModel->getAllActive();
        $categories = $this->serviceModel->getCategories();
        
        // Filter out "Vehicle Repair", "Furniture Repair", and "Bedding Repair" categories
        $categories = array_filter($categories, function($category) {
            $categoryName = strtolower(trim($category['name'] ?? ''));
            return $categoryName !== 'vehicle repair' 
                && $categoryName !== 'furniture repair' 
                && $categoryName !== 'bedding repair';
        });
        // Re-index array after filtering
        $categories = array_values($categories);
        
        // Get user's address from database
        $userId = $this->currentUser()['id'];
        $userModel = $this->model('User');
        $userDetails = $userModel->getById($userId);
        $userAddress = $userDetails['address'] ?? '';
        
        // Get pre-selected service if service_id is provided
        $preSelectedService = null;
        $preSelectedCategoryId = null;
        $preSelectedServiceType = null;
        
        if (isset($_GET['service_id']) && !empty($_GET['service_id'])) {
            $serviceId = $_GET['service_id'];
            
            // Fetch service details from database
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("SELECT id, service_name, category_id, service_type FROM services WHERE id = ?");
            $stmt->execute([$serviceId]);
            $preSelectedService = $stmt->fetch();
            
            if ($preSelectedService) {
                $preSelectedCategoryId = $preSelectedService['category_id'];
                $preSelectedServiceType = $preSelectedService['service_type'];
            }
        }

        // Get pre-selected color if provided
        $preSelectedColorId = $_GET['color_id'] ?? null;
        $preSelectedColorType = $_GET['color_type'] ?? null;
        $preSelectedColor = null;

        if ($preSelectedColorId) {
            $inventoryModel = $this->model('Inventory');
            $preSelectedColor = $inventoryModel->getColorById($preSelectedColorId);
        }
        
        $data = [
            'services' => $services,
            'categories' => $categories,
            'userAddress' => $userAddress,
            'preSelectedCategoryId' => $preSelectedCategoryId,
            'preSelectedServiceType' => $preSelectedServiceType,
            'preSelectedService' => $preSelectedService,
            'preSelectedColorId' => $preSelectedColorId,
            'preSelectedColorType' => $preSelectedColorType,
            'preSelectedColor' => $preSelectedColor
        ];
        
        $this->view('customer/repair_reservation_partial', $data);
        exit;
    }
    
    /**
     * Process Repair Reservation - Redirected to unified booking process
     */
    public function processRepairReservation() {
        // Redirect to the unified booking process
        $this->processBooking();
    }
    
    /**
     * Send booking confirmation email to customer with queue number
     */
    private function sendBookingConfirmationEmail($bookingId, $customerId, $queueNumber, $queuePosition, $customersAhead) {
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get customer details
            $stmt = $db->prepare("SELECT fullname, email FROM users WHERE id = ?");
            $stmt->execute([$customerId]);
            $customer = $stmt->fetch();
            
            if (!$customer || empty($customer['email'])) {
                return false;
            }
            
            // Get booking details
            $booking = $this->bookingModel->getBookingDetails($bookingId, $customerId);
            
            if (!$booking) {
                return false;
            }
            
            // Send email notification
            require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
            $notificationService = new NotificationService();
            
            // Send booking confirmation email (queue number may be null if not assigned yet)
            $notificationService->sendBookingConfirmation(
                $customer['email'],
                $customer['fullname'],
                $queueNumber, // May be null - admin will assign later
                $booking
            );
            
            // Only send queue number assignment email if queue number is provided
            if ($queueNumber) {
                $notificationService->sendBookingNumberAssignmentEmail(
                    $customer['email'],
                    $customer['fullname'],
                    $queueNumber,
                    $queuePosition,
                    $customersAhead
                );
            }
            
            return true;
        } catch (Exception $e) {
            error_log("Error sending booking confirmation email: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Notify admin about new booking
     */
    private function notifyAdminAboutNewBooking($bookingId, $customerId) {
        $db = Database::getInstance()->getConnection();
        
        // Get customer details
        $userModel = $this->model('User');
        $customer = $userModel->getById($customerId);
        
        // Get booking details
        $booking = $this->bookingModel->getBookingDetails($bookingId, $customerId);
        
        if (!$booking || !$customer) {
            return false;
        }
        
        // Get all admin users
        $stmt = $db->prepare("SELECT id FROM users WHERE role = 'admin' AND status = 'active'");
        $stmt->execute();
        $admins = $stmt->fetchAll();
        
        // Create notifications for all admins
        foreach ($admins as $admin) {
            $stmt = $db->prepare("
                INSERT INTO notifications 
                (user_id, title, message, type, is_read, created_at) 
                VALUES (?, ?, ?, 'info', 0, NOW())
            ");
            
            $title = "New Booking Request";
            $itemDescription = !empty($booking['item_description']) ? substr($booking['item_description'], 0, 50) . '...' : 'No description';
            $message = "Customer {$customer['fullname']} has submitted a new booking request. Please review and assign a booking number.";
            
            $stmt->execute([
                $admin['id'],
                $title,
                $message
            ]);
        }
        
        return true;
    }
    
    /**
     * Notify admin about new repair reservation
     */
    private function notifyAdminAboutRepairReservation($repairItemId, $customerId) {
        $db = Database::getInstance()->getConnection();
        
        // Get customer details
        $userModel = $this->model('User');
        $customer = $userModel->getById($customerId);
        
        // Get repair item details
        $stmt = $db->prepare("SELECT * FROM repair_items WHERE id = ?");
        $stmt->execute([$repairItemId]);
        $repairItem = $stmt->fetch();
        
        if (!$repairItem || !$customer) {
            return false;
        }
        
        // Get all admin users
        $stmt = $db->prepare("SELECT id FROM users WHERE role = 'admin' AND status = 'active'");
        $stmt->execute();
        $admins = $stmt->fetchAll();
        
        // Create notifications for all admins
        foreach ($admins as $admin) {
            $stmt = $db->prepare("
                INSERT INTO notifications 
                (user_id, title, message, type, is_read, created_at) 
                VALUES (?, ?, ?, 'info', 0, NOW())
            ");
            
            $title = "New Repair Reservation";
            $message = "Customer {$customer['fullname']} has submitted a new repair reservation: {$repairItem['item_name']}. Please review and assign a booking number.";
            
            $stmt->execute([
                $admin['id'],
                $title,
                $message
            ]);
        }
        
        return true;
    }
    
    /**
     * View Repair Reservation Details
     */
    public function viewRepairReservation($repairItemId) {
        $userId = $this->currentUser()['id'];
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT ri.*, 
                CONCAT('Booking #', ri.id) as booking_number,
                u.fullname as customer_name, u.email, u.phone
                FROM repair_items ri
                LEFT JOIN users u ON ri.customer_id = u.id
                WHERE ri.id = ? AND ri.customer_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$repairItemId, $userId]);
        $repairItem = $stmt->fetch();
        
        if (!$repairItem) {
            $_SESSION['error'] = 'Repair reservation not found.';
            $this->redirect('customer/bookings');
            return;
        }
        
        $data = [
            'title' => 'Repair Reservation Details - ' . APP_NAME,
            'user' => $this->currentUser(),
            'repairItem' => $repairItem
        ];
        
        $this->view('customer/view_repair_reservation', $data);
    }
    
    /**
     * Edit Repair Reservation
     * Redirects to new repair reservation form with edit mode
     */
    public function editRepairReservation($repairItemId) {
        $userId = $this->currentUser()['id'];
        $db = Database::getInstance()->getConnection();
        
        // Verify the repair item belongs to the current user
        $sql = "SELECT id FROM repair_items WHERE id = ? AND customer_id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$repairItemId, $userId]);
        $repairItem = $stmt->fetch();
        
        if (!$repairItem) {
            $_SESSION['error'] = 'Repair reservation not found or you do not have permission to edit it.';
            $this->redirect('customer/bookings');
            return;
        }
        
        // Redirect to new repair reservation form with edit parameter
        $this->redirect('customer/newRepairReservation?edit=' . $repairItemId);
    }
    
    /**
     * Reschedule Repair Reservation
     * Shows reschedule form or redirects to reschedule page
     */
    public function rescheduleRepair($repairItemId) {
        $userId = $this->currentUser()['id'];
        $db = Database::getInstance()->getConnection();
        
        // Verify the repair item belongs to the current user
        $sql = "SELECT ri.*, 
                CONCAT('Booking #', ri.id) as booking_number,
                u.fullname as customer_name, u.email, u.phone
                FROM repair_items ri
                LEFT JOIN users u ON ri.customer_id = u.id
                WHERE ri.id = ? AND ri.customer_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$repairItemId, $userId]);
        $repairItem = $stmt->fetch();
        
        if (!$repairItem) {
            $_SESSION['error'] = 'Repair reservation not found or you do not have permission to reschedule it.';
            $this->redirect('customer/bookings');
            return;
        }
        
        // Check if status allows rescheduling
        $allowedStatuses = ['pending', 'submitted', 'for_pickup'];
        if (!in_array($repairItem['status'], $allowedStatuses)) {
            $_SESSION['error'] = 'This reservation cannot be rescheduled at this time.';
            $this->redirect('customer/bookings');
            return;
        }
        
        // Redirect to new repair reservation form with reschedule parameter
        $this->redirect('customer/newRepairReservation?reschedule=' . $repairItemId);
    }
    
    /**
     * Submit Store Rating
     */
    public function submitStoreRating() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode([
                'success' => false,
                'message' => 'Invalid request method'
            ]);
            return;
        }
        
        // Check if store_ratings table exists, create it if it doesn't
        try {
            $db = Database::getInstance()->getConnection();
            $checkTableStmt = $db->query("
                SELECT COUNT(*) as table_exists 
                FROM information_schema.tables 
                WHERE table_schema = DATABASE() 
                AND table_name = 'store_ratings'
            ");
            $tableCheck = $checkTableStmt->fetch();
            $tableExists = ($tableCheck && $tableCheck['table_exists'] > 0);
            
            if (!$tableExists) {
                // Automatically create the table
                try {
                    $this->createStoreRatingsTable($db);
                    error_log("INFO: store_ratings table created automatically");
                } catch (Exception $createError) {
                    error_log("ERROR: Failed to create store_ratings table: " . $createError->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => 'Rating system setup failed. Please run: http://localhost/UphoCare/database/setup_ratings_table_now.php - Error: ' . $createError->getMessage()
                    ]);
                    return;
                }
            }
        } catch (Exception $e) {
            error_log("ERROR: Error checking/creating store_ratings table: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'message' => 'Error checking database: ' . $e->getMessage()
            ]);
            return;
        }
        
        $userId = $this->currentUser()['id'];
        $storeId = $_POST['store_id'] ?? null;
        $rating = $_POST['rating'] ?? null;
        $reviewText = $_POST['review_text'] ?? '';
        
        if (!$storeId || !$rating) {
            echo json_encode([
                'success' => false,
                'message' => 'Store ID and rating are required'
            ]);
            return;
        }
        
        $rating = floatval($rating);
        if ($rating < 1 || $rating > 5) {
            echo json_encode([
                'success' => false,
                'message' => 'Rating must be between 1 and 5'
            ]);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if store exists
            $storeStmt = $db->prepare("SELECT id FROM store_locations WHERE id = ? AND status = 'active'");
            $storeStmt->execute([$storeId]);
            if (!$storeStmt->fetch()) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Store not found'
                ]);
                return;
            }

            // Verify eligibility: Must have at least one completed/paid booking at this store
            $checkEligibilityStmt = $db->prepare("
                SELECT COUNT(*) as completed_count 
                FROM bookings 
                WHERE user_id = ? AND store_location_id = ? 
                AND status IN ('completed', 'delivered_and_paid')
            ");
            $checkEligibilityStmt->execute([$userId, $storeId]);
            $eligibilityResult = $checkEligibilityStmt->fetch();
            
            if (!$eligibilityResult || $eligibilityResult['completed_count'] == 0) {
                echo json_encode([
                    'success' => false,
                    'message' => 'You can only rate stores where you have completed and paid reservations.'
                ]);
                return;
            }
            
            // Check if user has already rated this store
            try {
                $checkStmt = $db->prepare("SELECT id FROM store_ratings WHERE store_id = ? AND user_id = ?");
                $checkStmt->execute([$storeId, $userId]);
                $existingRating = $checkStmt->fetch();
            } catch (Exception $e) {
                // Table might not exist yet, create it and try again
                error_log("Warning: Error checking existing rating, table might not exist: " . $e->getMessage());
                try {
                    $this->createStoreRatingsTable($db);
                    // Try again after creating table
                    $checkStmt = $db->prepare("SELECT id FROM store_ratings WHERE store_id = ? AND user_id = ?");
                    $checkStmt->execute([$storeId, $userId]);
                    $existingRating = $checkStmt->fetch();
                } catch (Exception $createError) {
                    error_log("ERROR: Failed to create store_ratings table: " . $createError->getMessage());
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error setting up rating system: ' . $createError->getMessage()
                    ]);
                    return;
                }
            }
            
            if ($existingRating) {
                // Update existing rating
                $updateStmt = $db->prepare("
                    UPDATE store_ratings 
                    SET rating = ?, review_text = ?, updated_at = NOW()
                    WHERE id = ?
                ");
                $updateStmt->execute([$rating, $reviewText, $existingRating['id']]);
            } else {
                // Insert new rating
                try {
                    $insertStmt = $db->prepare("
                        INSERT INTO store_ratings (store_id, user_id, rating, review_text, status, created_at, updated_at)
                        VALUES (?, ?, ?, ?, 'active', NOW(), NOW())
                    ");
                    $insertStmt->execute([$storeId, $userId, $rating, $reviewText]);
                } catch (Exception $insertError) {
                    // If insert fails due to table structure, try to recreate table
                    error_log("Warning: Insert failed, might be table structure issue: " . $insertError->getMessage());
                    if (strpos($insertError->getMessage(), "doesn't exist") !== false || 
                        strpos($insertError->getMessage(), "Unknown column") !== false) {
                        try {
                            // Drop and recreate table
                            $db->exec("DROP TABLE IF EXISTS store_ratings");
                            $this->createStoreRatingsTable($db);
                            // Try insert again
                            $insertStmt = $db->prepare("
                                INSERT INTO store_ratings (store_id, user_id, rating, review_text, status, created_at, updated_at)
                                VALUES (?, ?, ?, ?, 'active', NOW(), NOW())
                            ");
                            $insertStmt->execute([$storeId, $userId, $rating, $reviewText]);
                        } catch (Exception $retryError) {
                            throw $retryError;
                        }
                    } else {
                        throw $insertError;
                    }
                }
            }
            
            // Update store's average rating (trigger will handle this automatically, but we can also do it manually)
            try {
                $avgStmt = $db->prepare("
                    UPDATE store_locations 
                    SET rating = COALESCE((
                        SELECT AVG(rating)
                        FROM store_ratings
                        WHERE store_id = ? AND status = 'active'
                    ), 0.00),
                    updated_at = NOW()
                    WHERE id = ?
                ");
                $avgStmt->execute([$storeId, $storeId]);
            } catch (Exception $e) {
                error_log("Warning: Could not update store rating average: " . $e->getMessage());
                // Continue anyway - rating was saved
            }
            
            echo json_encode([
                'success' => true,
                'message' => 'Rating submitted successfully'
            ]);
            
        } catch (Exception $e) {
            error_log("Error submitting store rating: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            echo json_encode([
                'success' => false,
                'message' => 'Error submitting rating: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get User's Rating for a Store
     */
    public function getUserRating() {
        header('Content-Type: application/json');
        
        $userId = $this->currentUser()['id'];
        $storeId = $_GET['store_id'] ?? null;
        
        if (!$storeId) {
            echo json_encode([
                'success' => false,
                'message' => 'Store ID is required'
            ]);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if store_ratings table exists
            try {
                $checkTableStmt = $db->query("
                    SELECT COUNT(*) as table_exists 
                    FROM information_schema.tables 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'store_ratings'
                ");
                $tableCheck = $checkTableStmt->fetch();
                $tableExists = ($tableCheck && $tableCheck['table_exists'] > 0);
                
                if (!$tableExists) {
                    // Table doesn't exist, return null
                    echo json_encode([
                        'success' => true,
                        'data' => null
                    ]);
                    return;
                }
            } catch (Exception $e) {
                // Error checking table, assume it doesn't exist
                echo json_encode([
                    'success' => true,
                    'data' => null
                ]);
                return;
            }
            
            $stmt = $db->prepare("
                SELECT id, store_id, user_id, rating, review_text, created_at, updated_at
                FROM store_ratings
                WHERE store_id = ? AND user_id = ? AND status = 'active'
            ");
            $stmt->execute([$storeId, $userId]);
            $rating = $stmt->fetch();
            
            if ($rating) {
                echo json_encode([
                    'success' => true,
                    'data' => $rating
                ]);
            } else {
                echo json_encode([
                    'success' => true,
                    'data' => null
                ]);
            }
            
        } catch (Exception $e) {
            error_log("Error getting user rating: " . $e->getMessage());
            // Return null instead of error to allow rating form to show
            echo json_encode([
                'success' => true,
                'data' => null
            ]);
        }
    }
    
    /**
     * Get Store Reviews
     */
    public function getStoreReviews() {
        header('Content-Type: application/json');
        
        $storeId = $_GET['store_id'] ?? null;
        
        if (!$storeId) {
            echo json_encode([
                'success' => false,
                'message' => 'Store ID is required'
            ]);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if store_ratings table exists, create if it doesn't
            try {
                $checkTableStmt = $db->query("
                    SELECT COUNT(*) as table_exists 
                    FROM information_schema.tables 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'store_ratings'
                ");
                $tableCheck = $checkTableStmt->fetch();
                $tableExists = ($tableCheck && $tableCheck['table_exists'] > 0);
                
                if (!$tableExists) {
                    // Automatically create the table
                    try {
                        $this->createStoreRatingsTable($db);
                    } catch (Exception $createError) {
                        error_log("Warning: Could not create store_ratings table: " . $createError->getMessage());
                        // Return empty array - table will be created on next rating submission
                        echo json_encode([
                            'success' => true,
                            'data' => []
                        ]);
                        return;
                    }
                }
            } catch (Exception $e) {
                // Error checking table, return empty array
                echo json_encode([
                    'success' => true,
                    'data' => []
                ]);
                return;
            }
            
            $stmt = $db->prepare("
                SELECT sr.id, sr.rating, sr.review_text, sr.created_at,
                       u.fullname as customer_name
                FROM store_ratings sr
                LEFT JOIN users u ON sr.user_id = u.id
                WHERE sr.store_id = ? AND sr.status = 'active'
                ORDER BY sr.created_at DESC
                LIMIT 10
            ");
            $stmt->execute([$storeId]);
            $reviews = $stmt->fetchAll();
            
            echo json_encode([
                'success' => true,
                'data' => $reviews
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting store reviews: " . $e->getMessage());
            // Return empty array instead of error to allow page to load
            echo json_encode([
                'success' => true,
                'data' => []
            ]);
        }
    }
    
    /**
     * Get Store Inventory/Services with real booking statistics
     */
    public function getStoreInventory() {
        header('Content-Type: application/json');
        
        $storeId = $_GET['store_id'] ?? null;
        
        if (!$storeId) {
            echo json_encode([
                'success' => false,
                'message' => 'Store ID is required'
            ]);
            return;
        }
        
        try {
            // Use Inventory model to get services with real booking statistics
            $inventoryModel = new Inventory();
            $services = $inventoryModel->getServiceStatistics($storeId);
            
            echo json_encode([
                'success' => true,
                'data' => $services
            ]);
            
        } catch (Exception $e) {
            error_log("Error getting store services: " . $e->getMessage());
            // Return empty array instead of error to allow page to load
            echo json_encode([
                'success' => true,
                'data' => []
            ]);
        }
    }
    
    /**
     * Get Repair Reservation Details (AJAX)
     */
    public function getRepairReservationDetails($repairItemId) {
        header('Content-Type: application/json');
        
        $userId = $this->currentUser()['id'];
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT ri.*, 
                CONCAT('Booking #', ri.id) as booking_number,
                u.fullname as customer_name, u.email, u.phone
                FROM repair_items ri
                LEFT JOIN users u ON ri.customer_id = u.id
                WHERE ri.id = ? AND ri.customer_id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$repairItemId, $userId]);
        $repairItem = $stmt->fetch();
        
        if ($repairItem) {
            echo json_encode([
                'success' => true,
                'data' => $repairItem
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Repair reservation not found'
            ]);
        }
        exit;
    }
    
    /**
     * Get Notifications (AJAX)
     */
    public function getNotifications() {
        header('Content-Type: application/json');
        
        if (!$this->isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            $userId = $this->currentUser()['id'];
            
            // Get unread count
            $countStmt = $db->prepare("SELECT COUNT(*) as unread_count FROM notifications WHERE user_id = ? AND is_read = 0");
            $countStmt->execute([$userId]);
            $unreadCount = $countStmt->fetch()['unread_count'];
            
            // Get recent notifications (last 10) - include related_id and related_type for clickable links
            // Check if related_id column exists
            $checkColumns = $db->query("SHOW COLUMNS FROM notifications LIKE 'related_id'");
            $hasRelatedId = $checkColumns->fetch();
            
            if ($hasRelatedId) {
                $notifStmt = $db->prepare("
                    SELECT id, title, message, type, is_read, created_at, related_id, related_type 
                    FROM notifications 
                    WHERE user_id = ? 
                    ORDER BY created_at DESC 
                    LIMIT 10
                ");
            } else {
                $notifStmt = $db->prepare("
                    SELECT id, title, message, type, is_read, created_at 
                    FROM notifications 
                    WHERE user_id = ? 
                    ORDER BY created_at DESC 
                    LIMIT 10
                ");
            }
            $notifStmt->execute([$userId]);
            $notifications = $notifStmt->fetchAll();
            
            // Format notifications for display
            $formattedNotifications = [];
            foreach ($notifications as $notif) {
                $notification = [
                    'id' => $notif['id'],
                    'title' => $notif['title'],
                    'message' => $notif['message'],
                    'type' => $notif['type'],
                    'is_read' => (bool)$notif['is_read'],
                    'created_at' => $notif['created_at'],
                    'time_ago' => $this->timeAgo($notif['created_at'])
                ];
                
                // Add related_id and related_type if available
                if ($hasRelatedId && isset($notif['related_id'])) {
                    $notification['related_id'] = $notif['related_id'];
                    $notification['related_type'] = $notif['related_type'] ?? null;
                }
                
                $formattedNotifications[] = $notification;
            }
            
            echo json_encode([
                'success' => true,
                'unread_count' => (int)$unreadCount,
                'notifications' => $formattedNotifications
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error fetching notifications: ' . $e->getMessage()]);
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
     * Submit Store Compliance Report (AJAX)
     */
    public function submitStoreComplianceReport() {
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
        
        $storeId = intval($_POST['store_id'] ?? 0);
        $issueTypesJson = $_POST['issue_types'] ?? '[]';
        $description = trim($_POST['description'] ?? '');
        $customerId = $this->currentUser()['id'];
        
        if ($storeId == 0) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Store ID is required']);
            exit;
        }
        
        if (empty($description)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Description is required']);
            exit;
        }
        
        // Parse issue types
        $issueTypes = json_decode($issueTypesJson, true);
        if (!is_array($issueTypes) || empty($issueTypes)) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Please select at least one issue type']);
            exit;
        }
        
        // Determine report type (use first selected type, or 'other' if multiple)
        $reportType = count($issueTypes) === 1 ? $issueTypes[0] : 'other';
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Check if table exists, create if not
            $tableExists = false;
            try {
                $checkTableStmt = $db->query("
                    SELECT COUNT(*) as table_exists 
                    FROM information_schema.tables 
                    WHERE table_schema = DATABASE() 
                    AND table_name = 'store_compliance_reports'
                ");
                $tableCheck = $checkTableStmt->fetch();
                $tableExists = ($tableCheck && $tableCheck['table_exists'] > 0);
            } catch (Exception $e) {
                $tableExists = false;
            }
            
            if (!$tableExists) {
                // Table doesn't exist, return error with instructions
                echo json_encode([
                    'success' => false,
                    'message' => 'Compliance reports table not found. Please run the database migration: database/create_store_compliance_reports_table.php'
                ]);
                exit;
            }
            
            // Insert report
            $stmt = $db->prepare("
                INSERT INTO store_compliance_reports 
                (store_id, customer_id, report_type, issue_types, description, status, created_at) 
                VALUES (?, ?, ?, ?, ?, 'pending', NOW())
            ");
            
            $result = $stmt->execute([
                $storeId,
                $customerId,
                $reportType,
                json_encode($issueTypes),
                $description
            ]);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Report submitted successfully. The administrator will review it shortly.'
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to submit report']);
            }
        } catch (Exception $e) {
            error_log('Error submitting compliance report: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error submitting report: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Upload Profile Images
     */
    public function uploadProfileImages() {
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $userId = $this->currentUser()['id'];
        $uploadDir = ROOT . DS . 'assets' . DS . 'uploads' . DS . 'profiles' . DS;
        
        // Create upload directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            if (!mkdir($uploadDir, 0755, true)) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create upload directory']);
                exit;
            }
        }
        
        $errors = [];
        $uploadedFiles = [];
        
        // Handle cover image upload
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $coverImage = $_FILES['cover_image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (in_array($coverImage['type'], $allowedTypes)) {
                $coverFileName = 'cover_' . $userId . '_' . time() . '.' . pathinfo($coverImage['name'], PATHINFO_EXTENSION);
                $coverPath = $uploadDir . $coverFileName;
                
                if (move_uploaded_file($coverImage['tmp_name'], $coverPath)) {
                    $uploadedFiles['cover_image'] = 'assets/uploads/profiles/' . $coverFileName;
                } else {
                    $errors[] = 'Failed to upload cover image';
                }
            } else {
                $errors[] = 'Invalid cover image format. Allowed: JPG, PNG, GIF, WEBP';
            }
        }
        
        // Handle profile image upload
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $profileImage = $_FILES['profile_image'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            
            if (in_array($profileImage['type'], $allowedTypes)) {
                $profileFileName = 'profile_' . $userId . '_' . time() . '.' . pathinfo($profileImage['name'], PATHINFO_EXTENSION);
                $profilePath = $uploadDir . $profileFileName;
                
                if (move_uploaded_file($profileImage['tmp_name'], $profilePath)) {
                    $uploadedFiles['profile_image'] = 'assets/uploads/profiles/' . $profileFileName;
                } else {
                    $errors[] = 'Failed to upload profile image';
                }
            } else {
                $errors[] = 'Invalid profile image format. Allowed: JPG, PNG, GIF, WEBP';
            }
        }
        
        if (empty($errors) && !empty($uploadedFiles)) {
            try {
                $userModel = $this->model('User');
                $updateData = [];
                
                if (isset($uploadedFiles['cover_image'])) {
                    $updateData['cover_image'] = $uploadedFiles['cover_image'];
                }
                if (isset($uploadedFiles['profile_image'])) {
                    $updateData['profile_image'] = $uploadedFiles['profile_image'];
                }
                
                if (!empty($updateData)) {
                    $result = $userModel->updateUser($userId, $updateData);
                    
                    if ($result) {
                        // Update session (same as admin)
                        $updatedUser = $userModel->getById($userId);
                        if ($updatedUser) {
                            $_SESSION['user'] = $updatedUser;
                        }
                        
                        echo json_encode([
                            'success' => true, 
                            'message' => 'Image uploaded successfully',
                            'files' => $uploadedFiles
                        ]);
                    } else {
                        http_response_code(500);
                        echo json_encode(['success' => false, 'message' => 'Failed to update user profile']);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'No files to upload']);
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => false, 
                    'message' => 'Error: ' . $e->getMessage()
                ]);
            }
        } else {
            http_response_code(400);
            echo json_encode([
                'success' => false, 
                'message' => !empty($errors) ? implode(', ', $errors) : 'No files uploaded'
            ]);
        }
        exit;
    }
    
    /**
     * Get Booking Progress History for Customer (AJAX)
     */
    public function getBookingProgress($bookingId) {
        header('Content-Type: application/json');
        
        $userId = $this->currentUser()['id'];
        
        try {
            // Verify booking belongs to customer
            $booking = $this->bookingModel->getBookingDetails($bookingId, $userId);
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                return;
            }
            
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
     * Get Preview Receipt (Customer Action)
     * Returns preview receipt data for customer to review before approval
     */
    public function getPreviewReceipt($bookingId) {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $userId = $this->currentUser()['id'];
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get booking details - verify it belongs to the customer
            // Include all receipt fields that admin saved
            $sql = "SELECT b.*, 
                    s.service_name, 
                    s.service_type, 
                    sc.category_name,
                    u.fullname as customer_name, 
                    u.email as customer_email, 
                    u.phone as customer_phone,
                    inv.color_name,
                    inv.color_code,
                    b.color_type,
                    COALESCE(NULLIF(inv.price_per_meter, 0), 0) as inventory_price_per_meter
                    FROM bookings b
                    LEFT JOIN services s ON b.service_id = s.id
                    LEFT JOIN service_categories sc ON s.category_id = sc.id
                    LEFT JOIN users u ON b.user_id = u.id
                    LEFT JOIN inventory inv ON b.selected_color_id = inv.id
                    WHERE b.id = ? AND b.user_id = ?
                    LIMIT 1";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$bookingId, $userId]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found or you do not have permission']);
                exit;
            }
            
            // Check if status allows viewing preview receipt
            $allowedStatuses = ['inspection_completed_waiting_approval', 'inspect_completed', 'preview_receipt_sent'];
            if (!in_array($booking['status'], $allowedStatuses)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Preview receipt is only available when status is "Inspection Completed - Waiting for Approval"'
                ]);
                exit;
            }
            
            // Use the EXACT stored values from admin's calculation (preview receipt from admin)
            // Admin saves: grand_total, total_amount, fabric_total, fabric_cost, color_price, labor_fee, etc.
            // IMPORTANT: Use stored values exactly as admin calculated them - do not recalculate
            
            $laborFee = floatval($booking['labor_fee'] ?? 0);
            $pickupFee = floatval($booking['pickup_fee'] ?? 0);
            $deliveryFee = floatval($booking['delivery_fee'] ?? 0);
            $gasFee = floatval($booking['gas_fee'] ?? 0);
            $travelFee = floatval($booking['travel_fee'] ?? 0);
            $inspectionFee = floatval($booking['inspection_fee'] ?? 0);
            
            // Use stored fabric cost from admin's calculation (fabric_total or fabric_cost)
            $fabricCost = floatval($booking['fabric_total'] ?? $booking['fabric_cost'] ?? 0);
            $numberOfMeters = floatval($booking['number_of_meters'] ?? $booking['meters'] ?? 0);
            $pricePerMeter = floatval($booking['price_per_meter'] ?? $booking['fabric_cost_per_meter'] ?? $booking['inventory_price_per_meter'] ?? 0);
            
            // Get color_price (separate from fabric cost) - this is what admin saved
            $colorPrice = floatval($booking['color_price'] ?? 0);
            
            // If fabric cost is not stored, calculate it (fallback only)
            if ($fabricCost == 0 && $numberOfMeters > 0 && $pricePerMeter > 0) {
                $fabricCost = $numberOfMeters * $pricePerMeter;
            }
            
            // Use the stored grand_total or total_amount from admin's calculation
            // This is the EXACT total that admin calculated and sent
            $storedGrandTotal = floatval($booking['grand_total'] ?? $booking['total_amount'] ?? 0);
            
            // Calculate subtotal including color_price (as admin did)
            // Material subtotal = fabric cost + color price
            $materialSubtotal = $fabricCost + $colorPrice;
            
            // Calculate subtotal before additional fees (as admin did)
            $subtotal = $laborFee + $materialSubtotal;
            $totalAdditionalFees = $pickupFee + $deliveryFee + $gasFee + $travelFee + $inspectionFee;
            
            // Use admin's calculated grand_total if available (this is the exact amount admin sent)
            // Otherwise calculate as fallback
            if ($storedGrandTotal > 0) {
                $totalAmount = $storedGrandTotal;
            } else {
                $totalAmount = $subtotal + $totalAdditionalFees;
            }
            
            $discount = floatval($booking['discount'] ?? 0);
            $totalPaid = $totalAmount - $discount;
            
            // Materials array - match exactly what admin sent
            $materials = [];
            if ($numberOfMeters > 0 && $pricePerMeter > 0) {
                $materialName = $booking['color_name'] ?? 'Fabric';
                $colorCode = $booking['color_code'] ?? '';
                $colorType = ($booking['color_type'] === 'premium') ? 'Premium' : 'Standard';
                
                // Format: "color_name (color_code) - ₱price/meter" (matching admin format)
                $displayName = $materialName;
                if ($colorCode) {
                    $displayName .= ' (' . $colorCode . ')';
                }
                $displayName .= ' - ₱' . number_format($pricePerMeter, 2) . '/meter';
                
                $materials[] = [
                    'name' => $displayName,
                    'quantity' => $numberOfMeters,
                    'price' => $pricePerMeter,
                    'total' => $fabricCost
                ];
            }
            
            // Add color price as separate line item if it exists and is different from fabric cost
            // This matches how admin displays it
            if ($colorPrice > 0 && $colorPrice != $fabricCost) {
                $materials[] = [
                    'name' => 'Color Price',
                    'quantity' => 1,
                    'price' => $colorPrice,
                    'total' => $colorPrice
                ];
            }
            
            if (floatval($booking['foam_cost'] ?? 0) > 0) {
                $materials[] = [
                    'name' => 'Foam Replacement',
                    'quantity' => 1,
                    'price' => floatval($booking['foam_cost']),
                    'total' => floatval($booking['foam_cost'])
                ];
            }
            
            // Return receipt data
            echo json_encode([
                'success' => true,
                'receipt' => [
                    'booking' => [
                        'id' => $booking['id'],
                        'bookingNumber' => '#' . $booking['id'],
                        'serviceName' => $booking['service_name'] ?? 'N/A',
                        'categoryName' => $booking['category_name'] ?? 'N/A',
                        'colorType' => $booking['color_type'] ?? null,
                        'fabricType' => $booking['fabric_type'] ?? null,
                        'leatherType' => $booking['leather_type'] ?? null,
                        'colorName' => $booking['color_name'] ?? null,
                        'colorCode' => $booking['color_code'] ?? null
                    ],
                    'customer' => [
                        'name' => $booking['customer_name'] ?? 'N/A',
                        'email' => $booking['customer_email'] ?? 'N/A',
                        'phone' => $booking['customer_phone'] ?? 'N/A'
                    ],
                    'service' => [
                        'name' => $booking['service_name'] ?? 'N/A',
                        'type' => $booking['service_type'] ?? 'N/A',
                        'category' => $booking['category_name'] ?? 'N/A'
                    ],
                    'measurements' => [
                        'height' => $booking['measurement_height'] ?? $booking['height'] ?? null,
                        'width' => $booking['measurement_width'] ?? $booking['width'] ?? null,
                        'thickness' => $booking['measurement_thickness'] ?? $booking['thickness'] ?? null,
                        'notes' => $booking['measurement_notes'] ?? $booking['notes'] ?? null
                    ],
                    'damages' => [
                        'types' => $booking['damage_types'] ?? $booking['damage_type'] ?? null,
                        'severity' => $booking['damage_severity'] ?? null,
                        'description' => $booking['damage_description'] ?? $booking['description'] ?? null
                    ],
                    'materials' => $materials,
                    'payment' => [
                        'laborFee' => $laborFee,
                        'fabricCost' => $fabricCost,
                        'colorPrice' => $colorPrice, // Include color_price separately
                        'materialSubtotal' => $materialSubtotal, // fabricCost + colorPrice
                        'foamCost' => floatval($booking['foam_cost'] ?? 0),
                        'miscMaterialsCost' => floatval($booking['misc_materials_cost'] ?? 0),
                        'pickupFee' => $pickupFee,
                        'deliveryFee' => $deliveryFee,
                        'gasFee' => $gasFee,
                        'travelFee' => $travelFee,
                        'inspectionFee' => $inspectionFee,
                        'subtotal' => $subtotal, // laborFee + materialSubtotal
                        'totalAdditionalFees' => $totalAdditionalFees,
                        'discount' => $discount,
                        'totalAmount' => $totalAmount, // This is the admin's calculated grand_total (EXACT value)
                        'grandTotal' => $storedGrandTotal > 0 ? $storedGrandTotal : $totalAmount, // Admin's stored grand_total (EXACT value)
                        'totalPaid' => $totalPaid
                    ]
                ]
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            error_log("Error getting preview receipt: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Approve Preview Receipt (Customer Action)
     * Changes status from 'inspection_completed_waiting_approval' or 'inspect_completed' to 'under_repair'
     * For Delivery Service workflow
     */
    public function approvePreviewReceipt() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        header('Content-Type: application/json');
        
        $bookingId = $_POST['booking_id'] ?? null;
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get booking details
            $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
            $stmt->execute([$bookingId, $this->currentUser()['id']]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found or you do not have permission']);
                return;
            }
            
            // Only allow if status is 'inspection_completed_waiting_approval' or 'inspect_completed'
            $allowedStatuses = ['inspection_completed_waiting_approval', 'inspect_completed', 'preview_receipt_sent'];
            if (!in_array($booking['status'], $allowedStatuses)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Preview receipt can only be approved when status is "Inspection Completed - Waiting for Approval". Current status: ' . $booking['status']
                ]);
                return;
            }
            
            // Update status to 'under_repair' (repair can now begin)
            $updateStmt = $db->prepare("UPDATE bookings SET status = 'under_repair', updated_at = NOW() WHERE id = ?");
            $result = $updateStmt->execute([$bookingId]);
            
            if ($result) {
                // Send notification to admin
                try {
                    require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                    $notificationService = new NotificationService();
                    
                    $customer = $this->currentUser();
                    $subject = "Customer Approved Preview Receipt - Booking #" . $bookingId;
                    $message = "Customer {$customer['fullname']} has approved the preview receipt for Booking #{$bookingId}.\n\n";
                    $message .= "Repair work can now begin. Status has been changed to 'On Repair'.";
                    
                    // Get admin emails
                    $adminStmt = $db->prepare("SELECT email FROM users WHERE role = 'admin'");
                    $adminStmt->execute();
                    $admins = $adminStmt->fetchAll();
                    
                    foreach ($admins as $admin) {
                        $notificationService->sendEmail($admin['email'], $subject, $message);
                    }
                } catch (Exception $e) {
                    error_log("Error sending notification: " . $e->getMessage());
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Preview receipt approved successfully. Repair work will now begin. Status changed to "On Repair".',
                    'new_status' => 'under_repair'
                ]);
            } else {
                throw new Exception('Database update failed');
            }
        } catch (Exception $e) {
            error_log("Error approving preview receipt: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to approve preview receipt']);
        }
    }
    
    /**
     * Reject Preview Receipt (Customer Action)
     * Cancels the booking when customer rejects the preview receipt
     */
    public function rejectPreviewReceipt() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            return;
        }
        
        header('Content-Type: application/json');
        
        $bookingId = $_POST['booking_id'] ?? null;
        
        if (!$bookingId) {
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            return;
        }
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get booking details
            $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
            $stmt->execute([$bookingId, $this->currentUser()['id']]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found or you do not have permission']);
                return;
            }
            
            // Only allow rejection if status is 'inspection_completed_waiting_approval' or 'inspect_completed' or 'preview_receipt_sent'
            $allowedStatuses = ['inspection_completed_waiting_approval', 'inspect_completed', 'preview_receipt_sent'];
            if (!in_array($booking['status'], $allowedStatuses)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Preview receipt can only be rejected when status is "Inspection Completed" or "Preview Receipt Sent". Current status: ' . $booking['status']
                ]);
                return;
            }
            
            // Update status to 'cancelled'
            $updateStmt = $db->prepare("UPDATE bookings SET status = 'cancelled', updated_at = NOW() WHERE id = ?");
            $result = $updateStmt->execute([$bookingId]);
            
            if ($result) {
                // Send notification to admin
                try {
                    require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                    $notificationService = new NotificationService();
                    
                    $customer = $this->currentUser();
                    $subject = "Customer Rejected Preview Receipt - Booking #" . $bookingId;
                    $message = "Customer {$customer['fullname']} has rejected the preview receipt for Booking #{$bookingId}.\n\n";
                    $message .= "The booking has been cancelled.";
                    
                    // Get admin emails
                    $adminStmt = $db->prepare("SELECT email FROM users WHERE role = 'admin'");
                    $adminStmt->execute();
                    $admins = $adminStmt->fetchAll();
                    
                    foreach ($admins as $admin) {
                        $notificationService->sendEmail($admin['email'], $subject, $message);
                    }
                } catch (Exception $e) {
                    error_log("Error sending notification: " . $e->getMessage());
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Preview receipt rejected. Your booking has been cancelled.',
                    'new_status' => 'cancelled'
                ]);
            } else {
                throw new Exception('Database update failed');
            }
        } catch (Exception $e) {
            error_log("Error rejecting preview receipt: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to reject preview receipt']);
        }
    }
    
    /**
     * Get Official Receipt Data (Customer Action)
     * Returns official receipt data for completed/paid bookings
     */
    public function getOfficialReceipt($bookingId) {
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $userId = $this->currentUser()['id'];
        
        try {
            $db = Database::getInstance()->getConnection();
            
            // Get booking details - verify it belongs to the customer
            $sql = "SELECT b.*, 
                    s.service_name, 
                    s.service_type, 
                    sc.category_name,
                    u.fullname as customer_name, 
                    u.email as customer_email, 
                    u.phone as customer_phone,
                    inv.color_name,
                    inv.color_code,
                    b.color_type
                    FROM bookings b
                    LEFT JOIN services s ON b.service_id = s.id
                    LEFT JOIN service_categories sc ON s.category_id = sc.id
                    LEFT JOIN users u ON b.user_id = u.id
                    LEFT JOIN inventory inv ON b.selected_color_id = inv.id
                    WHERE b.id = ? AND b.user_id = ?
                    LIMIT 1";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$bookingId, $userId]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found or you do not have permission']);
                exit;
            }
            
            // Check if booking is paid
            $paymentStatus = strtolower(trim($booking['payment_status'] ?? 'unpaid'));
            $status = strtolower(trim($booking['status'] ?? ''));
            $isPaid = in_array($paymentStatus, ['paid', 'paid_full_cash', 'paid_on_delivery_cod']) || 
                     in_array($status, ['delivered_and_paid', 'completed']);
            
            if (!$isPaid) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Official receipt is only available for completed and paid bookings'
                ]);
                exit;
            }
            
            // Calculate amounts
            $laborFee = floatval($booking['labor_fee'] ?? 0);
            $pickupFee = floatval($booking['pickup_fee'] ?? 0);
            $deliveryFee = floatval($booking['delivery_fee'] ?? 0);
            $gasFee = floatval($booking['gas_fee'] ?? 0);
            $travelFee = floatval($booking['travel_fee'] ?? 0);
            $inspectionFee = floatval($booking['inspection_fee'] ?? 0);
            $colorPrice = floatval($booking['color_price'] ?? 0);
            $numberOfMeters = floatval($booking['number_of_meters'] ?? $booking['meters'] ?? 0);
            $pricePerMeter = floatval($booking['price_per_meter'] ?? $booking['fabric_cost_per_meter'] ?? 0);
            $fabricCost = $numberOfMeters * $pricePerMeter;
            if ($fabricCost == 0 && $colorPrice > 0) {
                $fabricCost = $colorPrice;
            }
            $foamCost = floatval($booking['foam_cost'] ?? 0);
            $miscMaterialsCost = floatval($booking['misc_materials_cost'] ?? 0);
            
            // Calculate totals
            $subtotal = $laborFee + $fabricCost + $foamCost + $miscMaterialsCost;
            $totalAdditionalFees = $pickupFee + $deliveryFee + $gasFee + $travelFee + $inspectionFee;
            $totalAmount = $subtotal + $totalAdditionalFees;
            $discount = floatval($booking['discount'] ?? 0);
            
            // Use the actual grand_total that was saved (this is what the customer actually paid)
            // If grand_total exists and is greater than 0, use it; otherwise calculate it
            $grandTotal = floatval($booking['grand_total'] ?? 0);
            if ($grandTotal > 0) {
                // Use the saved grand_total as the total amount due and total paid
                $totalAmount = $grandTotal;
                $totalPaid = $grandTotal;
            } else {
                // Fallback to calculated amount if grand_total is not set
                $totalPaid = $totalAmount - $discount;
            }
            
            // Generate Official Receipt Number
            $receiptNumber = $booking['receipt_number'] ?? ('OR-' . date('Ymd') . '-' . str_pad($bookingId, 4, '0', STR_PAD_LEFT));
            
            // Payment mode
            $paymentMode = 'Cash';
            if ($paymentStatus === 'paid_on_delivery_cod') {
                $paymentMode = 'Cash on Delivery (COD)';
            } elseif (strpos(strtolower($paymentStatus), 'gcash') !== false || strpos(strtolower($paymentStatus), 'bank') !== false) {
                $paymentMode = 'GCash / Bank Transfer';
            }
            
            // Payment date/time
            $paymentDate = !empty($booking['payment_date']) ? $booking['payment_date'] : 
                          (!empty($booking['updated_at']) ? $booking['updated_at'] : date('Y-m-d H:i:s'));
            $paymentDateFormatted = date('F d, Y', strtotime($paymentDate));
            $paymentTimeFormatted = date('g:i A', strtotime($paymentDate));
            
            // Delivery date
            $deliveryDate = !empty($booking['delivery_date']) ? date('F d, Y', strtotime($booking['delivery_date'])) : 
                           (!empty($booking['completed_at']) ? date('F d, Y', strtotime($booking['completed_at'])) : 
                           (!empty($booking['updated_at']) ? date('F d, Y', strtotime($booking['updated_at'])) : date('F d, Y')));
            
            // Materials array
            $materials = [];
            if ($numberOfMeters > 0 && $pricePerMeter > 0) {
                $materialName = $booking['color_name'] ?? 'Fabric';
                $colorType = ($booking['color_type'] === 'premium') ? 'Premium' : 'Standard';
                $materials[] = [
                    'name' => $materialName . ' (' . $colorType . ')',
                    'quantity' => $numberOfMeters,
                    'unit' => 'meters',
                    'price' => $pricePerMeter,
                    'total' => $fabricCost
                ];
            }
            if ($foamCost > 0) {
                $materials[] = [
                    'name' => 'Foam Replacement',
                    'quantity' => 1,
                    'unit' => 'piece',
                    'price' => $foamCost,
                    'total' => $foamCost
                ];
            }
            if ($miscMaterialsCost > 0) {
                $materials[] = [
                    'name' => 'Miscellaneous Materials',
                    'quantity' => 1,
                    'unit' => 'lot',
                    'price' => $miscMaterialsCost,
                    'total' => $miscMaterialsCost
                ];
            }
            
            // Service items array
            $serviceItems = [];
            if ($laborFee > 0) {
                $serviceItems[] = [
                    'description' => ($booking['service_name'] ?? 'Upholstery Service') . ' – Labor',
                    'quantity' => 1,
                    'unitPrice' => $laborFee,
                    'total' => $laborFee
                ];
            }
            if ($pickupFee > 0) {
                $serviceItems[] = [
                    'description' => 'Pick-Up Fee',
                    'quantity' => 1,
                    'unitPrice' => $pickupFee,
                    'total' => $pickupFee
                ];
            }
            if ($deliveryFee > 0) {
                $serviceItems[] = [
                    'description' => 'Delivery Fee',
                    'quantity' => 1,
                    'unitPrice' => $deliveryFee,
                    'total' => $deliveryFee
                ];
            }
            if ($gasFee > 0) {
                $serviceItems[] = [
                    'description' => 'Gas Fee',
                    'quantity' => 1,
                    'unitPrice' => $gasFee,
                    'total' => $gasFee
                ];
            }
            if ($travelFee > 0) {
                $serviceItems[] = [
                    'description' => 'Travel Fee',
                    'quantity' => 1,
                    'unitPrice' => $travelFee,
                    'total' => $travelFee
                ];
            }
            if ($inspectionFee > 0) {
                $serviceItems[] = [
                    'description' => 'Inspection Fee',
                    'quantity' => 1,
                    'unitPrice' => $inspectionFee,
                    'total' => $inspectionFee
                ];
            }
            
            // Item description
            $itemDescription = $booking['item_description'] ?? $booking['service_name'] ?? 'N/A';
            $damageDescription = $booking['damage_description'] ?? 'N/A';
            
            // Measurements
            $measurement = '';
            if (!empty($booking['measurement_height']) || !empty($booking['measurement_width'])) {
                $height = $booking['measurement_height'] ?? 0;
                $width = $booking['measurement_width'] ?? 0;
                $thickness = $booking['measurement_thickness'] ?? 0;
                if ($thickness > 0) {
                    $measurement = $height . ' x ' . $width . ' x ' . $thickness . ' inches';
                } else {
                    $measurement = $height . ' x ' . $width . ' inches';
                }
            } else {
                $measurement = $booking['measurement_custom'] ?? 'N/A';
            }
            
            // Return receipt data
            echo json_encode([
                'success' => true,
                'receipt' => [
                    'receiptNumber' => $receiptNumber,
                    'dateIssued' => date('F d, Y'),
                    'booking' => [
                        'id' => $booking['id'],
                        'bookingNumber' => '#' . $booking['id'],
                        'serviceName' => $booking['service_name'] ?? 'N/A',
                        'categoryName' => $booking['category_name'] ?? 'N/A',
                        'serviceType' => $booking['service_type'] ?? 'N/A'
                    ],
                    'customer' => [
                        'name' => $booking['customer_name'] ?? 'N/A',
                        'email' => $booking['customer_email'] ?? 'N/A',
                        'phone' => $booking['customer_phone'] ?? 'N/A',
                        'address' => $booking['delivery_address'] ?? $booking['pickup_address'] ?? 'N/A'
                    ],
                    'item' => [
                        'description' => $itemDescription,
                        'damage' => $damageDescription,
                        'measurement' => $measurement,
                        'materials' => $materials
                    ],
                    'services' => $serviceItems,
                    'payment' => [
                        'laborFee' => $laborFee,
                        'fabricCost' => $fabricCost,
                        'foamCost' => $foamCost,
                        'miscMaterialsCost' => $miscMaterialsCost,
                        'pickupFee' => $pickupFee,
                        'deliveryFee' => $deliveryFee,
                        'gasFee' => $gasFee,
                        'travelFee' => $travelFee,
                        'inspectionFee' => $inspectionFee,
                        'subtotal' => $subtotal,
                        'totalAdditionalFees' => $totalAdditionalFees,
                        'discount' => $discount,
                        'totalAmount' => $totalAmount,
                        'grandTotal' => $grandTotal > 0 ? $grandTotal : $totalAmount, // Include grand_total for reference
                        'totalPaid' => $totalPaid,
                        'balance' => 0,
                        'mode' => $paymentMode,
                        'referenceNumber' => $booking['payment_ref_number'] ?? '',
                        'paymentDate' => $paymentDateFormatted,
                        'paymentTime' => $paymentTimeFormatted,
                        'deliveryDate' => $deliveryDate
                    ]
                ]
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            error_log("Error getting official receipt: " . $e->getMessage());
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
}

