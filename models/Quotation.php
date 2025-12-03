<?php
/**
 * Quotation Model
 */

require_once ROOT . DS . 'core' . DS . 'Model.php';

class Quotation extends Model {
    
    protected $table = 'quotations';
    
    /**
     * Get all quotations for a customer
     */
    public function getCustomerQuotations($userId) {
        $stmt = $this->db->prepare("
            SELECT q.*, 
                   b.id as booking_id,
                   b.booking_date,
                   s.service_name,
                   s.service_type,
                   u.fullname as customer_name,
                   u.email as customer_email
            FROM quotations q
            LEFT JOIN bookings b ON q.booking_id = b.id
            LEFT JOIN services s ON b.service_id = s.id
            LEFT JOIN users u ON b.user_id = u.id
            WHERE b.user_id = ?
            ORDER BY q.created_at DESC
        ");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get quotation by ID with booking details
     */
    public function getQuotationById($quotationId) {
        $stmt = $this->db->prepare("
            SELECT q.*, 
                   b.id as booking_id,
                   b.user_id,
                   b.booking_date,
                   b.status as booking_status,
                   s.service_name,
                   s.service_type,
                   s.description as service_description,
                   u.fullname as customer_name,
                   u.email as customer_email,
                   u.phone as customer_phone
            FROM quotations q
            LEFT JOIN bookings b ON q.booking_id = b.id
            LEFT JOIN services s ON b.service_id = s.id
            LEFT JOIN users u ON b.user_id = u.id
            WHERE q.id = ?
        ");
        $stmt->execute([$quotationId]);
        return $stmt->fetch();
    }
    
    /**
     * Get quotations by booking ID
     */
    public function getQuotationsByBookingId($bookingId) {
        $stmt = $this->db->prepare("
            SELECT q.* 
            FROM quotations q
            WHERE q.booking_id = ?
            ORDER BY q.created_at DESC
        ");
        $stmt->execute([$bookingId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Create new quotation
     */
    public function createQuotation($data) {
        // Generate quotation number if not provided
        if (empty($data['quotation_number'])) {
            $data['quotation_number'] = $this->generateQuotationNumber();
        }
        
        // Set default status if not provided
        if (empty($data['status'])) {
            $data['status'] = 'draft';
        }
        
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->insert($data);
    }
    
    /**
     * Update quotation status
     */
    public function updateQuotationStatus($quotationId, $status, $notes = null) {
        $data = ['status' => $status];
        if ($notes !== null) {
            $data['notes'] = $notes;
        }
        return $this->update($quotationId, $data);
    }
    
    /**
     * Accept quotation
     */
    public function acceptQuotation($quotationId) {
        return $this->updateQuotationStatus($quotationId, 'accepted');
    }
    
    /**
     * Reject quotation
     */
    public function rejectQuotation($quotationId, $reason = null) {
        return $this->updateQuotationStatus($quotationId, 'rejected', $reason);
    }
    
    /**
     * Send quotation (change status from draft to sent)
     */
    public function sendQuotation($quotationId) {
        return $this->updateQuotationStatus($quotationId, 'sent');
    }
    
    /**
     * Generate unique quotation number
     */
    private function generateQuotationNumber() {
        $date = date('Ymd');
        $prefix = 'QT-' . $date . '-';
        
        // Get the last quotation number for today
        $stmt = $this->db->prepare("
            SELECT quotation_number 
            FROM quotations 
            WHERE quotation_number LIKE ? 
            ORDER BY id DESC 
            LIMIT 1
        ");
        $stmt->execute([$prefix . '%']);
        $lastQuotation = $stmt->fetch();
        
        if ($lastQuotation) {
            // Extract number and increment
            $matches = [];
            if (preg_match('/-' . $date . '-(\d+)$/', $lastQuotation['quotation_number'], $matches)) {
                $nextNumber = (int)$matches[1] + 1;
            } else {
                $nextNumber = 1;
            }
        } else {
            $nextNumber = 1;
        }
        
        return $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Check if quotation is expired
     */
    public function isQuotationExpired($quotationId) {
        $quotation = $this->getById($quotationId);
        if (!$quotation || !$quotation['valid_until']) {
            return false;
        }
        
        return strtotime($quotation['valid_until']) < time();
    }
    
    /**
     * Get all quotations for admin
     */
    public function getAllQuotations($status = null) {
        $sql = "
            SELECT q.*, 
                   b.id as booking_id,
                   b.booking_date,
                   b.status as booking_status,
                   s.service_name,
                   s.service_type,
                   u.fullname as customer_name,
                   u.email as customer_email
            FROM quotations q
            LEFT JOIN bookings b ON q.booking_id = b.id
            LEFT JOIN services s ON b.service_id = s.id
            LEFT JOIN users u ON b.user_id = u.id
        ";
        
        if ($status) {
            $sql .= " WHERE q.status = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$status]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }
}

