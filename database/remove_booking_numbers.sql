-- Remove Booking Numbers from Database
-- This script removes all booking number related columns and tables
-- since the system now uses availability based on fabric/color stock and store capacity

USE db_upholcare;

-- Step 1: Drop foreign key constraints related to booking_number_id
-- Check and drop foreign key constraint on bookings table
SET @constraint_name = (
    SELECT CONSTRAINT_NAME 
    FROM information_schema.KEY_COLUMN_USAGE 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'bookings' 
    AND COLUMN_NAME = 'booking_number_id'
    AND REFERENCED_TABLE_NAME = 'booking_numbers'
    LIMIT 1
);

SET @sql = IF(@constraint_name IS NOT NULL, 
    CONCAT('ALTER TABLE `bookings` DROP FOREIGN KEY `', @constraint_name, '`'),
    'SELECT "No foreign key constraint found for booking_number_id" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 2: Drop index on booking_number_id if it exists
ALTER TABLE `bookings` DROP INDEX IF EXISTS `booking_number_id`;
ALTER TABLE `bookings` DROP INDEX IF EXISTS `idx_booking_number_id`;

-- Step 3: Remove booking_number_id column from bookings table
ALTER TABLE `bookings` DROP COLUMN IF EXISTS `booking_number_id`;

-- Step 4: Remove customer_booking_number_id from bookings table (if exists and unused)
ALTER TABLE `bookings` DROP INDEX IF EXISTS `customer_booking_number_id`;
ALTER TABLE `bookings` DROP COLUMN IF EXISTS `customer_booking_number_id`;

-- Step 5: Check if repair_items table uses customer_booking_number_id
-- If it does, we'll keep that table but remove the column from bookings
-- (repair_items may still need it for repair workflow)

-- Step 6: Drop booking_numbers table (no longer needed)
DROP TABLE IF EXISTS `booking_numbers`;

-- Step 7: Drop customer_booking_numbers table (no longer needed)
-- Note: Only drop if repair_items doesn't reference it
-- Check if repair_items has customer_booking_number_id column
SET @repair_has_column = (
    SELECT COUNT(*) 
    FROM information_schema.COLUMNS 
    WHERE TABLE_SCHEMA = DATABASE() 
    AND TABLE_NAME = 'repair_items' 
    AND COLUMN_NAME = 'customer_booking_number_id'
);

-- If repair_items doesn't use customer_booking_number_id, drop the table
SET @sql = IF(@repair_has_column = 0,
    'DROP TABLE IF EXISTS `customer_booking_numbers`',
    'SELECT "Keeping customer_booking_numbers table - repair_items still uses it" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 8: Verify removal
SELECT 'Booking number columns and tables removed successfully!' as status;
SELECT 'Remaining columns in bookings table:' as info;
SHOW COLUMNS FROM `bookings` LIKE '%booking%';

