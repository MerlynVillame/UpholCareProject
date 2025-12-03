<?php
/**
 * Inventory Model
 * Manages color/fabric inventory with premium/standard types
 */

require_once ROOT . DS . 'core' . DS . 'Model.php';

class Inventory extends Model {
    protected $table = 'inventory';
    
    /**
     * Get available colors for a specific store
     */
    public function getAvailableColors($storeLocationId = null, $fabricType = null) {
        try {
            // First check if table exists
            $checkTable = $this->db->query("SHOW TABLES LIKE '{$this->table}'");
            if ($checkTable->rowCount() === 0) {
                // Table doesn't exist, return empty array
                return [];
            }
            
            // Check if store_location_id column exists
            $checkColumn = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'store_location_id'");
            $hasStoreLocationColumn = $checkColumn->rowCount() > 0;
            
            $sql = "SELECT * FROM {$this->table} WHERE 1=1";
            $params = [];
            
            // Only filter by status if column exists
            $checkStatusColumn = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'status'");
            if ($checkStatusColumn->rowCount() > 0) {
                $sql .= " AND status != 'out-of-stock'";
            }
            
            // Filter by store location if column exists and store ID is provided
            // Only show colors that are specifically assigned to this store
            if ($hasStoreLocationColumn && $storeLocationId) {
                $sql .= " AND store_location_id = ?";
                $params[] = $storeLocationId;
            } elseif ($hasStoreLocationColumn && !$storeLocationId) {
                // If no store ID provided, only show colors not assigned to any specific store (NULL)
                $sql .= " AND store_location_id IS NULL";
            }
            
            // Filter by fabric type if column exists
            $checkFabricTypeColumn = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'fabric_type'");
            if ($checkFabricTypeColumn->rowCount() > 0 && $fabricType) {
                $sql .= " AND fabric_type = ?";
                $params[] = $fabricType;
            }
            
            $sql .= " ORDER BY color_name ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error in getAvailableColors: " . $e->getMessage());
            // Return empty array on error instead of throwing
            return [];
        }
    }
    
    /**
     * Get color by ID
     */
    public function getColorById($colorId) {
        $sql = "SELECT * FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$colorId]);
        return $stmt->fetch();
    }
    
    /**
     * Calculate color price based on type
     */
    public function getColorPrice($colorId, $fabricType = 'standard') {
        $color = $this->getColorById($colorId);
        if (!$color) {
            return 0.00;
        }
        
        $basePrice = floatval($color['price_per_unit'] ?? 0);
        
        if ($fabricType === 'premium') {
            $premiumPrice = floatval($color['premium_price'] ?? 0);
            return $basePrice + $premiumPrice;
        }
        
        return $basePrice;
    }
    
    /**
     * Check if color is available at store
     */
    public function isColorAvailable($colorId, $storeLocationId = null) {
        try {
            // Check if table exists
            $checkTable = $this->db->query("SHOW TABLES LIKE '{$this->table}'");
            if ($checkTable->rowCount() === 0) {
                return false;
            }
            
            $sql = "SELECT * FROM {$this->table} WHERE id = ?";
            $params = [$colorId];
            
            // Check if status column exists
            $checkStatusColumn = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'status'");
            if ($checkStatusColumn->rowCount() > 0) {
                $sql .= " AND status != 'out-of-stock'";
            }
            
            // Check if store_location_id column exists
            $checkStoreColumn = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'store_location_id'");
            if ($checkStoreColumn->rowCount() > 0 && $storeLocationId) {
                $sql .= " AND (store_location_id = ? OR store_location_id IS NULL)";
                $params[] = $storeLocationId;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $result = $stmt->fetch();
            
            return $result !== false;
        } catch (Exception $e) {
            error_log("Error in isColorAvailable: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Get all inventory items
     */
    public function getAll($storeLocationId = null) {
        try {
            // Check if table exists
            $checkTable = $this->db->query("SHOW TABLES LIKE '{$this->table}'");
            if ($checkTable->rowCount() === 0) {
                return [];
            }
            
            // Check if store_location_id column exists
            $checkColumn = $this->db->query("SHOW COLUMNS FROM {$this->table} LIKE 'store_location_id'");
            $hasStoreLocationColumn = $checkColumn->rowCount() > 0;
            
            // Check if store_locations table exists
            $checkStoreTable = $this->db->query("SHOW TABLES LIKE 'store_locations'");
            $hasStoreTable = $checkStoreTable->rowCount() > 0;
            
            // Build query based on what columns/tables exist
            if ($hasStoreLocationColumn && $hasStoreTable) {
                $sql = "SELECT i.*, sl.store_name 
                        FROM {$this->table} i
                        LEFT JOIN store_locations sl ON i.store_location_id = sl.id";
            } else {
                // If store_location_id column doesn't exist, just select from inventory
                $sql = "SELECT i.*, NULL as store_name 
                        FROM {$this->table} i";
            }
            
            $params = [];
            
            // Only filter by store if column exists
            if ($hasStoreLocationColumn && $storeLocationId) {
                $sql .= " WHERE i.store_location_id = ?";
                $params[] = $storeLocationId;
            }
            
            $sql .= " ORDER BY i.color_name ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            error_log("Error in Inventory::getAll(): " . $e->getMessage());
            // Return empty array on error instead of throwing
            return [];
        }
    }
    
    /**
     * Create inventory item
     */
    public function create($data) {
        // Get all columns from the table
        $columnsResult = $this->db->query("SHOW COLUMNS FROM {$this->table}");
        $columns = $columnsResult->fetchAll(PDO::FETCH_COLUMN);
        
        // Check which columns exist
        $hasFabricType = in_array('fabric_type', $columns);
        $hasLeatherType = in_array('leather_type', $columns);
        $hasPricePerUnit = in_array('price_per_unit', $columns);
        $hasStandardPrice = in_array('standard_price', $columns);
        $hasPremiumPrice = in_array('premium_price', $columns);
        $hasPricePerMeter = in_array('price_per_meter', $columns);
        $hasStoreLocation = in_array('store_location_id', $columns);
        $hasStatus = in_array('status', $columns);
        
        // Build column list dynamically
        $insertColumns = ['color_code', 'color_name', 'color_hex'];
        $insertValues = [
            $data['color_code'],
            $data['color_name'],
            $data['color_hex'] ?? '#000000'
        ];
        
        // Add type column if exists
        // Normalize to lowercase for consistency
        // IMPORTANT: Check both fabric_type and leather_type keys from $data
        $typeValueRaw = $data['fabric_type'] ?? $data['leather_type'] ?? '';
        error_log("DEBUG Inventory::create - Raw typeValue from data: " . var_export($typeValueRaw, true));
        error_log("DEBUG Inventory::create - data['fabric_type']: " . var_export($data['fabric_type'] ?? 'NOT SET', true));
        error_log("DEBUG Inventory::create - data['leather_type']: " . var_export($data['leather_type'] ?? 'NOT SET', true));
        error_log("DEBUG Inventory::create - All data keys: " . implode(', ', array_keys($data)));
        
        if (empty($typeValueRaw)) {
            $typeValue = 'standard';
            error_log("DEBUG Inventory::create - typeValue was empty, defaulting to 'standard'");
        } else {
            $typeValue = strtolower(trim($typeValueRaw));
            error_log("DEBUG Inventory::create - Normalized typeValue: " . $typeValue);
        }
        
        // Validate - must be exactly 'standard' or 'premium' (lowercase)
        if ($typeValue !== 'standard' && $typeValue !== 'premium') {
            error_log("DEBUG Inventory::create - Invalid typeValue '{$typeValue}', defaulting to 'standard'");
            $typeValue = 'standard'; // Default to standard if invalid
        }
        
        error_log("DEBUG Inventory::create - Final typeValue to insert: " . $typeValue);
        error_log("DEBUG Inventory::create - hasFabricType: " . ($hasFabricType ? 'true' : 'false'));
        error_log("DEBUG Inventory::create - hasLeatherType: " . ($hasLeatherType ? 'true' : 'false'));
        
        if ($hasFabricType) {
            $insertColumns[] = 'fabric_type';
            $insertValues[] = $typeValue; // This should be 'premium' or 'standard'
            error_log("DEBUG Inventory::create - Adding fabric_type = '{$typeValue}' to insert (array index: " . (count($insertValues) - 1) . ")");
        } elseif ($hasLeatherType) {
            $insertColumns[] = 'leather_type';
            $insertValues[] = $typeValue;
            error_log("DEBUG Inventory::create - Adding leather_type = '{$typeValue}' to insert");
        } else {
            error_log("DEBUG Inventory::create - WARNING: Neither fabric_type nor leather_type column exists!");
        }
        
        // Add price columns if they exist
        if ($hasPricePerUnit) {
            $insertColumns[] = 'price_per_unit';
            $insertValues[] = $data['price_per_unit'] ?? $data['standard_price'] ?? 0.00;
        } elseif ($hasStandardPrice) {
            $insertColumns[] = 'standard_price';
            $insertValues[] = $data['standard_price'] ?? $data['price_per_unit'] ?? 0.00;
        }
        
        if ($hasPremiumPrice) {
            $insertColumns[] = 'premium_price';
            $insertValues[] = $data['premium_price'] ?? 0.00;
        }
        
        // Add price_per_meter if column exists
        if ($hasPricePerMeter) {
            $insertColumns[] = 'price_per_meter';
            $insertValues[] = $data['price_per_meter'] ?? 0.00;
        }
        
        // Add quantity
        $insertColumns[] = 'quantity';
        $insertValues[] = $data['quantity'] ?? 0.00;
        
        // Add store_location_id if exists
        if ($hasStoreLocation) {
            $insertColumns[] = 'store_location_id';
            $insertValues[] = $data['store_location_id'] ?? null;
        }
        
        // Calculate status
        $status = 'in-stock';
        if (floatval($data['quantity'] ?? 0) === 0) {
            $status = 'out-of-stock';
        } elseif (floatval($data['quantity'] ?? 0) < 5) {
            $status = 'low-stock';
        }
        
        // Add status if column exists
        if ($hasStatus) {
            $insertColumns[] = 'status';
            $insertValues[] = $status;
        }
        
        // Build SQL
        $columnsStr = implode(', ', $insertColumns);
        $placeholders = implode(', ', array_fill(0, count($insertValues), '?'));
        $sql = "INSERT INTO {$this->table} ({$columnsStr}) VALUES ({$placeholders})";
        
        // Debug: Log the SQL and values
        error_log("DEBUG Inventory::create - SQL: " . $sql);
        error_log("DEBUG Inventory::create - Columns: " . $columnsStr);
        error_log("DEBUG Inventory::create - Values: " . print_r($insertValues, true));
        
        // Find the index of fabric_type in insertValues to verify the value
        if ($hasFabricType) {
            $fabricTypeIndex = array_search('fabric_type', $insertColumns);
            if ($fabricTypeIndex !== false && isset($insertValues[$fabricTypeIndex])) {
                error_log("DEBUG Inventory::create - fabric_type value at index {$fabricTypeIndex}: " . var_export($insertValues[$fabricTypeIndex], true));
            }
        }
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($insertValues);
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            error_log("DEBUG Inventory::create - SQL Error: " . print_r($errorInfo, true));
            throw new Exception("Database error: " . ($errorInfo[2] ?? 'Unknown error'));
        }
        
        $insertId = $this->db->lastInsertId();
        error_log("DEBUG Inventory::create - Insert successful, ID: " . $insertId);
        
        // Verify what was actually saved
        $verifyStmt = $this->db->prepare("SELECT fabric_type FROM {$this->table} WHERE id = ?");
        $verifyStmt->execute([$insertId]);
        $saved = $verifyStmt->fetch(PDO::FETCH_ASSOC);
        error_log("DEBUG Inventory::create - Verified saved fabric_type: " . var_export($saved['fabric_type'] ?? 'NULL', true));
        
        return $insertId;
    }
    
    /**
     * Update inventory item
     */
    public function update($id, $data) {
        // Get all columns from the table
        $columnsResult = $this->db->query("SHOW COLUMNS FROM {$this->table}");
        $columns = $columnsResult->fetchAll(PDO::FETCH_COLUMN);
        
        // Check which columns exist
        $hasFabricType = in_array('fabric_type', $columns);
        $hasLeatherType = in_array('leather_type', $columns);
        $hasPricePerMeter = in_array('price_per_meter', $columns);
        $hasPricePerUnit = in_array('price_per_unit', $columns);
        $hasPremiumPrice = in_array('premium_price', $columns);
        $hasStoreLocation = in_array('store_location_id', $columns);
        $hasStatus = in_array('status', $columns);
        
        // Build UPDATE SQL dynamically
        // Normalize fabric_type/leather_type to lowercase
        $typeValueRaw = $data['fabric_type'] ?? $data['leather_type'] ?? '';
        error_log("DEBUG Inventory::update - Raw typeValue from data: " . var_export($typeValueRaw, true));
        
        if (empty($typeValueRaw)) {
            $typeValue = 'standard';
            error_log("DEBUG Inventory::update - typeValue was empty, defaulting to 'standard'");
        } else {
            $typeValue = strtolower(trim($typeValueRaw));
            error_log("DEBUG Inventory::update - Normalized typeValue: " . $typeValue);
        }
        
        if ($typeValue !== 'standard' && $typeValue !== 'premium') {
            error_log("DEBUG Inventory::update - Invalid typeValue '{$typeValue}', defaulting to 'standard'");
            $typeValue = 'standard'; // Default to standard if invalid
        }
        
        error_log("DEBUG Inventory::update - Final typeValue to update: " . $typeValue);
        error_log("DEBUG Inventory::update - hasFabricType: " . ($hasFabricType ? 'true' : 'false'));
        error_log("DEBUG Inventory::update - hasLeatherType: " . ($hasLeatherType ? 'true' : 'false'));
        
        // Build update fields dynamically
        $updateFields = [
            'color_code = ?',
            'color_name = ?',
            'color_hex = ?'
        ];
        
        $updateValues = [
            $data['color_code'],
            $data['color_name'],
            $data['color_hex'] ?? '#000000'
        ];
        
        // Add type column if exists
        if ($hasFabricType) {
            $updateFields[] = 'fabric_type = ?';
            $updateValues[] = $typeValue;
            error_log("DEBUG Inventory::update - Adding fabric_type = '{$typeValue}' to update");
        } elseif ($hasLeatherType) {
            $updateFields[] = 'leather_type = ?';
            $updateValues[] = $typeValue;
            error_log("DEBUG Inventory::update - Adding leather_type = '{$typeValue}' to update");
        } else {
            error_log("DEBUG Inventory::update - WARNING: Neither fabric_type nor leather_type column exists!");
        }
        
        // Add price columns if they exist
        if ($hasPricePerUnit) {
            $updateFields[] = 'price_per_unit = ?';
            $updateValues[] = $data['price_per_unit'] ?? 0.00;
        }
        
        if ($hasPremiumPrice) {
            $updateFields[] = 'premium_price = ?';
            $updateValues[] = $data['premium_price'] ?? 0.00;
        }
        
        // Add price_per_meter if column exists
        if ($hasPricePerMeter) {
            $updateFields[] = 'price_per_meter = ?';
            $updateValues[] = $data['price_per_meter'] ?? 0.00;
        }
        
        // Add quantity
        $updateFields[] = 'quantity = ?';
        $updateValues[] = $data['quantity'] ?? 0.00;
        
        // Add store_location_id if exists
        if ($hasStoreLocation) {
            $updateFields[] = 'store_location_id = ?';
            $updateValues[] = $data['store_location_id'] ?? null;
        }
        
        // Add status if column exists
        if ($hasStatus) {
            // Calculate status
            $status = 'in-stock';
            if (floatval($data['quantity'] ?? 0) === 0) {
                $status = 'out-of-stock';
            } elseif (floatval($data['quantity'] ?? 0) < 5) {
                $status = 'low-stock';
            }
            $updateFields[] = 'status = ?';
            $updateValues[] = $status;
        }
        
        // Always add updated_at
        $updateFields[] = 'updated_at = NOW()';
        
        // Add id for WHERE clause
        $updateValues[] = $id;
        
        // Build and execute SQL
        $sql = "UPDATE {$this->table} SET " . implode(', ', $updateFields) . " WHERE id = ?";
        
        // Debug: Log the SQL and values
        error_log("DEBUG Inventory::update - SQL: " . $sql);
        error_log("DEBUG Inventory::update - Update fields: " . implode(', ', $updateFields));
        error_log("DEBUG Inventory::update - Update values: " . print_r($updateValues, true));
        
        $stmt = $this->db->prepare($sql);
        $result = $stmt->execute($updateValues);
        
        if (!$result) {
            $errorInfo = $stmt->errorInfo();
            error_log("DEBUG Inventory::update - SQL Error: " . print_r($errorInfo, true));
            throw new Exception("Database error: " . ($errorInfo[2] ?? 'Unknown error'));
        }
        
        error_log("DEBUG Inventory::update - Update successful for ID: " . $id);
        
        return $result;
    }
    
    /**
     * Delete inventory item
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$id]);
    }
}

