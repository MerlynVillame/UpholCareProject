<?php
/**
 * Booking Number Assignment System
 * Admin assigns booking numbers to customers before they can make reservations
 */

class BookingNumberAssignment {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Assign booking number to customer
     */
    public function assignBookingNumberToCustomer($customerId, $bookingNumberId, $adminId) {
        try {
            // Check if customer already has an active booking number
            $stmt = $this->db->prepare("
                SELECT id FROM customer_booking_numbers 
                WHERE customer_id = ? AND status = 'active'
            ");
            $stmt->execute([$customerId]);
            
            if ($stmt->fetch()) {
                return ['success' => false, 'message' => 'Customer already has an active booking number'];
            }
            
            // Assign booking number to customer
            $stmt = $this->db->prepare("
                INSERT INTO customer_booking_numbers 
                (customer_id, booking_number_id, assigned_by_admin_id, status, assigned_at) 
                VALUES (?, ?, ?, 'active', NOW())
            ");
            
            $result = $stmt->execute([$customerId, $bookingNumberId, $adminId]);
            
            if ($result) {
                // Log admin activity
                $this->logAdminActivity($adminId, 'assign_booking_number', 'customer_booking_number', $this->db->lastInsertId(), "Assigned booking number to customer");
                
                return ['success' => true, 'message' => 'Booking number assigned successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to assign booking number'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Get available booking numbers for assignment
     */
    public function getAvailableBookingNumbers() {
        $stmt = $this->db->prepare("
            SELECT bn.* FROM booking_numbers bn 
            LEFT JOIN customer_booking_numbers cbn ON bn.id = cbn.booking_number_id AND cbn.status = 'active'
            WHERE cbn.booking_number_id IS NULL 
            ORDER BY bn.id ASC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get assigned booking numbers
     */
    public function getAssignedBookingNumbers() {
        $stmt = $this->db->prepare("
            SELECT cbn.*, bn.booking_number, u.fullname as customer_name, u.email, u.phone,
                   admin.fullname as assigned_by_admin
            FROM customer_booking_numbers cbn
            LEFT JOIN booking_numbers bn ON cbn.booking_number_id = bn.id
            LEFT JOIN users u ON cbn.customer_id = u.id
            LEFT JOIN users admin ON cbn.assigned_by_admin_id = admin.id
            ORDER BY cbn.assigned_at DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get customer's assigned booking number
     */
    public function getCustomerBookingNumber($customerId) {
        $stmt = $this->db->prepare("
            SELECT cbn.*, bn.booking_number
            FROM customer_booking_numbers cbn
            LEFT JOIN booking_numbers bn ON cbn.booking_number_id = bn.id
            WHERE cbn.customer_id = ? AND cbn.status = 'active'
        ");
        $stmt->execute([$customerId]);
        return $stmt->fetch();
    }
    
    /**
     * Revoke booking number from customer
     */
    public function revokeBookingNumber($customerBookingNumberId, $adminId, $reason = '') {
        try {
            $stmt = $this->db->prepare("
                UPDATE customer_booking_numbers 
                SET status = 'revoked', revoked_by_admin_id = ?, revoked_at = NOW(), revoke_reason = ?
                WHERE id = ?
            ");
            
            $result = $stmt->execute([$adminId, $reason, $customerBookingNumberId]);
            
            if ($result) {
                $this->logAdminActivity($adminId, 'revoke_booking_number', 'customer_booking_number', $customerBookingNumberId, "Revoked booking number: " . $reason);
                return ['success' => true, 'message' => 'Booking number revoked successfully'];
            }
            
            return ['success' => false, 'message' => 'Failed to revoke booking number'];
            
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
    
    /**
     * Log admin activity
     */
    private function logAdminActivity($adminId, $action, $targetType, $targetId, $details) {
        $stmt = $this->db->prepare("
            INSERT INTO admin_activity_log 
            (admin_id, action, target_type, target_id, details, ip_address, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        
        $stmt->execute([$adminId, $action, $targetType, $targetId, $details, $ipAddress]);
    }
}
