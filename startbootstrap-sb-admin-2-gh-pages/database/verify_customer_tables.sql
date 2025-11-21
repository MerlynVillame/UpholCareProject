-- Verify Customer Module Database Structure
-- Run this in phpMyAdmin to check if your tables are ready

USE upholcare;

-- Check if all required tables exist
SELECT 
    'Tables Check' as Check_Type,
    CASE WHEN COUNT(*) = 5 THEN '✓ PASS' ELSE '✗ MISSING TABLES' END as Status
FROM information_schema.tables 
WHERE table_schema = 'upholcare' 
AND table_name IN ('users', 'bookings', 'services', 'payments', 'quotations');

-- Check users table structure
SELECT 'users table' as Table_Name, 
    CASE WHEN COUNT(*) >= 7 THEN '✓ PASS' ELSE '✗ MISSING COLUMNS' END as Status
FROM information_schema.columns
WHERE table_schema = 'upholcare' 
AND table_name = 'users'
AND column_name IN ('id', 'username', 'email', 'password', 'role', 'status', 'created_at');

-- Check bookings table structure
SELECT 'bookings table' as Table_Name,
    CASE WHEN COUNT(*) >= 10 THEN '✓ PASS' ELSE '✗ MISSING COLUMNS' END as Status
FROM information_schema.columns
WHERE table_schema = 'upholcare' 
AND table_name = 'bookings'
AND column_name IN ('id', 'customer_id', 'service_id', 'status', 'total_amount', 'created_at');

-- Check services table structure
SELECT 'services table' as Table_Name,
    CASE WHEN COUNT(*) >= 5 THEN '✓ PASS' ELSE '✗ MISSING COLUMNS' END as Status
FROM information_schema.columns
WHERE table_schema = 'upholcare' 
AND table_name = 'services'
AND column_name IN ('id', 'service_name', 'price', 'status');

-- Check if there are any customers
SELECT 'Customer Accounts' as Check_Type,
    CASE WHEN COUNT(*) > 0 THEN CONCAT('✓ ', COUNT(*), ' customers found') ELSE '✗ No customers' END as Status
FROM users
WHERE role = 'customer';

-- Check if there are any services
SELECT 'Services Available' as Check_Type,
    CASE WHEN COUNT(*) > 0 THEN CONCAT('✓ ', COUNT(*), ' services found') ELSE '✗ No services' END as Status
FROM services
WHERE status = 'active';

-- List all customers
SELECT 'Customer List' as Info;
SELECT id, username, email, fullname, role, status, created_at
FROM users
WHERE role = 'customer'
ORDER BY created_at DESC
LIMIT 10;

-- List all active services
SELECT 'Active Services' as Info;
SELECT id, service_name, service_type, price, status
FROM services
WHERE status = 'active'
ORDER BY service_name
LIMIT 10;

-- Sample: Add missing columns to users table if needed
-- Uncomment and run if role column doesn't exist:
-- ALTER TABLE users ADD COLUMN role ENUM('admin', 'customer') DEFAULT 'customer' AFTER phone;
-- ALTER TABLE users ADD COLUMN status ENUM('active', 'inactive', 'suspended') DEFAULT 'active' AFTER role;

-- Sample: Create test customer account
-- Uncomment to create:
-- INSERT INTO users (username, email, password, fullname, phone, role, status) 
-- VALUES (
--     'customer',
--     'customer@uphocare.com',
--     '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
--     'Test Customer',
--     '09123456789',
--     'customer',
--     'active'
-- );

SELECT '=== Database Verification Complete ===' as Result;

