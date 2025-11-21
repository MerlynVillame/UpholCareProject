-- UphoCare Database Setup (Empty)
-- This creates the basic database structure without test users

-- Create database if it doesn't exist
CREATE DATABASE IF NOT EXISTS db_upholcare;
USE db_upholcare;

-- Note: This file creates an empty database
-- You can import your own SQL file or create tables as needed
-- No test users or privileges are added

-- Create essential tables for UphoCare system

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    fullname VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'customer') DEFAULT 'customer',
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Services table
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_name VARCHAR(100) NOT NULL,
    service_type VARCHAR(50),
    description TEXT,
    price DECIMAL(10,2),
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    service_id INT,
    booking_date DATE,
    status ENUM('pending', 'confirmed', 'in_progress', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
);

-- Service categories table
CREATE TABLE IF NOT EXISTS service_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_name VARCHAR(100) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
    payment_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- Quotations table
CREATE TABLE IF NOT EXISTS quotations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    quotation_number VARCHAR(50) UNIQUE,
    total_amount DECIMAL(10,2),
    status ENUM('draft', 'sent', 'accepted', 'rejected') DEFAULT 'draft',
    valid_until DATE,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id)
);

-- Insert sample service categories
INSERT INTO service_categories (category_name, description) VALUES
('Vehicle Upholstery', 'Car seats, truck covers, motorcycle seats'),
('Bedding', 'Mattress covers, pillow cases, bed sheets'),
('Furniture', 'Sofa covers, chair cushions, table cloths');

-- Insert sample services
INSERT INTO services (service_name, service_type, description, price) VALUES
('Car Seat Repair', 'Vehicle Upholstery', 'Repair and restore car seats', 150.00),
('Truck Cover Custom', 'Vehicle Upholstery', 'Custom truck bed covers', 300.00),
('Mattress Cover', 'Bedding', 'Custom mattress covers', 80.00),
('Sofa Reupholstery', 'Furniture', 'Complete sofa reupholstering', 500.00);

-- Database and tables created successfully
-- Ready for use with UphoCare system
