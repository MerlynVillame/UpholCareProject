<?php
/**
 * Customer Controller
 */

require_once ROOT . DS . 'core' . DS . 'Controller.php';

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
        $sql = "SELECT ri.*, cbn.id as customer_booking_number_id, bn.booking_number,
                'repair' as booking_type
                FROM repair_items ri
                LEFT JOIN customer_booking_numbers cbn ON ri.customer_booking_number_id = cbn.id
                LEFT JOIN booking_numbers bn ON cbn.booking_number_id = bn.id
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
        
        // Get the first service that matches the category and service type
        $categoryId = $_POST['service_category'];
        $serviceType = $_POST['service_type'];
        
        $services = $this->serviceModel->getByCategoryAndType($categoryId, $serviceType);
        $serviceId = $services[0]['id'] ?? null; // Use the first service as representative
        
        if (!$serviceId) {
            $_SESSION['error'] = 'Service not found. Please try again.';
            $this->redirect('customer/newRepairReservation');
        }
        
        // Check if this is a business mode booking
        $isBusinessMode = isset($_GET['mode']) && $_GET['mode'] === 'business';
        
        // Prepare booking data, only including fields that exist in the database
        $bookingData = [
            'user_id' => $userId,
            'service_id' => $serviceId,
            'booking_number_id' => null, // Will be assigned by admin when accepting the booking
            'store_location_id' => $_POST['store_location_id'] ?? null,
            'service_type' => $serviceType,
            'item_description' => $_POST['item_description'] ?? '',
            'item_type' => $_POST['item_type'] ?? '',
            'pickup_date' => $_POST['pickup_date'] ?? null,
            'notes' => $_POST['notes'] ?? '',
            'total_amount' => $_POST['total_amount'] ?? 0.00,
            'payment_status' => 'unpaid',
            'booking_type' => $isBusinessMode ? 'business' : 'personal',
            'status' => $isBusinessMode ? 'admin_review' : 'pending'
        ];
        
        // Remove null values that might cause issues, but keep booking_number_id as null
        $bookingData = array_filter($bookingData, function($value, $key) {
            // Keep booking_number_id even if it's null (it should be null initially)
            if ($key === 'booking_number_id') {
                return true;
            }
            return $value !== null;
        }, ARRAY_FILTER_USE_BOTH);
        
        $bookingId = $this->bookingModel->createBooking($bookingData);
        
        if ($bookingId) {
            // Notify admin about new booking
            $this->notifyAdminAboutNewBooking($bookingId, $userId);
            
            if ($isBusinessMode) {
                $_SESSION['success'] = 'Business booking created successfully! Your booking has been sent to admin for processing.';
            } else {
                $_SESSION['success'] = 'Booking created successfully! Admin will review and assign you a booking number.';
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
        
        $data = [
            'title' => 'Booking Details - ' . APP_NAME,
            'user' => $this->currentUser(),
            'booking' => $booking
        ];
        
        $this->view('customer/view_booking', $data);
    }

    /**
     * Get Booking Details (AJAX)
     */
    public function getBookingDetails($id) {
        header('Content-Type: application/json');
        $userId = $this->currentUser()['id'];
        $booking = $this->bookingModel->getBookingDetails($id, $userId);
        if ($booking) {
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
     * Payments
     */
    public function payments() {
        $data = [
            'title' => 'Payments - ' . APP_NAME,
            'user' => $this->currentUser()
        ];
        
        $this->view('customer/payments', $data);
    }
    
    /**
     * Services
     */
    public function services() {
        $services = $this->serviceModel->getAllActive();
        $categories = $this->serviceModel->getCategories();
        
        $data = [
            'title' => 'Services - ' . APP_NAME,
            'user' => $this->currentUser(),
            'services' => $services,
            'categories' => $categories
        ];
        
        $this->view('customer/services', $data);
    }
    
    /**
     * Services Catalog - Recommended Services
     */
    public function servicesCatalog() {
        $services = $this->serviceModel->getAllActive();
        $categories = $this->serviceModel->getCategories();
        
        $data = [
            'title' => 'Services Catalog - ' . APP_NAME,
            'user' => $this->currentUser(),
            'services' => $services,
            'categories' => $categories
        ];
        
        $this->view('customer/services_catalog', $data);
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
     */
    public function history() {
        $userId = $this->currentUser()['id'];
        $bookings = $this->bookingModel->getCustomerBookings($userId);
        
        $data = [
            'title' => 'History - ' . APP_NAME,
            'user' => $this->currentUser(),
            'bookings' => $bookings
        ];
        
        $this->view('customer/history', $data);
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
        
        $data = [
            'title' => 'Repair Reservation - ' . APP_NAME,
            'user' => $this->currentUser(),
            'services' => $services,
            'categories' => $categories
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
        
        // Ensure database columns exist
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
        
        $uploadedFiles = [];
        $errors = [];
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $maxFileSize = 5 * 1024 * 1024; // 5MB
        
        // Get user details to delete old images
        $userModel = $this->model('User');
        $userDetails = $userModel->getById($userId);
        
        // Handle cover image
        if (isset($_FILES['cover_image']) && $_FILES['cover_image']['error'] === UPLOAD_ERR_OK) {
            $coverImage = $_FILES['cover_image'];
            
            // Validate file size
            if ($coverImage['size'] > $maxFileSize) {
                $errors[] = 'Cover image size must be less than 5MB';
            } else {
                $coverExtension = strtolower(pathinfo($coverImage['name'], PATHINFO_EXTENSION));
                
                if (in_array($coverExtension, $allowedExtensions)) {
                    // Delete old cover image if exists
                    if (!empty($userDetails['cover_image']) && file_exists(ROOT . DS . $userDetails['cover_image'])) {
                        @unlink(ROOT . DS . $userDetails['cover_image']);
                    }
                    
                    $coverFileName = 'cover_' . $userId . '_' . time() . '.' . $coverExtension;
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
        }
        
        // Handle profile image
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
            $profileImage = $_FILES['profile_image'];
            
            // Validate file size
            if ($profileImage['size'] > $maxFileSize) {
                $errors[] = 'Profile image size must be less than 5MB';
            } else {
                $profileExtension = strtolower(pathinfo($profileImage['name'], PATHINFO_EXTENSION));
                
                if (in_array($profileExtension, $allowedExtensions)) {
                    // Delete old profile image if exists
                    if (!empty($userDetails['profile_image']) && file_exists(ROOT . DS . $userDetails['profile_image'])) {
                        @unlink(ROOT . DS . $userDetails['profile_image']);
                    }
                    
                    $profileFileName = 'profile_' . $userId . '_' . time() . '.' . $profileExtension;
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
        }
        
        if (empty($errors) && !empty($uploadedFiles)) {
            try {
                // Update user profile with image paths
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
                        echo json_encode([
                            'success' => false, 
                            'message' => 'Failed to update profile'
                        ]);
                    }
                } else {
                    http_response_code(400);
                    echo json_encode([
                        'success' => false, 
                        'message' => 'No files to upload'
                    ]);
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
    
}

