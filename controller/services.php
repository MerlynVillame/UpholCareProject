<?php
/**
 * Services Controller
 * Handles all service-related operations
 */

require_once '../config/Database.php';

class ServicesController {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all active services
     */
    public function getAllServices() {
        $sql = "SELECT * FROM services ORDER BY service_name";
        return $this->db->fetchAll($sql);
    }
    
    /**
     * Get service by ID
     */
    public function getServiceById($id) {
        $sql = "SELECT * FROM services WHERE service_id = :id";
        return $this->db->fetchOne($sql, ['id' => $id]);
    }
    
    /**
     * Get services by category
     */
    public function getServicesByCategory($category) {
        $sql = "SELECT * FROM services WHERE category = :category ORDER BY service_name";
        return $this->db->fetchAll($sql, ['category' => $category]);
    }
    
    /**
     * Add new service
     */
    public function addService($data) {
        return $this->db->insert('services', $data);
    }
    
    /**
     * Update service
     */
    public function updateService($id, $data) {
        return $this->db->update('services', $data, 'service_id = :id', ['id' => $id]);
    }
    
    /**
     * Delete service
     */
    public function deleteService($id) {
        return $this->db->delete('services', 'service_id = :id', ['id' => $id]);
    }
    
    /**
     * Get service categories
     */
    public function getCategories() {
        $sql = "SELECT DISTINCT category FROM services WHERE category IS NOT NULL ORDER BY category";
        $results = $this->db->fetchAll($sql);
        return array_column($results, 'category');
    }
    
    /**
     * Get service materials
     */
    public function getServiceMaterials($serviceId) {
        $sql = "SELECT sm.*, m.material_name, m.material_type, m.price_per_unit 
                FROM service_materials sm 
                JOIN materials m ON sm.material_id = m.material_id 
                WHERE sm.service_id = :service_id";
        return $this->db->fetchAll($sql, ['service_id' => $serviceId]);
    }
}

// Initialize controller and get services data
$servicesController = new ServicesController();
$services = $servicesController->getAllServices();
$categories = $servicesController->getCategories();

// Include the view
require_once "../views/cus_services.php";