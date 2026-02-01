-- ============================================
-- ADD 'preview_receipt_sent' STATUS TO bookings TABLE
-- ============================================
-- This status indicates that the admin has sent a preview receipt to the customer
-- and is waiting for customer approval

USE db_upholcare;

-- Get current status ENUM values
SELECT COLUMN_TYPE 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'db_upholcare' 
AND TABLE_NAME = 'bookings' 
AND COLUMN_NAME = 'status';

-- Add 'preview_receipt_sent' to status ENUM
-- Note: This will add the new status to the existing ENUM
ALTER TABLE `bookings` 
MODIFY COLUMN `status` ENUM(
    'pending', 
    'for_pickup', 
    'picked_up', 
    'for_inspection', 
    'to_inspect',
    'preview_receipt_sent',
    'for_quotation', 
    'approved', 
    'in_queue', 
    'in_progress', 
    'start_repair',
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
    'rejected', 
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

