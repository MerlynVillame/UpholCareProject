<?php
/**
 * Business Model
 */

require_once ROOT . DS . 'core' . DS . 'Model.php';

class Business extends Model {
    protected $table = 'business_info';
    
    /**
     * Create business information
     */
    public function create($data) {
        $data['verification_status'] = 'pending';
        $data['created_at'] = date('Y-m-d H:i:s');
        
        return $this->insert($data);
    }
    
    /**
     * Get business info by user ID
     */
    public function getByUserId($userId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE user_id = ?");
        $stmt->execute([$userId]);
        return $stmt->fetch();
    }
    
    /**
     * Update verification status
     */
    public function updateVerificationStatus($userId, $status, $notes = null, $verifiedBy = null) {
        $data = [
            'verification_status' => $status,
            'verified_at' => date('Y-m-d H:i:s')
        ];
        
        if ($notes !== null) {
            $data['verification_notes'] = $notes;
        }
        
        if ($verifiedBy !== null) {
            $data['verified_by'] = $verifiedBy;
        }
        
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
        }
        $fields = implode(', ', $fields);
        
        $values = array_values($data);
        $values[] = $userId;
        
        $stmt = $this->db->prepare("UPDATE {$this->table} SET $fields WHERE user_id = ?");
        return $stmt->execute($values);
    }
    
    /**
     * Get all pending verifications
     */
    public function getPendingVerifications() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE verification_status = 'pending' ORDER BY created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}

