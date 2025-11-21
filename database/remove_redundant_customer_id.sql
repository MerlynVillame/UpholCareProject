-- Remove Redundant customer_id Column from bookings Table
-- The system uses user_id throughout, customer_id is redundant
-- 
-- IMPORTANT: Backup your database before running this script!
--
-- Run this script to remove the redundant customer_id column

USE db_upholcare;

-- Step 1: Verify that customer_id and user_id have the same values (for safety check)
-- This query should return 0 if all values match or both are NULL
SELECT COUNT(*) as mismatched_records 
FROM bookings 
WHERE COALESCE(user_id, 0) != COALESCE(customer_id, 0);

-- Step 2: Drop the index on customer_id if it exists
ALTER TABLE bookings 
DROP INDEX IF EXISTS idx_customer_id;

-- Step 3: Remove the redundant customer_id column
ALTER TABLE bookings 
DROP COLUMN IF EXISTS customer_id;

-- Step 4: Verify the table structure
DESCRIBE bookings;

-- Success! The redundant customer_id column has been removed.
-- The system will continue to work normally using user_id.

