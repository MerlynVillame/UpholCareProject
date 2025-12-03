-- EASY Script to Remove Booking Numbers - Just Copy and Paste This!
-- This automatically finds and drops everything
-- Run this entire script in phpMyAdmin SQL tab for db_upholcare database

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

SET @sql = IF(@constraint_name IS NOT NULL,
    CONCAT('ALTER TABLE `bookings` DROP FOREIGN KEY `', @constraint_name, '`'),
    'SELECT "No foreign key found - may already be removed" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Drop the index (ignore error if doesn't exist)
SET @sql = 'ALTER TABLE `bookings` DROP INDEX `booking_number_id`';
PREPARE stmt FROM @sql;
BEGIN
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
    EXECUTE stmt;
END;
DEALLOCATE PREPARE stmt;

-- Step 3: Remove the booking_number_id column
ALTER TABLE `bookings` DROP COLUMN `booking_number_id`;

-- Step 4: Try to remove customer_booking_number_id (ignore error if doesn't exist)
SET @sql = 'ALTER TABLE `bookings` DROP COLUMN `customer_booking_number_id`';
PREPARE stmt FROM @sql;
BEGIN
    DECLARE CONTINUE HANDLER FOR SQLEXCEPTION BEGIN END;
    EXECUTE stmt;
END;
DEALLOCATE PREPARE stmt;

-- Step 5: Drop the booking_numbers table
DROP TABLE IF EXISTS `booking_numbers`;

-- Step 6: Drop customer_booking_numbers table
DROP TABLE IF EXISTS `customer_booking_numbers`;

-- Done! Verify:
SELECT 'SUCCESS! Booking numbers removed.' as result;
SHOW COLUMNS FROM `bookings` LIKE '%booking%';

