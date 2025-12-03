-- Add service_option column to bookings table
-- This column stores how the service will be provided: pickup, delivery, both, or walk_in

USE db_upholcare;

-- Add service_option column if it doesn't exist
ALTER TABLE `bookings` 
ADD COLUMN `service_option` VARCHAR(50) DEFAULT 'pickup' AFTER `service_type`;

-- Verify the change
DESCRIBE `bookings`;

-- Show sample data
SELECT id, booking_number_id, service_type, service_option, status 
FROM bookings 
LIMIT 10;

