-- Simple Script to Remove Booking Numbers
-- Run this in phpMyAdmin SQL tab for db_upholcare database

USE db_upholcare;

-- STEP 1: First, find the actual foreign key constraint name
-- Run this query first to see what the constraint name is:
SELECT CONSTRAINT_NAME 
FROM information_schema.KEY_COLUMN_USAGE 
WHERE TABLE_SCHEMA = 'db_upholcare'
AND TABLE_NAME = 'bookings' 
AND COLUMN_NAME = 'booking_number_id'
AND REFERENCED_TABLE_NAME = 'booking_numbers';

-- STEP 2: After you see the constraint name from Step 1, 
-- replace 'YOUR_CONSTRAINT_NAME' below with the actual name
-- Common names: bookings_ibfk_3, bookings_booking_number_fk, bookings_ibfk_1
-- Then run this:
ALTER TABLE `bookings` DROP FOREIGN KEY `bookings_ibfk_3`;

-- STEP 3: Drop the index
ALTER TABLE `bookings` DROP INDEX `booking_number_id`;

-- STEP 4: Remove the booking_number_id column
ALTER TABLE `bookings` DROP COLUMN `booking_number_id`;

-- STEP 5: Remove customer_booking_number_id (may error if doesn't exist - that's okay)
ALTER TABLE `bookings` DROP COLUMN `customer_booking_number_id`;

-- STEP 6: Drop the booking_numbers table
DROP TABLE IF EXISTS `booking_numbers`;

-- STEP 7: Drop customer_booking_numbers table
DROP TABLE IF EXISTS `customer_booking_numbers`;

-- STEP 8: Verify removal
SHOW COLUMNS FROM `bookings` LIKE '%booking%';

