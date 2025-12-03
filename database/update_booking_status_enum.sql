-- Update bookings table status ENUM to include all workflow statuses
-- This fixes the issue where 'approved' and other statuses are rejected by MySQL

USE db_upholcare;

-- First, check current status values to see what needs to be preserved
-- Then update the ENUM to include all required statuses

ALTER TABLE `bookings` 
MODIFY COLUMN `status` ENUM(
    'pending',
    'approved',
    'in_queue',
    'under_repair',
    'for_quality_check',
    'ready_for_pickup',
    'out_for_delivery',
    'completed',
    'cancelled',
    -- Legacy statuses for backward compatibility
    'confirmed',
    'in_progress',
    'accepted',
    'rejected',
    'declined'
) DEFAULT 'pending';

-- Verify the change
DESCRIBE `bookings`;

-- Show current status distribution
SELECT status, COUNT(*) as count FROM bookings GROUP BY status;

