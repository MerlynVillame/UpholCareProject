-- Complete fix for admin_registrations table
-- This script handles all cases: table doesn't exist, table exists but needs updates, etc.

USE db_upholcare;

-- Step 1: Check if table exists, if not create it with all fields
CREATE TABLE IF NOT EXISTS admin_registrations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    admin_id INT NULL COMMENT 'Control panel admin ID if approved',
    fullname VARCHAR(100) NOT NULL,
    email VARCHAR(191) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL COMMENT 'Hashed password',
    phone VARCHAR(20) NULL,
    registration_status ENUM('pending_verification', 'pending', 'approved', 'rejected') DEFAULT 'pending_verification',
    approved_by INT NULL COMMENT 'Super admin who approved',
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT NULL,
    verification_code VARCHAR(10) NULL COMMENT '6-digit verification code sent via email',
    verification_code_sent_at TIMESTAMP NULL COMMENT 'When verification code was sent',
    verification_code_verified_at TIMESTAMP NULL COMMENT 'When verification code was verified',
    verification_attempts INT DEFAULT 0 COMMENT 'Number of verification attempts',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (registration_status),
    INDEX idx_created_at (created_at),
    INDEX idx_email (email),
    INDEX idx_verification_code (verification_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Step 2: If table already exists, add missing columns
-- Add verification_code column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'db_upholcare' 
     AND TABLE_NAME = 'admin_registrations' 
     AND COLUMN_NAME = 'verification_code') > 0,
    'SELECT "Column verification_code already exists" AS message;',
    'ALTER TABLE admin_registrations ADD COLUMN verification_code VARCHAR(10) NULL COMMENT ''6-digit verification code sent via email'';'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add verification_code_sent_at column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'db_upholcare' 
     AND TABLE_NAME = 'admin_registrations' 
     AND COLUMN_NAME = 'verification_code_sent_at') > 0,
    'SELECT "Column verification_code_sent_at already exists" AS message;',
    'ALTER TABLE admin_registrations ADD COLUMN verification_code_sent_at TIMESTAMP NULL COMMENT ''When verification code was sent'';'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add verification_code_verified_at column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'db_upholcare' 
     AND TABLE_NAME = 'admin_registrations' 
     AND COLUMN_NAME = 'verification_code_verified_at') > 0,
    'SELECT "Column verification_code_verified_at already exists" AS message;',
    'ALTER TABLE admin_registrations ADD COLUMN verification_code_verified_at TIMESTAMP NULL COMMENT ''When verification code was verified'';'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Add verification_attempts column if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = 'db_upholcare' 
     AND TABLE_NAME = 'admin_registrations' 
     AND COLUMN_NAME = 'verification_attempts') > 0,
    'SELECT "Column verification_attempts already exists" AS message;',
    'ALTER TABLE admin_registrations ADD COLUMN verification_attempts INT DEFAULT 0 COMMENT ''Number of verification attempts'';'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 3: Update the ENUM to include 'pending_verification'
-- This is the critical fix - the ENUM must include 'pending_verification'
ALTER TABLE admin_registrations 
MODIFY COLUMN registration_status ENUM('pending_verification', 'pending', 'approved', 'rejected') DEFAULT 'pending_verification';

-- Step 4: Add index for verification_code if it doesn't exist
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS 
     WHERE TABLE_SCHEMA = 'db_upholcare' 
     AND TABLE_NAME = 'admin_registrations' 
     AND INDEX_NAME = 'idx_verification_code') > 0,
    'SELECT "Index idx_verification_code already exists" AS message;',
    'ALTER TABLE admin_registrations ADD INDEX idx_verification_code (verification_code);'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 5: Verify the table structure
SELECT '✅ admin_registrations table fixed successfully!' AS status;
SELECT 'Table structure:' AS info;
DESCRIBE admin_registrations;

