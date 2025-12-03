-- Add Motor Seat service to the services table
-- This service will be available under Vehicle Upholstery category

USE db_upholcare;

-- Insert Motor Seat service
INSERT INTO `services` (`service_name`, `service_type`, `description`, `price`, `status`, `created_at`)
VALUES (
    'Motor Seat',
    'Vehicle Upholstery',
    'Repair and restore motorcycle seats',
    '120.00',
    'active',
    NOW()
);

-- If services table has category_id column, link it to Vehicle Upholstery category
-- First, get the Vehicle Upholstery category ID
SET @vehicle_category_id = (SELECT id FROM service_categories WHERE category_name = 'Vehicle Upholstery' LIMIT 1);

-- Update the service with category_id if it exists
UPDATE `services` 
SET `category_id` = @vehicle_category_id 
WHERE `service_name` = 'Motor Seat' 
  AND `service_type` = 'Vehicle Upholstery'
  AND EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = 'db_upholcare' AND TABLE_NAME = 'services' AND COLUMN_NAME = 'category_id');

