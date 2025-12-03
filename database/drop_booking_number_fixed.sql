-- Fixed Script to Remove Booking Numbers
-- Run this in phpMyAdmin SQL tab for db_upholcare database
-- This script first finds the actual constraint name, then drops it

USE db_upholcare;

-- Step 1: Find the actual foreign key constraint name
-- Run this query first to see the constraint name
SELECT CONSTRAINT_NAME 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'db_upholcare'
AND TABLE_NAME = 'bookings' 
AND COLUMN_NAME = 'booking_number_id'
AND REFERENCED_TABLE_NAME = 'booking_numbers';

-- Step 2: Drop the foreign key constraint using the actual name
-- Replace 'bookings_ibfk_3' with the actual constraint name from Step 1
ALTER TABLE `bookings` DROP FOREIGN KEY `bookings_ibfk_3`;

-- Alternative: If you know the constraint name, use this directly:
-- Common names: bookings_ibfk_3, bookings_booking_number_fk, bookings_ibfk_1, etc.

-- Step 3: Drop the index (if it exists)
ALTER TABLE `bookings` DROP INDEX `booking_number_id`;

-- Step 4: Remove the booking_number_id column
ALTER TABLE `bookings` DROP COLUMN `booking_number_id`;

-- Step 5: Remove customer_booking_number_id if it exists
-- Note: MySQL doesn't support IF EXISTS for DROP COLUMN, so check first
-- If column doesn't exist, this will error - that's okay, just continue
ALTER TABLE `bookings` DROP COLUMN `customer_booking_number_id`;

-- Step 6: Drop the booking_numbers table
DROP TABLE IF EXISTS `booking_numbers`;

-- Step 7: Drop customer_booking_numbers table
DROP TABLE IF EXISTS `customer_booking_numbers`;

-- Step 8: Verify removal
SHOW COLUMNS FROM `bookings` LIKE '%booking%';

