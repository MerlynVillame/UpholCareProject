-- Add Missing Tables to Existing Database
-- This will NOT delete your existing data
USE upholcare;

-- Service Categories Table (if not exists)
CREATE TABLE IF NOT EXISTS service_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders Table (rename from bookings or create new)
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) UNIQUE NOT NULL,
    customer_id INT NOT NULL,
    service_id INT NOT NULL,
    item_description TEXT NOT NULL,
    item_type VARCHAR(100),
    pickup_date DATE,
    delivery_date DATE,
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled', 'ready_for_pickup') DEFAULT 'pending',
    total_amount DECIMAL(10, 2) NOT NULL,
    payment_status ENUM('unpaid', 'partial', 'paid') DEFAULT 'unpaid',
    payment_method VARCHAR(50),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_order_number (order_number),
    INDEX idx_customer_id (customer_id),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order Status History Table
CREATE TABLE IF NOT EXISTS order_status_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    notes TEXT,
    changed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order Images Table
CREATE TABLE IF NOT EXISTS order_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image_type ENUM('before', 'after', 'damage') NOT NULL,
    description TEXT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_order_id (order_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    message TEXT NOT NULL,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Update users table to ensure it has all required columns
ALTER TABLE users 
ADD COLUMN IF NOT EXISTS role ENUM('admin', 'customer') DEFAULT 'customer' AFTER phone,
ADD COLUMN IF NOT EXISTS status ENUM('active', 'inactive', 'suspended') DEFAULT 'active' AFTER role,
ADD COLUMN IF NOT EXISTS last_login TIMESTAMP NULL AFTER status;

-- Insert sample service categories
INSERT IGNORE INTO service_categories (id, name, description, icon, status) VALUES
(1, 'Vehicle Covers', 'Repair and restoration of vehicle upholstery including car seats, headliners, and door panels', 'fa-car', 'active'),
(2, 'Bedding', 'Professional restoration and repair of mattress covers and bedding materials', 'fa-bed', 'active'),
(3, 'Furniture', 'Expert repair and reupholstering of furniture covers including sofas, chairs, and cushions', 'fa-couch', 'active');

-- Ensure admin user exists with correct password hash
-- Password: admin123
INSERT INTO users (username, email, password, full_name, role, status) 
VALUES ('admin', 'admin@uphocare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'active')
ON DUPLICATE KEY UPDATE role='admin', status='active';

SELECT 'Missing tables added successfully!' as Result;

