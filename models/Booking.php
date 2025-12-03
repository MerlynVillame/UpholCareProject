<?php
/**
 * Booking Model (Orders)
 */

require_once ROOT . DS . 'core' . DS . 'Model.php';

class Booking extends Model {
    protected $table = 'bookings';
    
    /**
     * Get all bookings for a customer
     */
    public function getCustomerBookings($customerId, $status = null) {
        $sql = "SELECT b.*, s.service_name, s.service_type, sc.category_name,
                COALESCE(b.status, 'pending') as status
                FROM {$this->table} b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                WHERE b.user_id = ?";
        
        $params = [$customerId];
        
        if ($status && $status !== 'all') {
            $sql .= " AND COALESCE(b.status, 'pending') = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY b.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $bookings = $stmt->fetchAll();
        
        // Ensure all bookings have a valid status (fix NULL/empty statuses)
        foreach ($bookings as &$booking) {
            if (empty($booking['status']) || $booking['status'] === null) {
                $booking['status'] = 'pending';
            }
        }
        
        return $bookings;
    }
    
    /**
     * Get booking details
     */
    public function getBookingDetails($bookingId, $customerId) {
        $sql = "SELECT b.*, s.service_name, s.service_type, s.description as service_description,
                s.price as base_price, sc.category_name,
                u.fullname as customer_name, u.email, u.phone,
                COALESCE(b.status, 'pending') as status
                FROM {$this->table} b
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.id = ? AND b.user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$bookingId, $customerId]);
        $booking = $stmt->fetch();
        
        // Preserve the actual status from database - only default to 'pending' if truly NULL or empty
        // Don't override valid statuses like 'approved', 'in_queue', etc.
        if ($booking) {
            $status = trim($booking['status'] ?? '');
            if ($status === '' || $status === null || strtolower($status) === 'null') {
                $booking['status'] = 'pending';
            } else {
                // Preserve the actual status from database (COALESCE already handled NULL)
                $booking['status'] = $status;
            }
        }
        
        return $booking;
    }
    
    /**
     * Create new booking
     * Note: Booking numbers removed - system now uses availability based on stock and capacity
     */
    public function createBooking($data) {
        // Booking numbers removed - system now uses availability based on stock and capacity
        $data['created_at'] = date('Y-m-d H:i:s');
        // Status is set in the controller based on booking type
        
        return $this->insert($data);
    }
    
    /**
     * Assign booking number to a booking (DEPRECATED - Booking numbers removed)
     * This method is kept for backward compatibility but does nothing
     */
    public function assignBookingNumber($bookingId, $bookingNumberId) {
        // Booking numbers removed - system now uses availability based on stock and capacity
        return true;
    }
    
    /**
     * Get next available booking number (DEPRECATED - Booking numbers removed)
     * Returns null as booking numbers are no longer used
     */
    private function getNextAvailableBookingNumber() {
        // Booking numbers removed - system now uses availability based on stock and capacity
        return null;
    }
    
    /**
     * Get all available booking numbers (DEPRECATED - Booking numbers removed)
     * Returns empty array as booking numbers are no longer used
     */
    public function getAvailableBookingNumbers() {
        // Booking numbers removed - system now uses availability based on stock and capacity
        return [];
    }
    
    /**
     * Get all used booking numbers (DEPRECATED - Booking numbers removed)
     * Returns empty array as booking numbers are no longer used
     */
    public function getUsedBookingNumbers() {
        // Booking numbers removed - system now uses availability based on stock and capacity
        return [];
    }
    
    /**
     * Get booking count by status
     */
    public function getBookingCountByStatus($customerId, $status) {
        if ($customerId) {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table} 
                                        WHERE user_id = ? AND status = ?");
            $stmt->execute([$customerId, $status]);
        } else {
            $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table} 
                                        WHERE status = ?");
            $stmt->execute([$status]);
        }
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Get total bookings count
     */
    public function getTotalBookings() {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table}");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Get total revenue
     */
    public function getTotalRevenue() {
        $stmt = $this->db->prepare("SELECT SUM(total_amount) as total FROM {$this->table} 
                                    WHERE payment_status = 'paid'");
        $stmt->execute();
        $result = $stmt->fetch();
        return $result['total'] ?? 0;
    }
    
    /**
     * Get total bookings count
     */
    public function getTotalBookingsCount($customerId) {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM {$this->table} 
                                    WHERE user_id = ?");
        $stmt->execute([$customerId]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Cancel booking
     */
    public function cancelBooking($bookingId, $customerId) {
        $stmt = $this->db->prepare("UPDATE {$this->table} 
                                    SET status = 'cancelled' 
                                    WHERE id = ? AND user_id = ? AND status = 'pending'");
        return $stmt->execute([$bookingId, $customerId]);
    }
    
    /**
     * Get recent bookings
     */
    public function getRecentBookings($customerId, $limit = 5) {
        if ($customerId) {
            // Get bookings for specific customer
            $sql = "SELECT b.*, s.service_name, u.fullname as customer_name
                    FROM {$this->table} b
                    LEFT JOIN services s ON b.service_id = s.id
                    LEFT JOIN users u ON b.user_id = u.id
                    WHERE b.user_id = ?
                    ORDER BY b.created_at DESC
                    LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$customerId, $limit]);
        } else {
            // Get all recent bookings (for admin dashboard)
            $sql = "SELECT b.*, s.service_name, u.fullname as customer_name
                    FROM {$this->table} b
                    LEFT JOIN services s ON b.service_id = s.id
                    LEFT JOIN users u ON b.user_id = u.id
                    ORDER BY b.created_at DESC
                    LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit]);
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * Find booking by ID
     */
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}

