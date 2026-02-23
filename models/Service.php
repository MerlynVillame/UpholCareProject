<?php
/**
 * Service Model
 */

require_once ROOT . DS . 'core' . DS . 'Model.php';

class Service extends Model {
    protected $table = 'services';
    
    /**
     * Get all active services with category information
     */
    public function getAllActive() {
        $sql = "SELECT s.*, 
                s.service_type as category_name,
                CASE 
                    WHEN s.service_type LIKE '%Vehicle%' OR s.service_type LIKE '%Car%' OR s.service_type LIKE '%Truck%' OR s.service_name LIKE '%Truck%' THEN 'car'
                    WHEN s.service_type LIKE '%Bedding%' OR s.service_type LIKE '%Bed%' OR s.service_type LIKE '%Mattress%' THEN 'bed'
                    WHEN s.service_type LIKE '%Furniture%' OR s.service_type LIKE '%Sofa%' THEN 'couch'
                    ELSE 'tag'
                END as category_icon
                FROM {$this->table} s
                WHERE s.status = 'active'
                ORDER BY s.service_type, 
                         CASE 
                             WHEN s.service_name = 'Truck Cover Custom' THEN 1
                             WHEN s.service_name = 'Motor Seat' THEN 2
                             ELSE 3
                         END,
                         s.service_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get services by type
     */
    public function getByType($type) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE service_type = ? AND status = 'active'
                ORDER BY service_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get services by category and type
     */
    public function getByCategoryAndType($categoryId, $type) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE category_id = ? AND service_type = ? AND status = 'active'
                ORDER BY service_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId, $type]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get service by category and name
     */
    public function getByCategoryAndName($categoryId, $serviceName) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE category_id = ? AND service_name = ? AND status = 'active'
                ORDER BY service_name
                LIMIT 1";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId, $serviceName]);
        return $stmt->fetch();
    }
    
    /**
     * Get service types by category
     */
    public function getServiceTypesByCategory($categoryId) {
        $sql = "SELECT DISTINCT service_type as name FROM {$this->table} 
                WHERE category_id = ? AND status = 'active' 
                ORDER BY service_type";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$categoryId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all service types
     */
    public function getServiceTypes() {
        $stmt = $this->db->prepare("SELECT DISTINCT service_type as name FROM {$this->table} WHERE status = 'active' ORDER BY service_type");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    
    /**
     * Get service categories
     */
    public function getCategories() {
        $stmt = $this->db->prepare("SELECT id, category_name as name, description FROM service_categories WHERE status = 'active' ORDER BY category_name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get service details by ID
     */
    public function getServiceDetails($serviceId) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$serviceId]);
        return $stmt->fetch();
    }
    
    /**
     * Get all services with category information (for admin) - excludes archived + inactive
     */
    public function getAllServices() {
        $sql = "SELECT s.*, 
                sc.category_name,
                sc.id as category_id
                FROM {$this->table} s
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                WHERE s.status = 'active'
                ORDER BY s.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get service with category information
     */
    public function getServiceWithCategory($serviceId) {
        $sql = "SELECT s.*, 
                sc.category_name,
                sc.id as category_id,
                sc.description as category_description
                FROM {$this->table} s
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                WHERE s.id = ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$serviceId]);
        return $stmt->fetch();
    }
    
    /**
     * Create new service
     */
    public function createService($data) {
        $data['created_at'] = date('Y-m-d H:i:s');
        if (!isset($data['status'])) {
            $data['status'] = 'active';
        }
        return $this->insert($data);
    }
    
    /**
     * Update service
     */
    public function updateService($serviceId, $data) {
        return $this->update($serviceId, $data);
    }
    
    /**
     * Delete service (soft delete by setting status to inactive)
     */
    public function deleteService($serviceId) {
        return $this->update($serviceId, ['status' => 'inactive']);
    }
    
    /**
     * Archive service (move to archived)
     */
    public function archiveService($serviceId) {
        return $this->update($serviceId, ['status' => 'archived']);
    }
    
    /**
     * Restore archived service back to active
     */
    public function restoreService($serviceId) {
        return $this->update($serviceId, ['status' => 'active']);
    }
    
    /**
     * Get all archived services with category information
     * Includes 'archived', legacy 'inactive', NULL, and empty-string statuses
     */
    public function getArchivedServices() {
        $sql = "SELECT s.*, 
                sc.category_name,
                sc.id as category_id
                FROM {$this->table} s
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                WHERE s.status IN ('archived', 'inactive')
                   OR s.status IS NULL
                   OR s.status = ''
                ORDER BY s.created_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Migrate all inactive / NULL / empty services to archived status
     */
    public function migrateInactiveToArchived() {
        $stmt = $this->db->prepare(
            "UPDATE {$this->table} 
             SET status = 'archived' 
             WHERE status = 'inactive' 
                OR status IS NULL 
                OR status = ''"
        );
        return $stmt->execute();
    }
    
    /**
     * Get all service categories (including inactive for admin)
     */
    public function getAllCategories() {
        $stmt = $this->db->prepare("SELECT * FROM service_categories ORDER BY category_name ASC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get services by store
     */
    public function getServicesByStore($storeId) {
        $sql = "SELECT s.*, 
                sc.category_name
                FROM {$this->table} s
                LEFT JOIN service_categories sc ON s.category_id = sc.id
                WHERE s.store_id = ? AND s.status = 'active'
                ORDER BY sc.category_name, s.service_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$storeId]);
        return $stmt->fetchAll();
    }

    /**
     * Get categories by store (categories that have active services in this store)
     */
    public function getCategoriesByStore($storeId) {
        $sql = "SELECT DISTINCT sc.id, sc.category_name as name, sc.description
                FROM service_categories sc
                INNER JOIN {$this->table} s ON s.category_id = sc.id
                WHERE s.store_id = ? AND s.status = 'active' AND sc.status = 'active'
                ORDER BY sc.category_name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$storeId]);
        return $stmt->fetchAll();
    }
}

