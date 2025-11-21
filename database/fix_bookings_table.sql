-- Fix Bookings Table - Add Missing Columns
-- Run this SQL to add the missing columns that the application expects

USE db_upholcare;

-- Add missing columns to bookings table
ALTER TABLE `bookings` 
ADD COLUMN IF NOT EXISTS `customer_id` INT(11) DEFAULT NULL AFTER `user_id`,
ADD COLUMN IF NOT EXISTS `booking_number_id` INT(11) DEFAULT NULL AFTER `customer_id`,
ADD COLUMN IF NOT EXISTS `store_location_id` INT(11) DEFAULT NULL AFTER `booking_number_id`,
ADD COLUMN IF NOT EXISTS `service_type` VARCHAR(50) DEFAULT NULL AFTER `store_location_id`,
ADD COLUMN IF NOT EXISTS `item_description` TEXT DEFAULT NULL AFTER `service_type`,
ADD COLUMN IF NOT EXISTS `item_type` VARCHAR(100) DEFAULT NULL AFTER `item_description`,
ADD COLUMN IF NOT EXISTS `pickup_date` DATE DEFAULT NULL AFTER `item_type`,
ADD COLUMN IF NOT EXISTS `total_amount` DECIMAL(10,2) DEFAULT 0.00 AFTER `pickup_date`,
ADD COLUMN IF NOT EXISTS `payment_status` ENUM('unpaid', 'partial', 'paid') DEFAULT 'unpaid' AFTER `total_amount`,
ADD COLUMN IF NOT EXISTS `booking_type` ENUM('personal', 'business', 'business_reservation') DEFAULT 'personal' AFTER `payment_status`,
ADD COLUMN IF NOT EXISTS `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- If customer_id column was just added, copy data from user_id for existing records
UPDATE `bookings` SET `customer_id` = `user_id` WHERE `customer_id` IS NULL AND `user_id` IS NOT NULL;

-- Add indexes for better performance
ALTER TABLE `bookings`
ADD INDEX IF NOT EXISTS `idx_customer_id` (`customer_id`),
ADD INDEX IF NOT EXISTS `idx_booking_number_id` (`booking_number_id`),
ADD INDEX IF NOT EXISTS `idx_payment_status` (`payment_status`),
ADD INDEX IF NOT EXISTS `idx_booking_type` (`booking_type`);

-- Show the updated table structure
DESCRIBE `bookings`;

