-- Migration: Add completion_date to bookings table
-- Purpose: Track exact date/time when bookings are completed for accurate reporting
-- Date: 2025-11-30

-- Add completion_date column if it doesn't exist
ALTER TABLE `bookings` 
ADD COLUMN IF NOT EXISTS `completion_date` DATETIME NULL DEFAULT NULL 
COMMENT 'Date and time when booking was marked as completed' 
AFTER `updated_at`;

-- Create index for faster querying in reports
CREATE INDEX IF NOT EXISTS `idx_completion_date` ON `bookings` (`completion_date`);

-- Create composite index for report queries
CREATE INDEX IF NOT EXISTS `idx_status_payment_completion` 
ON `bookings` (`status`, `payment_status`, `completion_date`);

-- Update existing completed bookings to set completion_date from updated_at
UPDATE `bookings` 
SET `completion_date` = COALESCE(`updated_at`, `created_at`)
WHERE `status` = 'completed' 
AND `payment_status` IN ('paid', 'paid_full_cash', 'paid_on_delivery_cod')
AND `completion_date` IS NULL;

-- Show results
SELECT 
    COUNT(*) as total_completed_bookings,
    COUNT(completion_date) as bookings_with_completion_date,
    COUNT(*) - COUNT(completion_date) as bookings_without_completion_date
FROM `bookings`
WHERE `status` = 'completed';

