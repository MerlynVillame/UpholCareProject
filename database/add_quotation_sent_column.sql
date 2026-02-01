-- ============================================
-- ADD quotation_sent COLUMN TO bookings TABLE
-- ============================================
-- Run this SQL in phpMyAdmin or MySQL client
-- This column tracks if the quotation/receipt was sent to the customer

USE db_upholcare;

-- Add quotation_sent column (if it doesn't exist, you'll get an error - that's okay)
ALTER TABLE `bookings` 
ADD COLUMN `quotation_sent` TINYINT(1) DEFAULT 0 
COMMENT 'Flag to track if quotation/receipt was sent to customer';

-- Verify the column was added
SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'db_upholcare' 
AND TABLE_NAME = 'bookings' 
AND COLUMN_NAME LIKE '%quotation%';

-- quotation_sent_at tracks the timestamp

USE db_upholcare;

-- Add quotation_sent column
-- Note: If column already exists, you'll get an error - that's okay, just ignore it
ALTER TABLE `bookings` 
ADD COLUMN `quotation_sent` TINYINT(1) DEFAULT 0 
COMMENT 'Flag to track if quotation/receipt was sent to customer';

-- Verify the change
SELECT COLUMN_NAME, DATA_TYPE, COLUMN_DEFAULT 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'db_upholcare' 
AND TABLE_NAME = 'bookings' 
AND COLUMN_NAME LIKE '%quotation%';

