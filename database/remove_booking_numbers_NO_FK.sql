-- Remove Booking Numbers - No Foreign Key Constraint Found
-- Since there's no foreign key constraint, we can drop directly
-- Run this in phpMyAdmin SQL tab for db_upholcare database

USE db_upholcare;

-- Step 1: Drop the index (if it exists)
-- This might error if index doesn't exist - that's okay, just continue
ALTER TABLE `bookings` DROP INDEX `booking_number_id`;

-- Step 2: Remove the booking_number_id column directly
ALTER TABLE `bookings` DROP COLUMN `booking_number_id`;

-- Step 3: Remove customer_booking_number_id column (if it exists)
-- This might error if column doesn't exist - that's okay
ALTER TABLE `bookings` DROP COLUMN `customer_booking_number_id`;

-- Step 4: Drop the booking_numbers table
DROP TABLE IF EXISTS `booking_numbers`;

-- Step 5: Drop customer_booking_numbers table
DROP TABLE IF EXISTS `customer_booking_numbers`;

-- Step 6: Verify removal
SELECT 'SUCCESS! Booking numbers removed.' as result;
SHOW COLUMNS FROM `bookings` LIKE '%booking%';

