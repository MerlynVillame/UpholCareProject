<?php
/**
 * CustomerBusiness Model
 * Handles business profile registration and approval status
 */

require_once ROOT . DS . 'core' . DS . 'Model.php';

class CustomerBusiness extends Model {
    protected $table = 'customer_businesses';
    
    /**
     * Get business profile for a user
     */
    public function getByUserId($userId) {
        $sql = "SELECT cb.*, bt.type_name as business_type_name 
                FROM {$this->table} cb
                LEFT JOIN business_types bt ON cb.business_type_id = bt.id
                WHERE cb.user_id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    /**
     * Get all business types
     */
    public function getBusinessTypes() {
        $stmt = $this->db->prepare("SELECT * FROM business_types ORDER BY type_name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Check if user has an approved business
     */
    public function isApproved($userId) {
        $stmt = $this->db->prepare("SELECT status FROM {$this->table} WHERE user_id = ?");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return ($result && $result['status'] === 'approved');
    }
    
    /**
     * Create or update business profile
     */
    public function saveProfile($data) {
        $userId = $data['user_id'];
        
        // Stricter Role Verification: Ensure user is a customer
        $stmt = $this->db->prepare("SELECT role FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch();
        
        if (!$user || $user['role'] !== 'customer') {
            throw new Exception("Unauthorized: Only users with the 'customer' role can register a business.");
        }
        
        $existing = $this->getByUserId($userId);
        
        if ($existing) {
            // Update existing profile (reset status to pending if key info changes)
            $data['updated_at'] = date('Y-m-d H:i:s');
            $data['status'] = 'pending'; // Require re-approval on update
            return $this->update($existing['id'], $data);
        } else {
            // Create new profile
            return $this->insert($data);
        }
    }
    
    /**
     * Get all businesses for Super Admin review
     */
    public function getAllForReview($status = null) {
        $sql = "SELECT cb.*, u.fullname as owner_name, bt.type_name as business_type_name
                FROM {$this->table} cb
                JOIN users u ON cb.user_id = u.id
                LEFT JOIN business_types bt ON cb.business_type_id = bt.id";
        
        $params = [];
        if ($status) {
            $sql .= " WHERE cb.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY cb.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Approve a business
     */
    public function approve($id, $adminId) {
        return $this->update($id, [
            'status' => 'approved',
            'approved_by' => $adminId,
            'approved_at' => date('Y-m-d H:i:s'),
            'rejected_reason' => null
        ]);
    }
    
    /**
     * Reject a business
     */
    public function reject($id, $reason) {
        return $this->update($id, [
            'status' => 'rejected',
            'rejected_reason' => $reason
        ]);
    }
}
