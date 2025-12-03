-- Add new statuses for inspection workflow
-- Statuses: 'to_inspect', 'for_repair'
-- This supports the complete inspection-based pricing workflow

USE db_upholcare;

-- Update the status ENUM to include new inspection workflow statuses
ALTER TABLE `bookings` 
MODIFY COLUMN `status` ENUM(
    'pending',
    'approved',
    'for_pickup',
    'picked_up',
    'to_inspect',
    'for_inspection',
    'for_quotation',
    'for_repair',
    'in_queue',
    'in_progress',
    'under_repair',
    'for_quality_check',
    'ready_for_pickup',
    'out_for_delivery',
    'completed',
    'paid',
    'closed',
    'cancelled',
    -- Legacy statuses for backward compatibility
    'confirmed',
    'accepted',
    'rejected',
    'declined',
    'admin_review'
) DEFAULT 'pending';

-- Verify the change
DESCRIBE `bookings`;

-- Show current status distribution
SELECT status, COUNT(*) as count FROM bookings GROUP BY status ORDER BY count DESC;

