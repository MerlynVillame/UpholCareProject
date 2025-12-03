-- Add pickup/delivery address and date columns to bookings table
-- These columns are essential for the service option workflow

USE db_upholcare;

-- Add address and date columns
ALTER TABLE `bookings` 
ADD COLUMN `pickup_address` TEXT NULL AFTER `service_option`,
ADD COLUMN `pickup_date` DATE NULL AFTER `pickup_address`,
ADD COLUMN `delivery_address` TEXT NULL AFTER `pickup_date`,
ADD COLUMN `delivery_date` DATE NULL AFTER `delivery_address`;

-- Verify the changes
DESCRIBE `bookings`;

-- Show sample data
SELECT id, service_option, pickup_address, pickup_date, delivery_address, delivery_date, status 
FROM bookings 
LIMIT 5;

