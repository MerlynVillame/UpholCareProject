-- Create Test Users for UphoCare System
-- Run this in phpMyAdmin SQL tab

-- First, check if users table exists
-- If it doesn't, you need to run the full database setup first

-- Delete existing test users (optional - uncomment if you want to recreate)
-- DELETE FROM users WHERE username IN ('admin', 'customer', 'testcustomer');

-- Create Admin Account
INSERT INTO users (username, email, password, fullname, phone, role, status, created_at)
VALUES (
    'admin',
    'admin@uphocare.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'System Administrator',
    '09123456789',
    'admin',
    'active',
    NOW()
)
ON DUPLICATE KEY UPDATE 
    email = 'admin@uphocare.com',
    fullname = 'System Administrator';

-- Create Customer Account 1
INSERT INTO users (username, email, password, fullname, phone, role, status, created_at)
VALUES (
    'customer',
    'customer@uphocare.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'Test Customer',
    '09123456789',
    'customer',
    'active',
    NOW()
)
ON DUPLICATE KEY UPDATE 
    email = 'customer@uphocare.com',
    fullname = 'Test Customer';

-- Create Customer Account 2
INSERT INTO users (username, email, password, fullname, phone, role, status, created_at)
VALUES (
    'testcustomer',
    'test@customer.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
    'John Doe',
    '09987654321',
    'customer',
    'active',
    NOW()
)
ON DUPLICATE KEY UPDATE 
    email = 'test@customer.com',
    fullname = 'John Doe';

-- Verify accounts were created
SELECT 
    id, 
    username, 
    email, 
    fullname, 
    role, 
    status, 
    created_at
FROM users
WHERE username IN ('admin', 'customer', 'testcustomer')
ORDER BY role DESC, username;

-- Display login credentials
SELECT 
    '=== LOGIN CREDENTIALS ===' as info
UNION ALL
SELECT '---'
UNION ALL
SELECT 'ADMIN ACCOUNT:'
UNION ALL
SELECT 'Username: admin'
UNION ALL
SELECT 'Password: password'
UNION ALL
SELECT '---'
UNION ALL
SELECT 'CUSTOMER ACCOUNTS:'
UNION ALL
SELECT 'Username: customer'
UNION ALL
SELECT 'Password: password'
UNION ALL
SELECT '---'
UNION ALL
SELECT 'Username: testcustomer'
UNION ALL
SELECT 'Password: password';

