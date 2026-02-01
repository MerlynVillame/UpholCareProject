-- Add repair workflow columns and statuses
-- This supports the automatic repair completion workflow

USE db_upholcare;

-- Add new statuses to the ENUM
ALTER TABLE `bookings` 
MODIFY COLUMN `status` ENUM(
    'pending',
    'for_pickup',
    'picked_up',
    'to_inspect',
    'for_inspection',
    'for_repair',
    'for_quotation',
    'approved',
    'in_queue',
    'in_progress',
    'start_repair',
    'under_repair',
    'for_quality_check',
    'repair_completed',
    'repair_completed_ready_to_deliver',
    'ready_for_pickup',
    'out_for_delivery',
    'completed',
    'paid',
    'closed',
    'cancelled',
    'confirmed',
    'accepted',
    'rejected',
    'declined',
    'admin_review'
) DEFAULT 'pending';

-- Add repair_days column to store allotted repair days
ALTER TABLE `bookings` 
ADD COLUMN IF NOT EXISTS `repair_days` INT(11) NULL DEFAULT NULL 
COMMENT 'Number of days allotted for repair work' 
AFTER `completion_date`;

-- Add repair_start_date column to track when repair actually started
ALTER TABLE `bookings` 
ADD COLUMN IF NOT EXISTS `repair_start_date` DATETIME NULL DEFAULT NULL 
COMMENT 'Date and time when repair work actually started (when status changed to start_repair or under_repair)' 
AFTER `repair_days`;

-- Create index for faster queries
CREATE INDEX IF NOT EXISTS `idx_repair_start_date` ON `bookings` (`repair_start_date`);
CREATE INDEX IF NOT EXISTS `idx_status_repair_start` ON `bookings` (`status`, `repair_start_date`);

-- Verify the changes
DESCRIBE `bookings`;

-- Show sample data
SELECT id, status, repair_days, repair_start_date, completion_date 
FROM bookings 
WHERE status IN ('start_repair', 'under_repair', 'repair_completed', 'repair_completed_ready_to_deliver')
LIMIT 5;

