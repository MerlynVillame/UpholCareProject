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
    
    public function __construct() {
        // Require customer role for all methods
        $this->requireRole(ROLE_CUSTOMER);
        $this->bookingModel = $this->model('Booking');
        $this->serviceModel = $this->model('Service');
        $this->storeModel = $this->model('Store');
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
        
        // Get recent bookings
        $recentBookings = $this->bookingModel->getRecentBookings($userId, 5);
        
        $data = [
            'title' => 'Dashboard - ' . APP_NAME,
            'user' => $this->currentUser(),
            'totalBookings' => $totalBookings,
            'pendingBookings' => $pendingBookings,
            'inProgressBookings' => $inProgressBookings,
            'completedBookings' => $completedBookings,
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
                AND status IN ('pending', 'approved', 'for_pickup', 'picked_up', 'for_inspection', 'for_quotation', 'in_progress', 'in_queue')
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
        $defaultStatus = $isBusinessMode ? 'admin_review' : 'pending';
        
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
        
        if ($serviceOption === 'delivery' || $serviceOption === 'both') {
            $deliveryAddress = $_POST['delivery_address'] ?? null;
            $deliveryDate = $_POST['delivery_date'] ?? null;
            // If delivery address is not provided, use user's account address
            if (!$deliveryAddress) {
                $userModel = $this->model('User');
                $userDetails = $userModel->getById($userId);
                $deliveryAddress = $userDetails['address'] ?? null;
            }
            if (!$deliveryAddress) {
                $_SESSION['error'] = 'Please provide delivery address or update your account address.';
                $this->redirect('customer/newRepairReservation');
                return;
            }
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
            // Don't override valid statuses like 'approved', 'in_queue', etc.
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
            if (!in_array($status, ['approved', 'in_queue'])) {
                throw new Exception('You can only update service option when booking status is Approved or In Queue.');
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
            
            if ($newServiceOption === 'delivery' || $newServiceOption === 'both') {
                $deliveryAddress = $_POST['delivery_address'] ?? null;
                // If delivery address is not provided, use user's account address
                if (!$deliveryAddress) {
                    $userModel = $this->model('User');
                    $userDetails = $userModel->getById($userId);
                    $deliveryAddress = $userDetails['address'] ?? null;
                }
                if (!$deliveryAddress) {
                    throw new Exception('Delivery address is required. Please provide it or update your account address.');
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
            $allowedStatuses = ['pending', 'approved', 'in_queue', 'under_repair'];
            
            if (!in_array($status, $allowedStatuses)) {
                throw new Exception('You can only update booking details when status is Pending, Approved, In Queue, or Under Repair.');
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
            
            if ($newServiceOption === 'delivery' || $newServiceOption === 'both') {
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
                    throw new Exception('Delivery address is required. Please provide it or update your account address.');
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
                SELECT b.*, bn.booking_number 
                FROM bookings b
                LEFT JOIN booking_numbers bn ON b.booking_number_id = bn.id
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
        header('Content-Type: application/json');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
            exit;
        }
        
        $input = json_decode(file_get_contents('php://input'), true);
        $bookingId = $input['booking_id'] ?? null;
        
        if (!$bookingId) {
            echo json_encode(['success' => false, 'message' => 'Booking ID is required']);
            exit;
        }
        
        $userId = $this->currentUser()['id'];
        $db = Database::getInstance()->getConnection();
        
        // Verify booking belongs to user
        $stmt = $db->prepare("SELECT id, quotation_sent, status FROM bookings WHERE id = ? AND user_id = ?");
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
        
        // Update booking status to approved (customer accepted the quotation)
        $updateStmt = $db->prepare("UPDATE bookings SET status = 'approved', quotation_accepted = 1, quotation_accepted_at = NOW() WHERE id = ?");
        $updateStmt->execute([$bookingId]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Quotation accepted successfully'
        ]);
        exit;
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
     * Shows only completed bookings
     */
    public function history() {
        $userId = $this->currentUser()['id'];
        
        // Get only completed bookings
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT b.*, 
                bn.booking_number, 
                s.service_name, 
                s.service_type, 
                sc.category_name,
                u.fullname as customer_name, 
                u.email, 
                u.phone,
                COALESCE(b.status, 'pending') as status,
                COALESCE(b.payment_status, 'unpaid') as payment_status
                FROM bookings b
                LEFT JOIN booking_numbers bn ON b.booking_number_id = bn.id
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.user_id = ? 
                AND LOWER(COALESCE(b.status, 'pending')) IN ('completed', 'delivered_and_paid')
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
                bn.booking_number, 
                s.service_name, 
                s.service_type, 
                sc.category_name,
                u.fullname as customer_name, 
                u.email, 
                u.phone,
                COALESCE(b.status, 'pending') as status,
                COALESCE(b.payment_status, 'unpaid') as payment_status
                FROM bookings b
                LEFT JOIN booking_numbers bn ON b.booking_number_id = bn.id
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
                    <h1>UphoCare</h1>
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
                    <p style="font-size: 0.9rem;">This is an official receipt from UphoCare</p>
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
        $userDetails = $userModel->getById($userId);
        
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
            
            // Refresh user details after adding columns
            $userDetails = $userModel->getById($userId);
        } catch (Exception $e) {
            error_log('Error checking/adding columns: ' . $e->getMessage());
            // Continue anyway - columns might already exist
        }
        
        // Set default images
        $coverImage = BASE_URL . 'assets/images/default-cover.svg';
        $profileImage = BASE_URL . 'assets/images/default-avatar.svg';
        
        if (!empty($userDetails['cover_image'])) {
            $coverImagePath = $userDetails['cover_image'];
            // Check if file exists
            if (file_exists(ROOT . DS . $coverImagePath)) {
                $coverImage = BASE_URL . $coverImagePath;
            }
        }
        
        if (!empty($userDetails['profile_image'])) {
            $profileImagePath = $userDetails['profile_image'];
            // Check if file exists
            if (file_exists(ROOT . DS . $profileImagePath)) {
                $profileImage = BASE_URL . $profileImagePath;
            }
        }
        
        $data = [
            'title' => 'My Profile - ' . APP_NAME,
            'user' => $this->currentUser(),
            'userDetails' => $userDetails,
            'coverImage' => $coverImage,
            'profileImage' => $profileImage
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
        // Set headers first to prevent any output issues
        header('Content-Type: application/json');
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // Start output buffering
        ob_start();
        
        try {
            // Check if user is logged in (but don't require specific role for this endpoint)
            if (!$this->isLoggedIn()) {
                http_response_code(401);
                ob_clean();
                echo json_encode([
                    'success' => false,
                    'message' => 'Please login to continue',
                    'colors' => []
                ]);
                ob_end_flush();
                exit;
            }
            
            $storeId = $_GET['store_id'] ?? null;
            
            if (!$storeId) {
                ob_clean();
                echo json_encode([
                    'success' => false,
                    'message' => 'Store ID is required',
                    'colors' => []
                ]);
                ob_end_flush();
                exit;
            }
            
            // Try to load inventory model
            try {
                $inventoryModel = $this->model('Inventory');
            } catch (Exception $modelError) {
                error_log('Error loading Inventory model: ' . $modelError->getMessage());
                ob_clean();
                echo json_encode([
                    'success' => false,
                    'message' => 'Inventory system not available. Please contact administrator.',
                    'colors' => []
                ]);
                ob_end_flush();
                exit;
            }
            
            // Get available colors - method handles missing table/columns gracefully
            try {
                $colors = $inventoryModel->getAvailableColors($storeId);
            } catch (Exception $colorError) {
                error_log('Error getting available colors: ' . $colorError->getMessage());
                // Return empty array instead of error - allows form to work without inventory
                $colors = [];
            }
            
            ob_clean();
            echo json_encode([
                'success' => true,
                'colors' => $colors ? $colors : []
            ], JSON_UNESCAPED_UNICODE);
            ob_end_flush();
        } catch (Exception $e) {
            error_log('Error getting available colors: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            ob_clean();
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load available colors: ' . (defined('DEBUG_MODE') && DEBUG_MODE ? $e->getMessage() : 'Please try again later'),
                'colors' => []
            ], JSON_UNESCAPED_UNICODE);
            ob_end_flush();
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
     * Update business profile
     */
    public function updateBusinessProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect('customer/profile');
        }
        
        $userId = $this->currentUser()['id'];
        
        $businessData = [
            'business_name' => trim($_POST['business_name'] ?? ''),
            'business_type' => trim($_POST['business_type'] ?? ''),
            'business_phone' => trim($_POST['business_phone'] ?? ''),
            'business_email' => trim($_POST['business_email'] ?? ''),
            'business_address' => trim($_POST['business_address'] ?? ''),
            'user_id' => $userId
        ];
        
        // Validate required fields
        if (empty($businessData['business_name'])) {
            $_SESSION['error'] = 'Business name is required';
            $this->redirect('customer/profile');
        }
        
        // Here you would typically save to a business_profiles table
        // For now, we'll just show success message
        $_SESSION['success'] = 'Business profile updated successfully!';
        $this->redirect('customer/profile');
    }
    
    /**
     * Get business bookings
     */
    public function businessBookings() {
        $userId = $this->currentUser()['id'];
        
        // Get business bookings (you would filter by business_type or add a business flag)
        $bookings = $this->bookingModel->getCustomerBookings($userId);
        
        // Filter for business bookings (this is a simple example)
        $businessBookings = array_filter($bookings, function($booking) {
            // You could add a business_type field to bookings or use other criteria
            return !empty($booking['item_description']) && 
                   (strpos(strtolower($booking['item_description']), 'office') !== false ||
                    strpos(strtolower($booking['item_description']), 'business') !== false ||
                    strpos(strtolower($booking['item_description']), 'hotel') !== false ||
                    strpos(strtolower($booking['item_description']), 'restaurant') !== false);
        });
        
        $data = [
            'title' => 'Business Bookings - ' . APP_NAME,
            'user' => $this->currentUser(),
            'bookings' => $businessBookings
        ];
        
        $this->view('customer/business_bookings', $data);
    }
    
    /**
     * Get business statistics (AJAX)
     */
    public function getBusinessStats() {
        header('Content-Type: application/json');
        
        $userId = $this->currentUser()['id'];
        
        // Get business bookings
        $bookings = $this->bookingModel->getCustomerBookings($userId);
        $businessBookings = array_filter($bookings, function($booking) {
            return !empty($booking['item_description']) && 
                   (strpos(strtolower($booking['item_description']), 'office') !== false ||
                    strpos(strtolower($booking['item_description']), 'business') !== false ||
                    strpos(strtolower($booking['item_description']), 'hotel') !== false ||
                    strpos(strtolower($booking['item_description']), 'restaurant') !== false);
        });
        
        $totalBookings = count($businessBookings);
        $totalRevenue = array_sum(array_column($businessBookings, 'total_amount'));
        $pendingOrders = count(array_filter($businessBookings, function($booking) {
            return $booking['status'] === 'pending';
        }));
        $activeProjects = count(array_filter($businessBookings, function($booking) {
            return in_array($booking['status'], ['confirmed', 'in_progress']);
        }));
        
        echo json_encode([
            'success' => true,
            'data' => [
                'totalBookings' => $totalBookings,
                'totalRevenue' => $totalRevenue,
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
        
        $stores = $this->storeModel->getAllActive();
        
        $data = [
            'title' => 'New Business Reservation - ' . APP_NAME,
            'user' => $this->currentUser(),
            'categories' => $categories,
            'stores' => $stores
        ];
        
        $this->view('customer/new_business_reservation', $data);
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
            
            // Prepare reservation data
            $reservationData = [
                'user_id' => $this->currentUser()['id'],
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
        
        $sql = "SELECT ri.*, cbn.id as customer_booking_number_id, bn.booking_number,
                u.fullname as customer_name, u.email, u.phone
                FROM repair_items ri
                LEFT JOIN customer_booking_numbers cbn ON ri.customer_booking_number_id = cbn.id
                LEFT JOIN booking_numbers bn ON cbn.booking_number_id = bn.id
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
        $sql = "SELECT ri.*, cbn.id as customer_booking_number_id, bn.booking_number,
                u.fullname as customer_name, u.email, u.phone
                FROM repair_items ri
                LEFT JOIN customer_booking_numbers cbn ON ri.customer_booking_number_id = cbn.id
                LEFT JOIN booking_numbers bn ON cbn.booking_number_id = bn.id
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
     * Get Repair Reservation Details (AJAX)
     */
    public function getRepairReservationDetails($repairItemId) {
        header('Content-Type: application/json');
        
        $userId = $this->currentUser()['id'];
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT ri.*, cbn.id as customer_booking_number_id, bn.booking_number,
                u.fullname as customer_name, u.email, u.phone
                FROM repair_items ri
                LEFT JOIN customer_booking_numbers cbn ON ri.customer_booking_number_id = cbn.id
                LEFT JOIN booking_numbers bn ON cbn.booking_number_id = bn.id
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
            
            // Get recent notifications (last 10)
            $notifStmt = $db->prepare("
                SELECT id, title, message, type, is_read, created_at 
                FROM notifications 
                WHERE user_id = ? 
                ORDER BY created_at DESC 
                LIMIT 10
            ");
            $notifStmt->execute([$userId]);
            $notifications = $notifStmt->fetchAll();
            
            // Format notifications for display
            $formattedNotifications = [];
            foreach ($notifications as $notif) {
                $formattedNotifications[] = [
                    'id' => $notif['id'],
                    'title' => $notif['title'],
                    'message' => $notif['message'],
                    'type' => $notif['type'],
                    'is_read' => (bool)$notif['is_read'],
                    'created_at' => $notif['created_at'],
                    'time_ago' => $this->timeAgo($notif['created_at'])
                ];
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
}

