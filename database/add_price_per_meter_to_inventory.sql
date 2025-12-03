-- Add price_per_meter column to inventory table
-- This column stores the price per meter for leather materials

USE db_upholcare;

-- Add price_per_meter column if it doesn't exist
ALTER TABLE `inventory`
ADD COLUMN IF NOT EXISTS `price_per_meter` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Price per meter for leather material' AFTER `premium_price`;

