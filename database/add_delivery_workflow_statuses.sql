-- ============================================
-- ADD DELIVERY WORKFLOW STATUSES TO bookings TABLE
-- ============================================
-- This adds statuses required for the delivery service workflow:
-- - for_dropoff: Customer must bring item to shop
-- - for_inspect: Item received, ready for inspection
-- - inspection_completed_waiting_approval: Inspection done, waiting for customer approval
-- Run this SQL in phpMyAdmin or MySQL client
--
-- IMPORTANT: This must be run before the delivery workflow can work properly!

USE db_upholcare;

-- Add delivery workflow statuses to the ENUM
-- This preserves all existing statuses and adds the new ones
ALTER TABLE `bookings` 
MODIFY COLUMN `status` ENUM(
    'pending',
    'approved',
    'for_pickup',
    'for_dropoff',  -- NEW: Customer must bring item to shop (delivery service)
    'for_inspect',  -- NEW: Item received, ready for inspection
    'picked_up',
    'to_inspect',
    'for_inspection',
    'inspect_completed',
    'inspection_completed_waiting_approval',  -- NEW: Inspection done, waiting for customer approval
    'preview_receipt_sent',
    'for_repair',
    'in_queue',
    'in_progress',
    'under_repair',
    'for_quality_check',
    'repair_completed',
    'repair_completed_ready_to_deliver',
    'ready_for_pickup',
    'out_for_delivery',
    'completed',
    'delivered_and_paid',
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
SELECT COLUMN_TYPE 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'db_upholcare' 
AND TABLE_NAME = 'bookings' 
AND COLUMN_NAME = 'status';

-- Show current status distribution
SELECT status, COUNT(*) as count 
FROM bookings 
GROUP BY status 
ORDER BY count DESC;

