-- ============================================================================
-- Create Admin Verification Codes Table
-- Stores pre-generated verification codes (1000-9999) for admin registrations
-- ============================================================================

USE db_upholcare;

-- ============================================================================
-- TABLE: admin_verification_codes
-- Stores pre-generated verification codes that can be assigned to admin registrations
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
    INDEX idx_assigned_to_email (assigned_to_email),
    FOREIGN KEY (admin_registration_id) REFERENCES admin_registrations(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================================
-- Populate table with verification codes from 1000 to 9999
-- ============================================================================

-- Delete existing codes if table already has data (optional - comment out if you want to keep existing)
-- DELETE FROM admin_verification_codes;

-- Insert codes from 1000 to 9999
INSERT INTO admin_verification_codes (verification_code, status) 
SELECT 
    LPAD(numbers.num, 4, '0') as verification_code,
    'available' as status
FROM (
    SELECT @row := @row + 1 AS num
    FROM 
        (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
        (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
        (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
        (SELECT 0 UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4,
        (SELECT @row := 999) r
    WHERE @row < 9999
) numbers
WHERE numbers.num >= 1000
ORDER BY numbers.num;

-- Alternative method using a stored procedure (if the above doesn't work in your MySQL version)
-- This is a more compatible approach
DELIMITER $$

DROP PROCEDURE IF EXISTS populate_verification_codes$$

CREATE PROCEDURE populate_verification_codes()
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

-- Execute the stored procedure to populate codes
CALL populate_verification_codes();

-- Drop the stored procedure after use
DROP PROCEDURE IF EXISTS populate_verification_codes;

-- ============================================================================
-- Verify the data
-- ============================================================================
SELECT 
    COUNT(*) as total_codes,
    SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as available_codes,
    SUM(CASE WHEN status = 'used' THEN 1 ELSE 0 END) as used_codes,
    SUM(CASE WHEN status = 'reserved' THEN 1 ELSE 0 END) as reserved_codes
FROM admin_verification_codes;

-- Show sample codes
SELECT * FROM admin_verification_codes ORDER BY verification_code LIMIT 10;
SELECT * FROM admin_verification_codes ORDER BY verification_code DESC LIMIT 10;

SELECT '✅ Admin Verification Codes Table Created and Populated!' AS status;
SELECT '✅ 9000 verification codes (1000-9999) are now available for assignment!' AS message;

