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
        $sql = "SELECT b.*, bn.booking_number, s.service_name, s.service_type, sc.category_name
                FROM {$this->table} b
                LEFT JOIN booking_numbers bn ON b.booking_number_id = bn.id
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                WHERE b.user_id = ?";
        
        $params = [$customerId];
        
        if ($status && $status !== 'all') {
            $sql .= " AND b.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY b.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get booking details
     */
    public function getBookingDetails($bookingId, $customerId) {
        $sql = "SELECT b.*, bn.booking_number, s.service_name, s.service_type, s.description as service_description,
                s.price as base_price, sc.category_name,
                u.fullname as customer_name, u.email, u.phone
                FROM {$this->table} b
                LEFT JOIN booking_numbers bn ON b.booking_number_id = bn.id
                LEFT JOIN services s ON b.service_id = s.id
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                LEFT JOIN users u ON b.user_id = u.id
                WHERE b.id = ? AND b.user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$bookingId, $customerId]);
        return $stmt->fetch();
    }
    
    /**
     * Create new booking
     * Note: Booking number is NOT assigned here - it will be assigned by admin when accepting the reservation
     */
    public function createBooking($data) {
        // Do NOT assign booking number here - admin will assign it when accepting the reservation
        // booking_number_id will remain NULL until admin assigns it
        $data['created_at'] = date('Y-m-d H:i:s');
        // Status is set in the controller based on booking type
        
        return $this->insert($data);
    }
    
    /**
     * Assign booking number to a booking (called by admin when accepting)
     */
    public function assignBookingNumber($bookingId, $bookingNumberId) {
        return $this->update($bookingId, ['booking_number_id' => $bookingNumberId]);
    }
    
    /**
     * Get next available booking number (assigned by admin)
     */
    private function getNextAvailableBookingNumber() {
        // Find a booking number that hasn't been used yet
        $stmt = $this->db->prepare("SELECT bn.id FROM booking_numbers bn 
                                    LEFT JOIN bookings b ON bn.id = b.booking_number_id 
                                    WHERE b.booking_number_id IS NULL 
                                    ORDER BY bn.id ASC 
                                    LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch();
        
        return $result ? $result['id'] : null;
    }
    
    /**
     * Get all available booking numbers (for admin)
     */
    public function getAvailableBookingNumbers() {
        $stmt = $this->db->prepare("SELECT bn.* FROM booking_numbers bn 
                                    LEFT JOIN bookings b ON bn.id = b.booking_number_id 
                                    WHERE b.booking_number_id IS NULL 
                                    ORDER BY bn.id ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get all used booking numbers (for admin)
     */
    public function getUsedBookingNumbers() {
        $stmt = $this->db->prepare("SELECT bn.*, b.id as booking_id, b.created_at as booking_created_at 
                                    FROM booking_numbers bn 
                                    INNER JOIN bookings b ON bn.id = b.booking_number_id 
                                    ORDER BY b.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
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
            $sql = "SELECT b.*, bn.booking_number, s.service_name, u.fullname as customer_name
                    FROM {$this->table} b
                    LEFT JOIN booking_numbers bn ON b.booking_number_id = bn.id
                    LEFT JOIN services s ON b.service_id = s.id
                    LEFT JOIN users u ON b.user_id = u.id
                    WHERE b.user_id = ?
                    ORDER BY b.created_at DESC
                    LIMIT ?";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$customerId, $limit]);
        } else {
            // Get all recent bookings (for admin dashboard)
            $sql = "SELECT b.*, bn.booking_number, s.service_name, u.fullname as customer_name
                    FROM {$this->table} b
                    LEFT JOIN booking_numbers bn ON b.booking_number_id = bn.id
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

