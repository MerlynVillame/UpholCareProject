-- Add new status options for PICKUP workflow
-- This adds statuses that support the upholstery inspection-based pricing workflow
-- 
-- Workflow for PICKUP service option:
-- 1. pending -> customer submitted booking
-- 2. for_pickup -> admin approved, waiting to collect item
-- 3. picked_up -> item collected, waiting for inspection
-- 4. for_inspection -> item being inspected for measurements/damage
-- 5. for_quotation -> inspection done, admin preparing final price
-- 6. approved -> customer approved the quotation, ready for repair
-- 7. in_progress -> technicians working on the item
-- 8. completed -> repair finished, ready for delivery/pickup
-- 9. paid -> customer paid in full
-- 10. closed -> booking completed and archived

USE db_upholcare;

-- Update the status ENUM to include all required statuses for pickup workflow
ALTER TABLE `bookings` 
MODIFY COLUMN `status` ENUM(
    'pending',
    'for_pickup',
    'picked_up',
    'for_inspection',
    'for_quotation',
    'approved',
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

