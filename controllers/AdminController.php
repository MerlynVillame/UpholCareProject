<?php
/**
 * Admin Controller
 */

require_once ROOT . DS . 'core' . DS . 'Controller.php';

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
    public function reports() {
        $db = Database::getInstance()->getConnection();
        
        // Get current year
        $currentYear = date('Y');
        
        // Get monthly sales data from database
        $monthlySalesData = [];
        $monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                      'July', 'August', 'September', 'October', 'November', 'December'];
        
        for ($month = 1; $month <= 12; $month++) {
            $monthStart = sprintf('%04d-%02d-01', $currentYear, $month);
            $monthEnd = sprintf('%04d-%02d-%d', $currentYear, $month, date('t', strtotime($monthStart)));
            
            // Get completed bookings for this month (status = completed AND payment_status = paid)
            // Use updated_at for completion date, fallback to created_at if updated_at is NULL
            $sql = "SELECT 
                        COUNT(*) as orders,
                        COALESCE(SUM(total_amount), 0) as revenue
                    FROM bookings 
                    WHERE status = 'completed' 
                    AND payment_status = 'paid'
                    AND (
                        (updated_at IS NOT NULL AND DATE(updated_at) BETWEEN ? AND ?)
                        OR (updated_at IS NULL AND DATE(created_at) BETWEEN ? AND ?)
                    )";
            
            $stmt = $db->prepare($sql);
            $stmt->execute([$monthStart, $monthEnd, $monthStart, $monthEnd]);
            $result = $stmt->fetch();
            
            $orders = (int)($result['orders'] ?? 0);
            $revenue = (float)($result['revenue'] ?? 0);
            
            // Calculate expenses (30% of revenue as estimated operating costs)
            $expenses = $revenue * 0.30;
            $profit = $revenue - $expenses;
            
            $monthlySalesData[] = [
                'month' => $monthNames[$month - 1],
                'orders' => $orders,
                'revenue' => $revenue,
                'expenses' => $expenses,
                'profit' => $profit
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
            'chartExpenses' => json_encode($expenses)
        ];
        
        $this->view('admin/reports', $data);
    }
    
    /**
     * Inventory Management
     */
    public function inventory() {
        $data = [
            'title' => 'Inventory Management - ' . APP_NAME,
            'user' => $this->currentUser()
        ];
        
        $this->view('admin/inventory', $data);
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
        $sql = "SELECT b.*, bn.booking_number, s.service_name, s.service_type, sc.category_name,
                u.fullname as customer_name, u.email, u.phone
                FROM bookings b
                LEFT JOIN booking_numbers bn ON b.booking_number_id = bn.id
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                LEFT JOIN users u ON b.user_id = u.id
                ORDER BY b.created_at DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute();
        $bookings = $stmt->fetchAll();
        
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
            $this->redirect('admin/allBookings');
        }
        
        $bookingId = $_POST['booking_id'];
        $newStatus = $_POST['status'];
        $paymentStatus = $_POST['payment_status'] ?? null;
        
        $updateData = ['status' => $newStatus];
        if ($paymentStatus !== null) {
            $updateData['payment_status'] = $paymentStatus;
        }
        
        $result = $this->bookingModel->update($bookingId, $updateData);
        
        if ($result) {
            $_SESSION['success'] = 'Booking status updated successfully.';
        } else {
            $_SESSION['error'] = 'Failed to update booking status.';
        }
        
        $this->redirect('admin/allBookings');
    }
    
    /**
     * Get Booking Details (AJAX)
     */
    public function getBookingDetails($bookingId) {
        $db = Database::getInstance()->getConnection();
        
        $sql = "SELECT b.*, bn.booking_number, s.service_name, s.service_type, sc.category_name,
                u.fullname as customer_name, u.email, u.phone
                FROM bookings b
                LEFT JOIN booking_numbers bn ON b.booking_number_id = bn.id
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.id = ?";
        
        $stmt = $db->prepare($sql);
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch();
        
        header('Content-Type: application/json');
        if ($booking) {
            echo json_encode(['success' => true, 'booking' => $booking]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Booking not found']);
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
            
            // Get booking details
            $stmt = $db->prepare("SELECT * FROM bookings WHERE id = ?");
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch();
            
            if (!$booking) {
                http_response_code(404);
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                return;
            }
            
            // Automatically assign booking number if not provided
            if (!$bookingNumberId) {
                // First, try to get an available booking number
                $availableNumbers = $this->bookingModel->getAvailableBookingNumbers();
                
                if (!empty($availableNumbers)) {
                    // Use first available booking number
                    $bookingNumberId = $availableNumbers[0]['id'];
                } else {
                    // No available booking numbers, generate one automatically
                    $prefix = 'BKG-';
                    $date = date('Ymd');
                    
                    // Get the highest number for today to generate next one
                    $stmt = $db->prepare("
                        SELECT booking_number 
                        FROM booking_numbers 
                        WHERE booking_number LIKE ? 
                        ORDER BY booking_number DESC 
                        LIMIT 1
                    ");
                    $likePattern = $prefix . $date . '-%';
                    $stmt->execute([$likePattern]);
                    $lastBooking = $stmt->fetch();
                    
                    $nextNumber = 1;
                    if ($lastBooking && !empty($lastBooking['booking_number'])) {
                        // Extract number from last booking number (e.g., BKG-20251111-0001 -> 1)
                        // Pattern: BKG-YYYYMMDD-NNNN
                        if (preg_match('/-' . $date . '-(\d+)$/', $lastBooking['booking_number'], $matches)) {
                            if (!empty($matches[1])) {
                                $nextNumber = (int)$matches[1] + 1;
                            }
                        }
                    }
                    
                    $newBookingNumber = $prefix . $date . '-' . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
                    
                    // Insert new booking number
                    $stmt = $db->prepare("INSERT INTO booking_numbers (booking_number) VALUES (?)");
                    $stmt->execute([$newBookingNumber]);
                    $bookingNumberId = $db->lastInsertId();
                }
            }
            
            // Get booking number details
            $stmt = $db->prepare("SELECT * FROM booking_numbers WHERE id = ?");
            $stmt->execute([$bookingNumberId]);
            $bookingNumber = $stmt->fetch();
            
            if (!$bookingNumber) {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to create or retrieve booking number']);
                return;
            }
            
            // Update booking: assign booking number and change status to approved
            $updateResult = $this->bookingModel->assignBookingNumber($bookingId, $bookingNumberId);
            
            if ($updateResult) {
                // Update status to approved
                $this->bookingModel->update($bookingId, [
                    'status' => 'approved',
                    'notes' => $adminNotes ? ($booking['notes'] ?? '') . "\n\n[Admin Notes: " . $adminNotes . "]" : ($booking['notes'] ?? '')
                ]);
                
                // Get customer details for notification
                $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
                $stmt->execute([$booking['user_id']]);
                $customer = $stmt->fetch();
                
                // Send notification to customer
                if ($customer) {
                    require_once ROOT . DS . 'core' . DS . 'NotificationService.php';
                    $notificationService = new NotificationService();
                    
                    // Send email notification
                    $notificationService->sendBookingConfirmation(
                        $customer['email'],
                        $customer['fullname'],
                        $bookingNumber['booking_number'],
                        $booking
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
                    
                    // Create notification with better message
                    if ($hasRelatedId) {
                        $notifStmt = $db->prepare("
                            INSERT INTO notifications (user_id, type, title, message, related_id, created_at) 
                            VALUES (?, 'success', ?, ?, ?, NOW())
                        ");
                        $notifStmt->execute([
                            $customer['id'],
                            'Reservation Accepted',
                            "Great news! Your reservation has been accepted by the admin. Booking Number: " . $bookingNumber['booking_number'] . ". You can now proceed with your booking.",
                            $bookingId
                        ]);
                    } else {
                        $notifStmt = $db->prepare("
                            INSERT INTO notifications (user_id, type, title, message, created_at) 
                            VALUES (?, 'success', ?, ?, NOW())
                        ");
                        $notifStmt->execute([
                            $customer['id'],
                            'Reservation Accepted',
                            "Great news! Your reservation has been accepted by the admin. Booking Number: " . $bookingNumber['booking_number'] . ". You can now proceed with your booking."
                        ]);
                    }
                }
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Reservation accepted successfully. Booking number assigned.',
                    'booking_number' => $bookingNumber['booking_number']
                ]);
            } else {
                http_response_code(500);
                echo json_encode(['success' => false, 'message' => 'Failed to accept reservation']);
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
        
        $sql = "SELECT b.*, bn.booking_number, s.service_name, s.service_type, sc.category_name,
                u.fullname as customer_name, u.email as customer_email, u.phone
                FROM bookings b
                LEFT JOIN booking_numbers bn ON b.booking_number_id = bn.id
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
        
        $sql = "SELECT b.*, bn.booking_number, s.service_name, s.service_type, sc.category_name,
                u.fullname as customer_name, u.email, u.phone
                FROM bookings b
                LEFT JOIN booking_numbers bn ON b.booking_number_id = bn.id
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
            
            $sql = "SELECT bn.*, 
                    CASE WHEN cbn.id IS NOT NULL THEN 'assigned' ELSE 'available' END as status
                    FROM booking_numbers bn
                    LEFT JOIN customer_booking_numbers cbn ON bn.id = cbn.booking_number_id AND cbn.status = 'active'
                    ORDER BY bn.created_at DESC";
            
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $bookingNumbers = $stmt->fetchAll();
            
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
            
            // Assign booking number to customer
            $stmt = $db->prepare("
                INSERT INTO customer_booking_numbers 
                (customer_id, booking_number_id, assigned_by_admin_id, status, assigned_at) 
                VALUES (?, ?, ?, 'active', NOW())
            ");
            
            $adminId = $this->currentUser()['id'];
            $result = $stmt->execute([$customerId, $bookingNumberId, $adminId]);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Booking number assigned successfully']);
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
            
            // Get customer's assigned booking number
            $stmt = $db->prepare("
                SELECT cbn.*, bn.booking_number, admin.fullname as assigned_by_admin
                FROM customer_booking_numbers cbn
                LEFT JOIN booking_numbers bn ON cbn.booking_number_id = bn.id
                LEFT JOIN users admin ON cbn.assigned_by_admin_id = admin.id
                WHERE cbn.customer_id = ? AND cbn.status = 'active'
            ");
            $stmt->execute([$customerId]);
            $bookingNumber = $stmt->fetch();
            
            // Get current booking details
            $stmt = $db->prepare("
                SELECT b.*, bn.booking_number, s.service_name, s.service_type, sc.category_name
                FROM bookings b
                LEFT JOIN booking_numbers bn ON b.booking_number_id = bn.id
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                WHERE b.id = ? AND b.user_id = ?
            ");
            $stmt->execute([$bookingId, $customerId]);
            $booking = $stmt->fetch();
            
            // Calculate compliance score
            $complianceScore = 0;
            $totalChecks = 6;
            
            if ($bookingNumber) $complianceScore += 16.67; // Valid booking number
            if ($customer['email']) $complianceScore += 16.67; // Valid email
            if ($customer['phone']) $complianceScore += 16.67; // Contact info
            if ($booking && $booking['service_id']) $complianceScore += 16.67; // Service selection
            if ($booking && $booking['booking_date']) $complianceScore += 16.67; // Booking date
            if ($booking && $booking['notes']) $complianceScore += 16.67; // Additional info
            
            $complianceScore = round($complianceScore);
            
            echo json_encode([
                'success' => true,
                'customer' => $customer,
                'bookingNumber' => $bookingNumber,
                'booking' => $booking,
                'complianceScore' => $complianceScore
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

