-- Add quotation_sent_at field to track when quotation email was sent after inspection
-- This is used for PICKUP service option workflow

USE db_upholcare;

-- Add quotation_sent_at column to bookings table
-- Place it after updated_at if preview_sent_at doesn't exist
ALTER TABLE `bookings` 
ADD COLUMN `quotation_sent_at` DATETIME NULL DEFAULT NULL;

-- Verify the change
DESCRIBE `bookings`;

-- Show bookings that have quotation_sent_at set
SELECT id, booking_number_id, service_option, status, quotation_sent_at 
FROM bookings 
WHERE quotation_sent_at IS NOT NULL
ORDER BY quotation_sent_at DESC;

