-- Create Test Accounts for UphoCare
-- Run this in phpMyAdmin to add test users

USE db_upholcare;

-- Insert test customer account
INSERT INTO users (username, email, password, fullname, phone, role, status, created_at) VALUES
(
    'customer',
    'customer@uphocare.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Test Customer',
    '09123456789',
    'customer',
    'active',
    NOW()
);

-- Insert test admin account
INSERT INTO users (username, email, password, fullname, phone, role, status, created_at) VALUES
(
    'admin',
    'admin@uphocare.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Test Admin',
    '09123456789',
    'admin',
    'active',
    NOW()
);

-- Insert another test customer
INSERT INTO users (username, email, password, fullname, phone, role, status, created_at) VALUES
(
    'testuser',
    'test@uphocare.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Test User',
    '09123456789',
    'customer',
    'active',
    NOW()
);

-- Verify accounts were created
SELECT id, username, email, fullname, role, status, created_at FROM users;
