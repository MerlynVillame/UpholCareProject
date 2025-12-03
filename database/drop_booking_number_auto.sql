-- Automatic Script to Remove Booking Numbers
-- This script automatically finds and drops the foreign key constraint
-- Run this in phpMyAdmin SQL tab for db_upholcare database

USE db_upholcare;

-- Step 1: Automatically find and drop the foreign key constraint
SET @constraint_name = (
    SELECT CONSTRAINT_NAME 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = 'db_upholcare'
    AND TABLE_NAME = 'bookings' 
    AND COLUMN_NAME = 'booking_number_id'
    AND REFERENCED_TABLE_NAME = 'booking_numbers'
    LIMIT 1
);

-- Drop the foreign key if it exists
SET @sql = IF(@constraint_name IS NOT NULL,
    CONCAT('ALTER TABLE `bookings` DROP FOREIGN KEY `', @constraint_name, '`'),
    'SELECT "No foreign key constraint found - may already be removed" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Drop the index (handle error if it doesn't exist)
SET @sql = 'ALTER TABLE `bookings` DROP INDEX `booking_number_id`';
SET @error_occurred = 0;

PREPARE stmt FROM @sql;
BEGIN
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET @error_occurred = 1;
    EXECUTE stmt;
END;
DEALLOCATE PREPARE stmt;

-- Step 3: Remove the booking_number_id column
ALTER TABLE `bookings` DROP COLUMN `booking_number_id`;

-- Step 4: Try to remove customer_booking_number_id (ignore error if doesn't exist)
SET @sql = 'ALTER TABLE `bookings` DROP COLUMN `customer_booking_number_id`';
SET @error_occurred = 0;

PREPARE stmt FROM @sql;
BEGIN
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET @error_occurred = 1;
    EXECUTE stmt;
END;
DEALLOCATE PREPARE stmt;

-- Step 5: Drop the booking_numbers table
DROP TABLE IF EXISTS `booking_numbers`;

-- Step 6: Drop customer_booking_numbers table
DROP TABLE IF EXISTS `customer_booking_numbers`;

-- Step 7: Verify removal
SELECT 'Migration completed! Checking remaining booking-related columns...' as status;
SHOW COLUMNS FROM `bookings` LIKE '%booking%';

