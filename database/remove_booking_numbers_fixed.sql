-- Remove Booking Numbers from Database - Fixed Version
-- This script properly removes booking number related columns and tables
-- Run this in phpMyAdmin SQL tab

USE db_upholcare;

-- Step 1: Find and drop the foreign key constraint first
-- The constraint name is likely 'bookings_ibfk_3' or 'bookings_booking_number_fk'
SET @constraint_name = NULL;

-- Try to find the constraint name
SELECT CONSTRAINT_NAME INTO @constraint_name
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'db_upholcare'
AND TABLE_NAME = 'bookings' 
AND COLUMN_NAME = 'booking_number_id'
AND REFERENCED_TABLE_NAME = 'booking_numbers'
LIMIT 1;

-- Drop the foreign key constraint if it exists
SET @sql = IF(@constraint_name IS NOT NULL,
    CONCAT('ALTER TABLE `bookings` DROP FOREIGN KEY `', @constraint_name, '`'),
    'SELECT "No foreign key constraint found" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Now drop the index (after foreign key is removed)
ALTER TABLE `bookings` DROP INDEX IF EXISTS `booking_number_id`;
ALTER TABLE `bookings` DROP INDEX IF EXISTS `idx_booking_number_id`;
ALTER TABLE `bookings` DROP INDEX IF EXISTS `bookings_ibfk_3`;

-- Step 3: Remove booking_number_id column from bookings table
ALTER TABLE `bookings` DROP COLUMN `booking_number_id`;

-- Step 4: Remove customer_booking_number_id column if it exists
ALTER TABLE `bookings` DROP INDEX IF EXISTS `customer_booking_number_id`;
ALTER TABLE `bookings` DROP COLUMN IF EXISTS `customer_booking_number_id`;

-- Step 5: Drop the booking_numbers table
DROP TABLE IF EXISTS `booking_numbers`;

-- Step 6: Check if repair_items uses customer_booking_number_id
-- If repair_items doesn't use it, drop customer_booking_numbers table
SET @repair_has_column = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = 'db_upholcare'
    AND TABLE_NAME = 'repair_items' 
    AND COLUMN_NAME = 'customer_booking_number_id'
);

-- Drop customer_booking_numbers only if repair_items doesn't use it
SET @sql = IF(@repair_has_column = 0,
    'DROP TABLE IF EXISTS `customer_booking_numbers`',
    'SELECT "Keeping customer_booking_numbers - repair_items uses it" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 7: Verify removal
SELECT 'Migration completed!' as status;
SELECT 'Checking remaining booking-related columns...' as info;
SHOW COLUMNS FROM `bookings` LIKE '%booking%';

