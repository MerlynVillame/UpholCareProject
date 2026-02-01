-- ============================================
-- ADD quotation_accepted COLUMNS TO bookings TABLE
-- ============================================
-- Run this SQL in phpMyAdmin or MySQL client

USE db_upholcare;

-- Add quotation_accepted column (if it doesn't exist)
ALTER TABLE `bookings` 
ADD COLUMN IF NOT EXISTS `quotation_accepted` TINYINT(1) DEFAULT 0 
COMMENT 'Flag to track if quotation/receipt was accepted by customer';

-- Add quotation_accepted_at column (if it doesn't exist)
ALTER TABLE `bookings` 
ADD COLUMN IF NOT EXISTS `quotation_accepted_at` DATETIME NULL DEFAULT NULL 
COMMENT 'Timestamp when customer accepted the quotation/receipt';

-- Verify the columns were added
SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'db_upholcare' 
AND TABLE_NAME = 'bookings' 
AND COLUMN_NAME LIKE '%quotation%';

