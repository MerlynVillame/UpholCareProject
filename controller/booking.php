<?php
/**
 * Booking Controller
 * Handles all booking-related operations
 */

	require_once __DIR__ . '/../config/database.php';

class BookingController {
    private $db;
    private $serviceNameCol = 'service_name';
    private $servicePriceCol = 'price';
    private $serviceIdCol = 'service_id';
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->resolveServiceColumns();
    }

		/**
		 * Detect actual column names in services table.
		 */
		private function resolveServiceColumns() {
			try {
				$structure = $this->db->getTableStructure('services');
				$columns = array_map(function($row){ return $row['Field']; }, $structure);
				if (!in_array($this->serviceNameCol, $columns)) {
					foreach (['name','title','service'] as $candidate) {
						if (in_array($candidate, $columns)) { $this->serviceNameCol = $candidate; break; }
					}
				}
				if (!in_array($this->servicePriceCol, $columns)) {
					foreach (['amount','cost'] as $candidate) {
						if (in_array($candidate, $columns)) { $this->servicePriceCol = $candidate; break; }
					}
				}
				if (!in_array($this->serviceIdCol, $columns)) {
					foreach (['id'] as $candidate) {
						if (in_array($candidate, $columns)) { $this->serviceIdCol = $candidate; break; }
					}
				}
			} catch (Exception $e) {
				// Keep defaults if describe fails
			}
		}

		/**
		 * Get all services (for booking form options)
		 */
		public function getAllServices() {
			$sql = "SELECT {$this->serviceIdCol} AS service_id, {$this->serviceNameCol} AS service_name, {$this->servicePriceCol} AS price FROM services ORDER BY {$this->serviceNameCol}";
			return $this->db->fetchAll($sql);
		}
    
    /**
     * Get all bookings for a customer
     */
    public function getCustomerBookings($customerId) {
        $sql = "SELECT b.*, s.{$this->serviceNameCol} AS service_name, s.{$this->servicePriceCol} AS service_price, c.first_name, c.last_name 
                FROM bookings b 
                JOIN services s ON b.service_id = s.{$this->serviceIdCol} 
                JOIN customers c ON b.customer_id = c.customer_id 
                WHERE b.customer_id = :customer_id 
                ORDER BY b.booking_date DESC, b.booking_time DESC";
        return $this->db->fetchAll($sql, ['customer_id' => $customerId]);
    }
    
    /**
     * Get booking by ID
     */
    public function getBookingById($id) {
        $sql = "SELECT b.*, s.{$this->serviceNameCol} AS service_name, s.description as service_description, 
                       s.{$this->servicePriceCol} as service_price, c.first_name, c.last_name, c.email, c.phone
                FROM bookings b 
                JOIN services s ON b.service_id = s.{$this->serviceIdCol} 
                JOIN customers c ON b.customer_id = c.customer_id 
                WHERE b.booking_id = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    /**
     * Create new booking
     */
    public function createBooking($data) {
        return $this->db->insert('bookings', $data);
    }
    
    /**
     * Update booking status
     */
    public function updateBookingStatus($id, $status) {
        return $this->db->update('bookings', ['status' => $status], 'booking_id = :id', ['id' => $id]);
    }
    
    /**
     * Cancel booking
     */
    public function cancelBooking($id) {
        return $this->db->update('bookings', ['status' => 'cancelled'], 'booking_id = :id', ['id' => $id]);
    }
    
    /**
     * Get pending bookings count
     */
    public function getPendingBookingsCount($customerId = null) {
        if ($customerId) {
            $sql = "SELECT COUNT(*) as count FROM bookings WHERE customer_id = :customer_id AND status = 'pending'";
            $result = $this->db->fetchOne($sql, ['customer_id' => $customerId]);
        } else {
            $sql = "SELECT COUNT(*) as count FROM bookings WHERE status = 'pending'";
            $result = $this->db->fetchOne($sql);
        }
        return $result['count'];
    }
    
    /**
     * Get bookings by date range
     */
    public function getBookingsByDateRange($startDate, $endDate) {
        $sql = "SELECT b.*, s.{$this->serviceNameCol} AS service_name, c.first_name, c.last_name 
                FROM bookings b 
                JOIN services s ON b.service_id = s.{$this->serviceIdCol} 
                JOIN customers c ON b.customer_id = c.customer_id 
                WHERE b.booking_date BETWEEN :start_date AND :end_date 
                ORDER BY b.booking_date, b.booking_time";
        return $this->db->fetchAll($sql, [
            'start_date' => $startDate,
            'end_date' => $endDate
        ]);
    }
    
    /**
     * Check if time slot is available
     */
    public function isTimeSlotAvailable($date, $time, $serviceId, $excludeBookingId = null) {
        $sql = "SELECT COUNT(*) as count FROM bookings 
                WHERE booking_date = :date AND booking_time = :time 
                AND service_id = :service_id AND status IN ('pending', 'confirmed', 'in_progress')";
        
        $params = [
            'date' => $date,
            'time' => $time,
            'service_id' => $serviceId
        ];
        
        if ($excludeBookingId) {
            $sql .= " AND booking_id != :exclude_id";
            $params['exclude_id'] = $excludeBookingId;
        }
        
        $result = $this->db->fetchOne($sql, $params);
        return $result['count'] == 0;
    }
    
    /**
     * Get booking photos
     */
    public function getBookingPhotos($bookingId) {
        $sql = "SELECT * FROM booking_photos WHERE booking_id = :booking_id ORDER BY photo_id";
        return $this->db->fetchAll($sql, ['booking_id' => $bookingId]);
    }
    
    /**
     * Add booking photo
     */
    public function addBookingPhoto($data) {
        return $this->db->insert('booking_photos', $data);
    }
}

// Initialize controller
$bookingController = new BookingController();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'create_booking':
            $data = [
                'customer_id' => $_POST['customer_id'] ?? 1, // Default to customer ID 1 for demo
                'service_id' => $_POST['service_id'],
                'booking_date' => $_POST['booking_date'],
                'booking_time' => $_POST['booking_time'],
                'notes' => $_POST['notes'] ?? '',
                'total_amount' => $_POST['total_amount'],
                'status' => 'pending'
            ];
            
            try {
                $bookingId = $bookingController->createBooking($data);
                echo json_encode(['success' => true, 'booking_id' => $bookingId]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
            
        case 'cancel_booking':
            $bookingId = $_POST['booking_id'];
            try {
                $bookingController->cancelBooking($bookingId);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
            
        case 'check_availability':
            $date = $_POST['date'];
            $time = $_POST['time'];
            $serviceId = $_POST['service_id'];
            
            $available = $bookingController->isTimeSlotAvailable($date, $time, $serviceId);
            echo json_encode(['available' => $available]);
            exit;
    }
}

// Get bookings data for display
	$bookings = $bookingController->getCustomerBookings(1); // Default to customer ID 1 for demo
$pendingCount = $bookingController->getPendingBookingsCount(1);
	$services = $bookingController->getAllServices();

// Include the view
require_once "../views/cus_booking.php";