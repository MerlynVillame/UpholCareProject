-- Fix booking_number_id column to allow NULL values
-- This is necessary because booking numbers are assigned by admin AFTER booking creation
-- 
-- Problem: The booking_number_id column was set to NOT NULL, causing errors when creating new bookings
-- Solution: Change the column to allow NULL values and add proper foreign key constraint
--
-- Date: 2025-01-15
-- 

USE db_upholcare;

-- Step 1: Modify booking_number_id column to allow NULL values
ALTER TABLE `bookings` 
MODIFY COLUMN `booking_number_id` INT(11) NULL;

-- Step 2: Drop existing foreign key constraint if it exists (with wrong DELETE rule)
SET @constraint_exists = (
    SELECT COUNT(*)
    FROM information_schema.TABLE_CONSTRAINTS
    WHERE CONSTRAINT_SCHEMA = 'db_upholcare'
    AND TABLE_NAME = 'bookings'
    AND CONSTRAINT_NAME = 'bookings_ibfk_3'
);

-- Only drop if exists
SET @drop_sql = IF(@constraint_exists > 0, 
    'ALTER TABLE `bookings` DROP FOREIGN KEY `bookings_ibfk_3`',
    'SELECT "Constraint does not exist, skipping drop" AS message'
);

PREPARE stmt FROM @drop_sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Step 3: Create foreign key constraint with proper ON DELETE SET NULL
ALTER TABLE `bookings`
ADD CONSTRAINT `bookings_ibfk_3` 
FOREIGN KEY (`booking_number_id`) 
REFERENCES `booking_numbers` (`id`) 
ON DELETE SET NULL;

-- Step 4: Verify the changes
SELECT 'Column modification complete' AS status;
DESCRIBE `bookings`;

SELECT 
    CONSTRAINT_NAME, 
    TABLE_NAME, 
    REFERENCED_TABLE_NAME,
    DELETE_RULE
FROM information_schema.REFERENTIAL_CONSTRAINTS
WHERE CONSTRAINT_SCHEMA = 'db_upholcare'
AND TABLE_NAME = 'bookings';

