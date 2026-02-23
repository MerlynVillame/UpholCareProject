-- UphoCare Database Schema
-- Repair and Restoration Management System for Upholstery Shops

CREATE DATABASE IF NOT EXISTS uphocare_db;
USE uphocare_db;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'customer'),
    status ENUM('active', 'inactive', 'suspended'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    INDEX idx_username (username),
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Service Categories Table
CREATE TABLE IF NOT EXISTS service_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    icon VARCHAR(50),
    status ENUM('active', 'inactive') ,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Services Table
CREATE TABLE IF NOT EXISTS service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    base_price DECIMAL(10, 2) NOT NULL,
    estimated_days INT DEFAULT 7,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES service_categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Orders Table
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
    FOREIGN KEY (customer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE RESTRICT,
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
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method VARCHAR(50) NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    transaction_reference VARCHAR(100),
    notes TEXT,
    received_by INT,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (received_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Order Images Table
CREATE TABLE IF NOT EXISTS order_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    image_type ENUM('before', 'after', 'damage') NOT NULL,
    description TEXT,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE
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
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_id (user_id),
    INDEX idx_is_read (is_read)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert Default Admin User
-- Password: admin123
INSERT INTO users (username, email, password, full_name, role, status) VALUES
('admin', 'admin@uphocare.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', 'admin', 'active');

-- Insert Demo Customer
-- Password: customer123
INSERT INTO users (username, email, password, full_name, phone, role, status) VALUES
('customer', 'customer@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', '09123456789', 'customer', 'active');

-- Insert Service Categories
INSERT INTO service_categories (name, description, icon, status) VALUES
('Vehicle Covers', 'Repair and restoration of vehicle upholstery including car seats, headliners, and door panels', 'fa-car', 'active'),
('Bedding', 'Professional restoration and repair of mattress covers and bedding materials', 'fa-bed', 'active'),
('Furniture', 'Expert repair and reupholstering of furniture covers including sofas, chairs, and cushions', 'fa-couch', 'active');

-- Insert Sample Services
INSERT INTO services (category_id, name, description, base_price, estimated_days, status) VALUES
(1, 'Car Seat Cover Repair', 'Repair and restoration of car seat covers', 3500.00, 7, 'active'),
(1, 'Headliner Replacement', 'Complete headliner replacement and restoration', 5500.00, 10, 'active'),
(1, 'Door Panel Restoration', 'Restoration of vehicle door panel upholstery', 2800.00, 5, 'active'),
(2, 'Mattress Cover Repair', 'Repair and restoration of mattress covers', 2500.00, 7, 'active'),
(2, 'Pillow Cover Restoration', 'Professional restoration of pillow covers', 800.00, 3, 'active'),
(3, 'Sofa Reupholstering', 'Complete sofa reupholstering service', 8500.00, 14, 'active'),
(3, 'Chair Cover Repair', 'Repair and restoration of chair covers', 3200.00, 7, 'active'),
(3, 'Cushion Cover Replacement', 'Replacement of furniture cushion covers', 1500.00, 5, 'active');

-- Insert Sample Orders (for demo purposes)
INSERT INTO orders (order_number, customer_id, service_id, item_description, item_type, pickup_date, delivery_date, status, total_amount, payment_status) VALUES
('ORD-001', 2, 1, 'Toyota Vios front seat covers - torn on driver side', 'Car Seat', '2024-01-15', '2024-01-22', 'pending', 3500.00, 'unpaid'),
('ORD-002', 2, 4, 'Queen size mattress cover - stain removal and repair', 'Mattress', '2024-01-12', '2024-01-19', 'in_progress', 2500.00, 'partial'),
('ORD-003', 2, 6, '3-seater sofa - complete reupholstering', 'Sofa', '2024-01-08', '2024-01-22', 'completed', 8500.00, 'paid');

