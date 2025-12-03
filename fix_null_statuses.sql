-- Fix NULL or empty status values in bookings table
-- Run this SQL script to update existing bookings with NULL/empty status to 'pending'

-- Update bookings with NULL status
UPDATE bookings 
SET status = 'pending' 
WHERE status IS NULL OR status = '' OR TRIM(status) = '';

-- Verify the fix
SELECT id, booking_number_id, status, created_at 
FROM bookings 
WHERE status IS NULL OR status = '' OR TRIM(status) = '';

-- If the above query returns 0 rows, all statuses are fixed!

