-- ============================================
-- REMOVE UNUSED STATUSES FROM bookings TABLE
-- ============================================
-- Remove: for_quotation, approved, in_queue, in_progress, start_repair, rejected
-- Run this SQL in phpMyAdmin or MySQL client

USE db_upholcare;

-- First, check if any bookings have these statuses that need to be migrated
SELECT status, COUNT(*) as count 
FROM bookings 
WHERE status IN ('for_quotation', 'approved', 'in_queue', 'in_progress', 'start_repair', 'rejected')
GROUP BY status;

-- Migrate existing bookings with these statuses to appropriate alternatives:
-- 'for_quotation' -> 'to_inspect' (waiting for inspection/quote)
-- 'approved' -> 'pending' (back to pending)
-- 'in_queue' -> 'pending' (back to pending)
-- 'in_progress' -> 'under_repair' (similar status)
-- 'start_repair' -> 'under_repair' (repair has started)
-- 'rejected' -> 'cancelled' (similar to cancelled)

UPDATE bookings SET status = 'to_inspect' WHERE status = 'for_quotation';
UPDATE bookings SET status = 'pending' WHERE status = 'approved';
UPDATE bookings SET status = 'pending' WHERE status = 'in_queue';
UPDATE bookings SET status = 'under_repair' WHERE status = 'in_progress';
UPDATE bookings SET status = 'under_repair' WHERE status = 'start_repair';
UPDATE bookings SET status = 'cancelled' WHERE status = 'rejected';

-- Now remove these statuses from the ENUM
ALTER TABLE `bookings` 
MODIFY COLUMN `status` ENUM(
    'pending', 
    'for_pickup', 
    'picked_up', 
    'for_inspection', 
    'to_inspect',
    'preview_receipt_sent',
    'under_repair', 
    'for_quality_check', 
    'ready_for_pickup',
    'out_for_delivery', 
    'completed', 
    'paid', 
    'closed', 
    'cancelled', 
    'confirmed', 
    'accepted', 
    'declined', 
    'admin_review',
    'repair_completed',
    'repair_completed_ready_to_deliver',
    'delivered_and_paid'
) DEFAULT 'pending';

-- Verify the change
SELECT COLUMN_TYPE 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'db_upholcare' 
AND TABLE_NAME = 'bookings' 
AND COLUMN_NAME = 'status';

-- Verify no bookings have the removed statuses
SELECT status, COUNT(*) as count 
FROM bookings 
WHERE status IN ('for_quotation', 'approved', 'in_queue', 'in_progress', 'start_repair', 'rejected')
GROUP BY status;

