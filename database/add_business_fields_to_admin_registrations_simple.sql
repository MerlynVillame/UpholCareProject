-- ============================================================================
-- Add Business Information Fields to admin_registrations Table
-- Simple version - Run this in phpMyAdmin or MySQL command line
-- ============================================================================

USE db_upholcare;

-- Add business information fields to admin_registrations table
-- If you get "Duplicate column" errors, the columns already exist and you can ignore them

ALTER TABLE `admin_registrations` 
ADD COLUMN `business_name` VARCHAR(255) NULL AFTER `phone`,
ADD COLUMN `business_address` TEXT NULL AFTER `business_name`,
ADD COLUMN `business_city` VARCHAR(100) NULL DEFAULT 'Bohol' AFTER `business_address`,
ADD COLUMN `business_province` VARCHAR(100) NULL DEFAULT 'Bohol' AFTER `business_city`,
ADD COLUMN `business_latitude` DECIMAL(10, 8) NULL AFTER `business_province`,
ADD COLUMN `business_longitude` DECIMAL(11, 8) NULL AFTER `business_latitude`,
ADD COLUMN `business_permit_path` VARCHAR(255) NULL COMMENT 'Path to uploaded PDF file' AFTER `business_longitude`,
ADD COLUMN `business_permit_filename` VARCHAR(255) NULL COMMENT 'Original filename of uploaded permit' AFTER `business_permit_path`;

-- Add indexes for better query performance
ALTER TABLE `admin_registrations`
ADD INDEX `idx_business_location` (`business_latitude`, `business_longitude`),
ADD INDEX `idx_business_city` (`business_city`);

-- Display table structure
DESCRIBE admin_registrations;

