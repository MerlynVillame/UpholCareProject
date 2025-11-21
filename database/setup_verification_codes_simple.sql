-- ============================================================================
-- Simple Setup: Admin Verification Codes System
-- This script creates the table and populates it with codes 1000-9999
-- Use this if the complete script has issues
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

-- ============================================================================
-- STEP 2: Populate with codes 1000-9999 using a simple loop
-- ============================================================================

-- Use a stored procedure to populate codes
DELIMITER $$

DROP PROCEDURE IF EXISTS populate_codes$$

CREATE PROCEDURE populate_codes()
BEGIN
    DECLARE i INT DEFAULT 1000;
    DECLARE code_str VARCHAR(10);
    
    WHILE i <= 9999 DO
        SET code_str = LPAD(i, 4, '0');
        
        INSERT IGNORE INTO admin_verification_codes (verification_code, status)
        VALUES (code_str, 'available');
        
        SET i = i + 1;
    END WHILE;
END$$

DELIMITER ;

-- Execute the procedure
CALL populate_codes();

-- Drop the procedure
DROP PROCEDURE IF EXISTS populate_codes;

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

SELECT '✅ Admin Verification Codes System Setup Complete!' AS message;
SELECT '✅ 9000 verification codes (1000-9999) are now available!' AS info;

