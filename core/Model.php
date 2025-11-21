<?php
/**
 * Base Model Class
 */

class Model {
    protected $db;
    protected $table;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get all records
     */
    public function getAll() {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
        $stmt->execute();
        return $stmt->fetchAll();
    }
    
    /**
     * Get record by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Insert a new record
     */
    public function insert($data) {
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
            throw new Exception("No valid columns to insert for table: {$this->table}");
        }
        
        $fields = implode(', ', array_keys($filteredData));
        $placeholders = implode(', ', array_fill(0, count($filteredData), '?'));
        
        $stmt = $this->db->prepare("INSERT INTO {$this->table} ($fields) VALUES ($placeholders)");
        $stmt->execute(array_values($filteredData));
        
        return $this->db->lastInsertId();
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
            // If DESCRIBE fails, return empty array
            return [];
        }
    }
    
    /**
     * Update a record
     */
    public function update($id, $data) {
        $fields = [];
        foreach ($data as $key => $value) {
            $fields[] = "$key = ?";
        }
        $fields = implode(', ', $fields);
        
        $values = array_values($data);
        $values[] = $id;
        
        $stmt = $this->db->prepare("UPDATE {$this->table} SET $fields WHERE id = ?");
        return $stmt->execute($values);
    }
    
    /**
     * Delete a record
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

