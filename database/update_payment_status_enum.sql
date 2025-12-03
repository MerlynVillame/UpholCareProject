-- Update payment_status ENUM to support new payment types
-- Remove 'partial' and add 'paid_full_cash' and 'paid_on_delivery_cod'

USE db_upholcare;

-- First, update any existing 'partial' values to 'unpaid' (since we're removing partial)
UPDATE bookings SET payment_status = 'unpaid' WHERE payment_status = 'partial';

-- For MariaDB/MySQL, we need to modify the ENUM
-- Check current column type first
ALTER TABLE bookings 
MODIFY COLUMN payment_status ENUM('unpaid', 'paid', 'paid_full_cash', 'paid_on_delivery_cod', 'refunded', 'failed', 'cancelled') 
DEFAULT 'unpaid';

-- Update any old 'paid' values to 'paid_full_cash' for consistency
UPDATE bookings SET payment_status = 'paid_full_cash' WHERE payment_status = 'paid';

-- Verify the update
DESCRIBE bookings;

