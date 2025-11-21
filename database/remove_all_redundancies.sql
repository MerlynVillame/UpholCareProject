-- ============================================================================
-- REMOVE ALL DATABASE REDUNDANCIES
-- ============================================================================
-- Database: db_upholcare
-- Date: November 18, 2025
-- 
-- This script removes redundant and unused columns from the bookings table
-- 
-- IMPORTANT: 
-- 1. BACKUP YOUR DATABASE BEFORE RUNNING THIS!
-- 2. Test in development environment first
-- 3. Review verification queries below before executing
--
-- ============================================================================

USE db_upholcare;

-- ============================================================================
-- STEP 1: VERIFICATION QUERIES (Run these first to verify safety)
-- ============================================================================

-- Check if customer_id matches user_id (should return 0 mismatches)
SELECT 'Checking customer_id vs user_id...' as step;
SELECT COUNT(*) as mismatched_records 
FROM bookings 
WHERE COALESCE(user_id, 0) != COALESCE(customer_id, 0);

-- Check if customer_booking_number_id is ever used (should return 0)
SELECT 'Checking customer_booking_number_id usage...' as step;
SELECT COUNT(*) as records_with_data 
FROM bookings 
WHERE customer_booking_number_id IS NOT NULL;

-- Check if repair_item_id is ever used (should return 0)
SELECT 'Checking repair_item_id usage...' as step;
SELECT COUNT(*) as records_with_data 
FROM bookings 
WHERE repair_item_id IS NOT NULL;

-- Show current bookings table structure
SELECT 'Current bookings table structure:' as step;
DESCRIBE bookings;

-- ============================================================================
-- STEP 2: SHOW WHAT WILL BE REMOVED
-- ============================================================================

SELECT '============================================================================' as separator;
SELECT 'THE FOLLOWING COLUMNS WILL BE REMOVED:' as notice;
SELECT '1. customer_id - Redundant (duplicates user_id)' as removal_1;
SELECT '2. customer_booking_number_id - Unused (never implemented)' as removal_2;
SELECT '3. repair_item_id - Unused (never implemented)' as removal_3;
SELECT '============================================================================' as separator;

-- Pause here and review the verification results above
-- If everything looks good, continue with the removal below

-- ============================================================================
-- STEP 3: REMOVE REDUNDANT COLUMNS
-- ============================================================================

-- Remove customer_id (redundant - duplicates user_id)
SELECT 'Removing redundant customer_id column...' as step;
ALTER TABLE bookings DROP INDEX IF EXISTS idx_customer_id;
ALTER TABLE bookings DROP COLUMN IF EXISTS customer_id;

-- Remove customer_booking_number_id (unused - never implemented)
SELECT 'Removing unused customer_booking_number_id column...' as step;
ALTER TABLE bookings DROP INDEX IF EXISTS customer_booking_number_id;
ALTER TABLE bookings DROP COLUMN IF EXISTS customer_booking_number_id;

-- Remove repair_item_id (unused - never implemented)  
SELECT 'Removing unused repair_item_id column...' as step;
ALTER TABLE bookings DROP INDEX IF EXISTS repair_item_id;
ALTER TABLE bookings DROP COLUMN IF EXISTS repair_item_id;

-- ============================================================================
-- STEP 4: VERIFY REMOVAL
-- ============================================================================

SELECT '============================================================================' as separator;
SELECT 'REDUNDANCIES REMOVED SUCCESSFULLY!' as success;
SELECT '============================================================================' as separator;

-- Show updated table structure
SELECT 'Updated bookings table structure:' as step;
DESCRIBE bookings;

-- Show remaining columns
SELECT 'Remaining booking-related columns:' as info;
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_KEY,
    COLUMN_DEFAULT,
    EXTRA
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'db_upholcare' 
  AND TABLE_NAME = 'bookings'
ORDER BY ORDINAL_POSITION;

-- Count total bookings to ensure nothing was lost
SELECT 'Total bookings in database:' as info;
SELECT COUNT(*) as total_bookings FROM bookings;

-- ============================================================================
-- STEP 5: CLEANUP COMPLETE
-- ============================================================================

SELECT '============================================================================' as separator;
SELECT 'CLEANUP COMPLETE!' as status;
SELECT 'Database redundancies have been removed.' as message;
SELECT 'Benefits: Smaller database, faster queries, cleaner schema' as benefits;
SELECT '============================================================================' as separator;

-- ============================================================================
-- ROLLBACK SCRIPT (If needed)
-- ============================================================================
/*
-- If you need to restore the removed columns, run this:

USE db_upholcare;

-- Restore customer_id column
ALTER TABLE bookings 
ADD COLUMN customer_id INT(11) NULL AFTER user_id,
ADD INDEX idx_customer_id (customer_id);

-- Sync customer_id with user_id
UPDATE bookings SET customer_id = user_id WHERE user_id IS NOT NULL;

-- Restore customer_booking_number_id column  
ALTER TABLE bookings 
ADD COLUMN customer_booking_number_id INT(11) NULL AFTER booking_number_id,
ADD INDEX customer_booking_number_id (customer_booking_number_id);

-- Restore repair_item_id column
ALTER TABLE bookings 
ADD COLUMN repair_item_id INT(11) NULL AFTER service_id,
ADD INDEX repair_item_id (repair_item_id);

SELECT 'Columns restored' as status;
*/

