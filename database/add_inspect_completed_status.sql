-- ============================================
-- ADD 'inspect_completed' STATUS TO bookings TABLE
-- ============================================
-- This status is set when admin sends preview receipt to customer
-- Run this SQL in phpMyAdmin or MySQL client

USE db_upholcare;

-- Add 'inspect_completed' status to the ENUM
ALTER TABLE `bookings` 
MODIFY COLUMN `status` ENUM(
    'pending', 
    'for_pickup', 
    'picked_up', 
    'for_inspection', 
    'to_inspect',
    'inspect_completed',  -- NEW STATUS: Set when preview receipt is sent
    'preview_receipt_sent',  -- Keep for backward compatibility
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

