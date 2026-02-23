-- Migration script for Business Mode Logic Flow

-- 1. Create Business Types table
CREATE TABLE IF NOT EXISTS business_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type_name VARCHAR(100) NOT NULL,
    description TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Insert default Business Types
INSERT INTO business_types (type_name) VALUES 
('Foam Furniture'),
('Vehicle Seats'),
('Bedding'),
('Upholstery'),
('Other')
ON DUPLICATE KEY UPDATE type_name = type_name;

-- 3. Create Customer Businesses table
CREATE TABLE IF NOT EXISTS customer_businesses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL COMMENT 'Must be a user with role = customer',
    business_name VARCHAR(255) NOT NULL,
    business_type_id INT NULL,
    business_address TEXT NOT NULL,
    permit_file VARCHAR(255) NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT NULL,
    approved_at TIMESTAMP NULL,
    rejected_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (business_type_id) REFERENCES business_types(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Update Bookings table
-- Add customer_business_id column
ALTER TABLE bookings 
ADD COLUMN customer_business_id INT NULL AFTER user_id;

-- Add foreign keys to bookings
ALTER TABLE bookings
ADD CONSTRAINT fk_bookings_customer_business_id 
FOREIGN KEY (customer_business_id) REFERENCES customer_businesses(id) ON DELETE SET NULL;

-- Ensure store_location_id has an index and foreign key if not already present
-- (It already exists based on DESCRIBE, but we can ensure the constraint name is consistent if needed)

-- Add index for status check performance
ALTER TABLE customer_businesses ADD INDEX idx_status (status);
