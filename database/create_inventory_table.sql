-- Create Inventory Table for Colors/Fabric
-- This table stores color inventory with premium/standard types and prices

USE db_upholcare;

CREATE TABLE IF NOT EXISTS `inventory` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `color_code` VARCHAR(50) NOT NULL,
    `color_name` VARCHAR(100) NOT NULL,
    `color_hex` VARCHAR(7) DEFAULT '#000000',
    `fabric_type` ENUM('premium', 'standard') DEFAULT 'standard',
    `price_per_unit` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Price per unit (roll/meter)',
    `premium_price` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Additional price for premium type',
    `quantity` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Available quantity in rolls',
    `store_location_id` INT(11) DEFAULT NULL COMMENT 'Which store/shop has this inventory',
    `status` ENUM('in-stock', 'low-stock', 'out-of-stock') DEFAULT 'in-stock',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `unique_color_store` (`color_code`, `store_location_id`),
    KEY `idx_store_location` (`store_location_id`),
    KEY `idx_fabric_type` (`fabric_type`),
    KEY `idx_status` (`status`),
    FOREIGN KEY (`store_location_id`) REFERENCES `store_locations`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add color selection fields to bookings table
ALTER TABLE `bookings` 
ADD COLUMN IF NOT EXISTS `selected_color_id` INT(11) DEFAULT NULL AFTER `service_id`,
ADD COLUMN IF NOT EXISTS `color_type` ENUM('premium', 'standard') DEFAULT 'standard' AFTER `selected_color_id`,
ADD COLUMN IF NOT EXISTS `color_price` DECIMAL(10,2) DEFAULT 0.00 AFTER `color_type`,
ADD KEY `idx_selected_color` (`selected_color_id`),
ADD FOREIGN KEY (`selected_color_id`) REFERENCES `inventory`(`id`) ON DELETE SET NULL;

-- Add preview fields for admin to send to customer
ALTER TABLE `bookings`
ADD COLUMN IF NOT EXISTS `preview_image` VARCHAR(255) DEFAULT NULL AFTER `color_price`,
ADD COLUMN IF NOT EXISTS `preview_sent_at` TIMESTAMP NULL DEFAULT NULL AFTER `preview_image`,
ADD COLUMN IF NOT EXISTS `preview_notes` TEXT DEFAULT NULL AFTER `preview_sent_at`;

