-- Migration for Logistics Approval Logic
-- Created: 2026-02-22

-- 1. Create Logistics Schedule Table
CREATE TABLE IF NOT EXISTS `logistics_schedule` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `booking_id` INT NOT NULL,
    `logistic_date` DATE NOT NULL,
    `type` ENUM('pickup', 'delivery') NOT NULL,
    `status` ENUM('scheduled', 'completed', 'cancelled') DEFAULT 'scheduled',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`booking_id`) REFERENCES `bookings`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Add requires_transport to services
-- Most services require transport if the customer picks a logistics option
-- But some might be "onsite" only (if added later)
ALTER TABLE `services` ADD COLUMN `requires_transport` TINYINT(1) DEFAULT 1;

-- 3. Ensure we have some default requires_transport values
UPDATE `services` SET `requires_transport` = 1;
