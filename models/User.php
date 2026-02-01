<?php
/**
 * User Model
 */

require_once ROOT . DS . 'core' . DS . 'Model.php';

class User extends Model {
    protected $table = 'users';
    
    /**
     * Find user by ID
     */
    public function findById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Find user by username
     */
    public function findByUsername($username) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    /**
     * Find user by email
     */
    public function findByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    /**
     * Authenticate user by email
     */
    public function authenticate($email, $password) {
        // Try to find user by email first
        $user = $this->findByEmail($email);
        
        // If not found by email, try username for backward compatibility
        if (!$user) {
            $user = $this->findByUsername($email);
        }
        
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Register new user
     */
    public function register($data) {
        // Hash password
        $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        $data['created_at'] = date('Y-m-d H:i:s');
        
        // Set status to 'active' by default if not provided
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }
        
        return $this->insert($data);
    }
    
    /**
     * Delete user
     */
    public function delete($userId) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$userId]);
    }
    
    /**
     * Update last login
     */
    public function updateLastLogin($id) {
        // Check if last_login column exists before updating
        try {
            $stmt = $this->db->prepare("UPDATE {$this->table} SET last_login = NOW() WHERE id = ?");
            return $stmt->execute([$id]);
        } catch (PDOException $e) {
            // If last_login column doesn't exist, just return true (skip the update)
            return true;
        }
    }
    
    /**
     * Get users by role
     */
    public function getByRole($role) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE role = ?");
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }
    
    /**
     * Check if username exists
     */
    public function usernameExists($username) {
        $user = $this->findByUsername($username);
        return $user !== false;
    }
    
    /**
     * Check if email exists
     */
    public function emailExists($email) {
        $user = $this->findByEmail($email);
        return $user !== false;
    }
    
    /**
     * Update user information
     */
    public function updateUser($userId, $data) {
        // Get table columns to filter out non-existent columns
        $columns = $this->getTableColumns();
        
        // Filter data to only include existing columns
        $filteredData = [];
        foreach ($data as $key => $value) {
            if (in_array($key, $columns)) {
                $filteredData[$key] = $value;
            }
        }
        
        if (empty($filteredData)) {
            error_log("No valid columns to update for user ID: {$userId}");
            return false;
        }
        
        $fields = [];
        $values = [];
        
        foreach ($filteredData as $key => $value) {
            $fields[] = "{$key} = ?";
            $values[] = $value;
        }
        
        $values[] = $userId;
        
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        
        $result = $stmt->execute($values);
        
        // Log for debugging
        if (!$result) {
            error_log("Failed to update user ID: {$userId}. Error: " . implode(', ', $stmt->errorInfo()));
        }
        
        return $result;
    }
    
    /**
     * Get table columns
     */
    private function getTableColumns() {
        static $columnsCache = [];
        
        if (isset($columnsCache[$this->table])) {
            return $columnsCache[$this->table];
        }
        
        try {
            $stmt = $this->db->query("DESCRIBE {$this->table}");
            $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
            $columnsCache[$this->table] = $columns;
            return $columns;
        } catch (Exception $e) {
            error_log("Error getting table columns for {$this->table}: " . $e->getMessage());
            return [];
        }
    }
}

