<?php
/**
 * Store Model
 * Handles store location data and operations
 */

require_once ROOT . DS . 'core' . DS . 'Model.php';

class Store extends Model {
    
    public function __construct() {
        parent::__construct();
        $this->table = 'store_locations';
    }
    
    /**
     * Get all active store locations (Bohol only, sorted by rating)
     * Note: Verified admin businesses are automatically added to store_locations table
     * when admin verification is completed, so we only need to query store_locations
     */
    public function getAllActive() {
        // Get stores from store_locations table
        // Filter to only include stores within Bohol bounds
        // Bohol bounds: Lat 9.5-10.2, Lng 123.6-124.4
        $sql = "SELECT id, store_name, address, city, province, latitude, longitude, 
                       phone, email, operating_hours, services_offered, features, rating, 
                       status, created_at, updated_at
                FROM {$this->table} 
                WHERE status = 'active'
                  AND latitude IS NOT NULL 
                  AND longitude IS NOT NULL
                  AND latitude >= 9.5 
                  AND latitude <= 10.2
                  AND longitude >= 123.6 
                  AND longitude <= 124.4
                ORDER BY rating DESC, store_name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get store by ID
     */
    public function getById($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ? AND status = 'active'";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Find nearest stores based on user coordinates (including verified admin businesses)
     */
    public function findNearestStores($userLat, $userLng, $limit = 5) {
        // Find stores from store_locations table (includes verified admin businesses)
        $sql = "SELECT *, 
                (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
                sin(radians(latitude)))) AS distance
                FROM {$this->table} 
                WHERE status = 'active'
                  AND latitude IS NOT NULL 
                  AND longitude IS NOT NULL
                HAVING distance < 50
                ORDER BY distance ASC
                LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userLat, $userLng, $userLat, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get stores by city (including verified admin businesses)
     */
    public function getByCity($city) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE city = ? AND status = 'active'
                AND latitude IS NOT NULL 
                AND longitude IS NOT NULL
                ORDER BY store_name";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$city]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all cities with stores (including verified admin businesses)
     */
    public function getAllCities() {
        $sql = "SELECT DISTINCT city, province FROM {$this->table} 
                WHERE status = 'active'
                AND latitude IS NOT NULL 
                AND longitude IS NOT NULL
                ORDER BY city";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Search stores by name or address (including verified admin businesses)
     */
    public function searchStores($searchTerm) {
        // Search in store_locations table (includes verified admin businesses)
        $sql = "SELECT * FROM {$this->table} 
                WHERE (store_name LIKE ? OR address LIKE ? OR city LIKE ?) 
                AND status = 'active'
                AND latitude IS NOT NULL 
                AND longitude IS NOT NULL
                ORDER BY store_name";
        
        $searchPattern = "%{$searchTerm}%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchPattern, $searchPattern, $searchPattern]);
        return $stmt->fetchAll();
    }
}
