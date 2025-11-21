-- ============================================================================
-- Add Business Name Field to admin_registrations Table
-- Quick fix - Run this in phpMyAdmin SQL tab
-- ============================================================================

USE db_upholcare;

-- Add business_name column after phone column
ALTER TABLE `admin_registrations` 
ADD COLUMN `business_name` VARCHAR(255) NULL AFTER `phone`;

-- Verify the column was added
DESCRIBE admin_registrations;

-- Show success message
SELECT '✅ Business name column added successfully!' AS status;

