-- ============================================================================
-- Complete Setup: Admin Verification Codes System
-- This script creates the table and populates it with codes 1000-9999
-- Run this script to set up the complete verification codes system
-- ============================================================================

USE db_upholcare;

-- ============================================================================
-- STEP 1: Create the admin_verification_codes table
-- ============================================================================
CREATE TABLE IF NOT EXISTS admin_verification_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    verification_code VARCHAR(10) NOT NULL UNIQUE COMMENT '4-digit code from 1000-9999',
    status ENUM('available', 'reserved', 'used', 'expired') DEFAULT 'available' COMMENT 'Status of the code',
    admin_registration_id INT NULL COMMENT 'Link to admin_registrations table',
    assigned_to_email VARCHAR(191) NULL COMMENT 'Email of admin who received this code',
    assigned_to_name VARCHAR(100) NULL COMMENT 'Name of admin who received this code',
    assigned_by_super_admin_id INT NULL COMMENT 'Super admin who assigned this code',
    assigned_at TIMESTAMP NULL COMMENT 'When code was assigned',
    expires_at TIMESTAMP NULL COMMENT 'When code expires (7 days from assignment)',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_verification_code (verification_code),
    INDEX idx_status (status),
    INDEX idx_admin_registration_id (admin_registration_id),
    INDEX idx_assigned_to_email (assigned_to_email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add foreign key if admin_registrations table exists
-- (This might fail if table doesn't exist yet, that's okay)
SET @fk_exists = (
    SELECT COUNT(*) 
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'admin_verification_codes' 
    AND CONSTRAINT_NAME = 'admin_verification_codes_ibfk_1'
);

SET @fk_sql = IF(@fk_exists = 0,
    'ALTER TABLE admin_verification_codes ADD CONSTRAINT admin_verification_codes_ibfk_1 FOREIGN KEY (admin_registration_id) REFERENCES admin_registrations(id) ON DELETE SET NULL',
    'SELECT "Foreign key already exists"'
);

PREPARE stmt FROM @fk_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- ============================================================================
-- STEP 2: Populate with codes 1000-9999
-- ============================================================================

-- Clear existing available codes (optional - comment out if you want to keep existing)
-- DELETE FROM admin_verification_codes WHERE status = 'available';

-- Create a numbers table approach (works in most MySQL versions)
INSERT IGNORE INTO admin_verification_codes (verification_code, status)
SELECT 
    LPAD(1000 + numbers.n, 4, '0') as verification_code,
    'available' as status
FROM (
    SELECT 
        (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a)) AS n
    FROM 
        (SELECT 0 AS a UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS a
        CROSS JOIN 
        (SELECT 0 AS a UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS b
        CROSS JOIN 
        (SELECT 0 AS a UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS c
        CROSS JOIN 
        (SELECT 0 AS a UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) AS d
    WHERE (a.a + (10 * b.a) + (100 * c.a) + (1000 * d.a)) <= 8999
) AS numbers;

-- ============================================================================
-- STEP 3: Verify the setup
-- ============================================================================

SELECT 
    'Setup Complete' AS status,
    COUNT(*) as total_codes,
    SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_codes,
    MIN(verification_code) as first_code,
    MAX(verification_code) as last_code
FROM admin_verification_codes;

-- Show sample codes
SELECT 'Sample Codes (First 10):' AS info;
SELECT verification_code, status 
FROM admin_verification_codes 
ORDER BY verification_code 
LIMIT 10;

SELECT 'Sample Codes (Last 10):' AS info;
SELECT verification_code, status 
FROM admin_verification_codes 
ORDER BY verification_code DESC 
LIMIT 10;

SELECT '✅ Admin Verification Codes System Setup Complete!' AS message;
SELECT '✅ 9000 verification codes (1000-9999) are now available!' AS info;

