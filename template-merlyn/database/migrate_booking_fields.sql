-- Database Migration: Add missing fields to bookings table
-- This script adds the missing fields that the UphoCare system expects

-- Add booking_number_id field
ALTER TABLE `bookings` 
ADD COLUMN `booking_number_id` INT(11) DEFAULT NULL AFTER `service_id`,
ADD KEY `booking_number_id` (`booking_number_id`);

-- Add total_amount field
ALTER TABLE `bookings` 
ADD COLUMN `total_amount` DECIMAL(10,2) DEFAULT NULL AFTER `booking_date`;

-- Add payment_status field
ALTER TABLE `bookings` 
ADD COLUMN `payment_status` ENUM('unpaid','partial','paid') DEFAULT 'unpaid' AFTER `total_amount`;

-- Add updated_at field for tracking changes
ALTER TABLE `bookings` 
ADD COLUMN `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP AFTER `created_at`;

-- Create booking_numbers table if it doesn't exist
CREATE TABLE IF NOT EXISTS `booking_numbers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_number` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `booking_number` (`booking_number`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- Add foreign key constraint for booking_number_id
ALTER TABLE `bookings`
ADD CONSTRAINT `bookings_ibfk_3` FOREIGN KEY (`booking_number_id`) REFERENCES `booking_numbers` (`id`) ON DELETE SET NULL;

-- Insert some sample booking numbers
INSERT INTO `booking_numbers` (`booking_number`) VALUES
('BKG-20250115-0001'),
('BKG-20250115-0002'),
('BKG-20250115-0003'),
('BKG-20250115-0004'),
('BKG-20250115-0005'),
('BKG-20250115-0006'),
('BKG-20250115-0007'),
('BKG-20250115-0008'),
('BKG-20250115-0009'),
('BKG-20250115-0010');

-- Update existing bookings with default values
UPDATE `bookings` SET 
    `total_amount` = 0.00,
    `payment_status` = 'unpaid',
    `updated_at` = CURRENT_TIMESTAMP
WHERE `total_amount` IS NULL;

-- Add service category relationship to services table
ALTER TABLE `services` 
ADD COLUMN `category_id` INT(11) DEFAULT NULL AFTER `service_type`,
ADD KEY `category_id` (`category_id`);

-- Add foreign key constraint for service categories
ALTER TABLE `services`
ADD CONSTRAINT `services_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `service_categories` (`id`) ON DELETE SET NULL;

-- Update existing services with category IDs
UPDATE `services` SET `category_id` = 1 WHERE `service_type` = 'Vehicle Upholstery';
UPDATE `services` SET `category_id` = 2 WHERE `service_type` = 'Bedding';
UPDATE `services` SET `category_id` = 3 WHERE `service_type` = 'Furniture';

-- Add some sample bookings for testing
INSERT INTO `bookings` (`user_id`, `service_id`, `booking_number_id`, `booking_date`, `total_amount`, `status`, `payment_status`, `notes`, `created_at`) VALUES
(1, 1, 1, '2025-01-20', 150.00, 'pending', 'unpaid', 'Customer requested quick repair', NOW()),
(1, 2, 2, '2025-01-22', 300.00, 'confirmed', 'paid', 'Custom truck cover order', NOW()),
(2, 3, 3, '2025-01-25', 80.00, 'pending', 'unpaid', 'Mattress cover for queen size', NOW());

-- Create logs directory structure (this will be handled by PHP)
-- The logs directory will be created automatically when the first email is sent
